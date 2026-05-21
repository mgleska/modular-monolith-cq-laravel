<?php

declare(strict_types=1);

namespace Module\Product\Action\Command;

use Module\Product\Model\Product;
use Module\Product\Model\ProductQuantity;
use Module\Store\Action\Query\GetStoreListQry;
use Throwable;

/** @codeCoverageIgnore */
class ImportQuantityCmd
{
    public function __construct(
        private readonly GetStoreListQry $getStoreListQry,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        // Fake import from external API

        $stores = $this->getStoreListQry->handle();
        $ids = Product::query()
            ->pluck('id')
            ->toArray();

        $insert = [];
        foreach ($stores as $store) {
            foreach ($ids as $id) {
                $quantity = rand(0, 20);
                if ($quantity == 0) {
                    continue;
                }
                $insert[] = ['store_id' => $store->id, 'product_id' => $id, 'quantity' => $quantity * 1000];
            }
        }

        ProductQuantity::truncate();
        ProductQuantity::insert($insert);
    }
}
