<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\OfferDto;
use Module\Offer\Action\Enum\QuantityLevelEnum;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\GetProductDetailsQry;
use Module\Shared\Exception\AppValidationException;

class GetOfferDetailsQry
{
    public function __construct(
        private readonly GetCurrentCustomerStoreIdQry $getCurrentCustomerStoreIdQry,
        private readonly GetProductDetailsQry $getProductDetailsQry,
    ) {}

    public function handle(int $id): OfferDto
    {
        $storeId = $this->getCurrentCustomerStoreIdQry->handle();

        $offer = Offer::query()->findOrFail($id);

        if ($offer->store_id !== $storeId) {
            throw AppValidationException::withMessages(['offerId' => 'Requested offer belongs to other store.']);
        }
        if ($offer->visible !== true) {
            throw AppValidationException::withMessages(['visible' => 'Requested offer is not visible.']);
        }
        if ($offer->product_id === null) {
            throw AppValidationException::withMessages(['product_id' => 'Requested offer is for unknown product.']);
        }

        $product = $this->getProductDetailsQry->handle($offer->product_id, $storeId);
        $quantityLevel = match (true) {
            $product->quantity >= 5000 => QuantityLevelEnum::AVAILABLE,
            $product->quantity > 0 => QuantityLevelEnum::AVAILABLE_LOW,
            default => QuantityLevelEnum::UNKNOWN
        };

        return new OfferDto(
            id: $offer->id,
            productEan: $offer->product_ean,
            productName: $offer->product_name ?? $product->name,
            price: $offer->price,
            lowestPrice: $offer->lowest_price,
            imageUrl: $product->imageUrl,
            quantityLevel: $quantityLevel,
        );
    }
}
