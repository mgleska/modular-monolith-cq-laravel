<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Action\Dto\StoreShortDto;
use Module\Store\Model\Store;

class GetStoreByExternalIdQry
{
    public function handle(string $extId): ?StoreShortDto
    {
        $store = Store::query()->where('external_id', $extId)->first();

        if ($store === null) {
            return null;
        }

        return new StoreShortDto(
            id: $store->id,
            externalId: $store->external_id,
            name: $store->name,
        );
    }
}
