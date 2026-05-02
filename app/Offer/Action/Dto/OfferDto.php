<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Module\Offer\Action\Enum\QuantityLevelEnum;
use Spatie\LaravelData\Data;

class OfferDto extends Data
{
    public function __construct(
        #[Example(45)]
        public readonly int $id,
        #[Example('8410564006257')]
        public readonly string $productEan,
        #[Example('Red square')]
        public readonly string $productName,
        #[Example(805)]
        public readonly int $price,
        #[Example(950)]
        public readonly ?int $lowestPrice,
        #[Example('https://my.images.com/red-square.png')]
        public readonly string $imageUrl,
        #[Example(QuantityLevelEnum::AVAILABLE->value)]
        public readonly QuantityLevelEnum $quantityLevel,
    ) {}
}
