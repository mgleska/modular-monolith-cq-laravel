<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Action\Dto\StoreShortDto;
use Module\Store\Model\Store;

class GetStoreListQry
{
    /**
     * @return StoreShortDto[]
     */
    public function handle(): array
    {
        return Store::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Store $store) => new StoreShortDto($store->id, $store->external_id, $store->name))
            ->all();
    }
}
