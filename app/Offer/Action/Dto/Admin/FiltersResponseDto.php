<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Spatie\LaravelData\Data;

class FiltersResponseDto extends Data
{
    public function __construct(
        /** @var StoreDto[] */
        public readonly array $stores
    ) {}
}
