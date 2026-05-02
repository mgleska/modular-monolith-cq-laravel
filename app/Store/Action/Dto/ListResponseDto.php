<?php

declare(strict_types=1);

namespace Module\Store\Action\Dto;

use Spatie\LaravelData\Data;

class ListResponseDto extends Data
{
    public function __construct(
        /** @var StoreShortDto[] */
        public readonly array $items,
    ) {}
}
