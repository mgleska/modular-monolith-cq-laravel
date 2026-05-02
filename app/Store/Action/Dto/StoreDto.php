<?php

declare(strict_types=1);

namespace Module\Store\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Data;

class StoreDto extends Data
{
    public function __construct(
        #[Example(5)]
        public readonly int $id,
        #[Example('r002')]
        public readonly string $externalId,
        #[Example('Luboń')]
        public readonly string $name,
        #[Example('ul. Poznańska 1')]
        public readonly string $address,
    ) {}
}
