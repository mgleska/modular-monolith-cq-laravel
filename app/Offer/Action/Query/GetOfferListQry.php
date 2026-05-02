<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Illuminate\Support\Facades\DB;
use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\ListResponseDto;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\JoinProductByIdSqlQry;

class GetOfferListQry
{
    public function __construct(
        private readonly GetCurrentCustomerStoreIdQry $getCurrentCustomerStoreIdQry,
        private readonly JoinProductByIdSqlQry $joinProductByIdSqlQry,
    ) {}

    public function handle(ListParamDto $dto): ListResponseDto
    {
        $storeId = $this->getCurrentCustomerStoreIdQry->handle();

        $joinProduct = $this->joinProductByIdSqlQry->joinById(Offer::TABLE . '.product_id');
        $joinProduct->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $query = DB::table(Offer::TABLE)
            ->leftJoin($joinProduct->joinTable, $joinProduct->joinClause)
            ->where('store_id', $storeId)
            ->where('visible', true)
            ->select([
                Offer::TABLE . '.id',
                Offer::TABLE . '.product_ean AS productEan',
                DB::raw('COALESCE(' . Offer::TABLE . '.product_name' . ", $joinProduct->table.name) AS productName"),
                Offer::TABLE . '.price',
                Offer::TABLE . '.lowest_price AS lowestPrice',
            ]);

        $page = max((int)$dto->page, 1);
        $paginator = $query->simplePaginate(perPage: 5, page: $page);

        return ListResponseDto::fromPaginator($paginator);
    }
}
