<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Data;

class StoreDto extends Data
{
    public function __construct(
        #[Example(5)]
        public readonly int $storeId,
        #[Example('Luboń')]
        public readonly string $storeName,
    ) {}
}
