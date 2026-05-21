<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Module\Offer\Action\Dto\Admin\OfferDto;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\GetProductDetailsQry;

class AdminGetOfferDetailsQry
{
    public function __construct(
        private readonly GetProductDetailsQry $getProductDetailsQry,
    ) {}

    public function handle(int $id): OfferDto
    {
        $offer = Offer::query()->findOrFail($id);

        if ($offer->product_id) {
            $product = $this->getProductDetailsQry->handle($offer->product_id, $offer->store_id);
        }
        else {
            $product = null;
        }

        return new OfferDto(
            id: $offer->id,
            version: $offer->version,
            visible: $offer->visible,
            productEan: $offer->product_ean,
            productName: $offer->product_name ?? $product->name ?? '',
            price: $offer->price,
            lowestPrice: $offer->lowest_price,
            imageUrl: $product->imageUrl ?? '',
            quantity: $product?->quantity,
        );
    }
}
