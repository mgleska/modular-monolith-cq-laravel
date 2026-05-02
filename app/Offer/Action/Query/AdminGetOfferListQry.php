<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Module\Offer\Action\Dto\Admin\ListParamDto;
use Module\Offer\Action\Dto\Admin\ListResponseDto;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\JoinProductByIdSqlQry;
use Module\Store\Action\Query\JoinStoreByIdSqlQry;

class AdminGetOfferListQry
{
    public function __construct(
        private readonly JoinProductByIdSqlQry $joinProductByIdSqlQry,
        private readonly JoinStoreByIdSqlQry $joinStoreByIdSqlQry,
    ) {}

    public function handle(ListParamDto $dto): ListResponseDto
    {
        $joinProduct = $this->joinProductByIdSqlQry->joinById(Offer::TABLE . '.product_id');
        $joinProduct->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $joinStore = $this->joinStoreByIdSqlQry->joinById(Offer::TABLE . '.store_id');
        $joinStore->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $query = DB::table(Offer::TABLE)
            ->leftJoin($joinProduct->joinTable, $joinProduct->joinClause)
            ->leftJoin($joinStore->joinTable, $joinStore->joinClause)
            ->select([
                Offer::TABLE . '.id',
                $joinStore->column('name', 'storeName'),
                Offer::TABLE . '.visible',
                Offer::TABLE . '.product_ean AS productEan',
                DB::raw('COALESCE(' . Offer::TABLE . '.product_name' . ", $joinProduct->table.name) AS productName"),
                Offer::TABLE . '.price',
            ])
            ->orderBy($joinStore->table . '.name')
            ->orderBy($joinProduct->table . '.name');

        if ($dto->storeId) {
            $query->where(Offer::TABLE . '.store_id', $dto->storeId);
        }

        if ($dto->search) {
            $query->where(function (Builder $query) use ($dto, $joinProduct) {
                $query->orWhere(DB::raw('COALESCE(' . Offer::TABLE . '.product_name' . ", $joinProduct->table.name)"), 'like', '%' . $dto->search . '%');
                $query->orWhere(Offer::TABLE . '.product_ean', 'like', '%' . $dto->search . '%');
            });
        }

        $page = max((int)$dto->page, 1);
        $perPage = max(($dto->perPage ?? 10), 1);
        $paginator = $query->simplePaginate(perPage: $perPage, page: $page);

        return ListResponseDto::fromPaginator($paginator);
    }
}
