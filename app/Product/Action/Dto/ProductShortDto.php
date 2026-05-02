<?php

declare(strict_types=1);

namespace Module\Product\Action\Dto;

use Spatie\LaravelData\Data;

class ProductShortDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $ean,
    ) {}
}
