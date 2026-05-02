<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Action\Dto\StoreDto;
use Module\Store\Model\Store;

class GetStoreDetailsQry
{
    public function handle(int $id): StoreDto
    {
        $store = Store::findOrFail($id);

        return new StoreDto(
            id: $store->id,
            externalId: $store->external_id,
            name: $store->name,
            address: $store->address,
        );
    }
}
