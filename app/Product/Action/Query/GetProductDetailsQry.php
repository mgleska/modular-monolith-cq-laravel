<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Module\Product\Action\Dto\ProductDetailsDto;
use Module\Product\Model\Product;
use Module\Product\Model\ProductQuantity;

class GetProductDetailsQry
{
    public function handle(int $productId, int $storeId): ProductDetailsDto
    {
        $product = Product::query()->findOrFail($productId);
        $quantity = ProductQuantity::query()
            ->where('product_id', $productId)
            ->where('store_id', $storeId)
            ->first();

        return new ProductDetailsDto(
            id: $product->id,
            ean: $product->ean,
            name: $product->name,
            imageUrl: $product->image_url,
            quantity: $quantity?->quantity,
        );
    }
}
