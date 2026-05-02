<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Data;

class ChangeVisibilityDto extends Data
{
    public function __construct(
        #[IntegerType, Example(45)]
        public readonly int $id,
        #[IntegerType, Example(2)]
        public readonly int $version,
        #[Example(false)]
        public readonly bool $visible
    ) {}
}
