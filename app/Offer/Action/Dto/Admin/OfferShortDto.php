<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Data;

class OfferShortDto extends Data
{
    public function __construct(
        #[Example(45)]
        public readonly int $id,
        #[Example('Luboń')]
        public readonly string $storeName,
        #[Example(true)]
        public readonly bool $visible,
        #[Example('8410564006257')]
        public readonly string $productEan,
        #[Example('Red square')]
        public readonly string $productName,
        #[Example(805)]
        public readonly int $price,
    ) {}
}
