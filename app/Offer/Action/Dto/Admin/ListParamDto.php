<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ListParamDto extends Data
{
    public function __construct(
        #[Example('')]
        public readonly ?string $search,
        #[IntegerType, Min(1), Example(5)]
        public readonly ?int $storeId,
        #[IntegerType, Min(1), Example(1)]
        public readonly ?int $page,
        #[IntegerType, Min(1), Example(10)]
        public readonly ?int $perPage,
    ) {}
}
