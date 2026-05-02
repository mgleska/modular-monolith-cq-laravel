<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Module\Offer\Action\Dto\Admin\FiltersResponseDto;
use Module\Offer\Action\Dto\Admin\StoreDto;
use Module\Store\Action\Dto\StoreShortDto;
use Module\Store\Action\Query\GetStoreListQry;

class AdminGetListFiltersQry
{
    public function __construct(
        private readonly GetStoreListQry $getStoreListQry,
    ) {}

    public function handle(): FiltersResponseDto
    {
        $stores = array_map(
            function (StoreShortDto $store) {
                return new StoreDto(storeId: $store->id, storeName: $store->name);
            },
            $this->getStoreListQry->handle()
        );

        return new FiltersResponseDto(stores: $stores);
    }
}
