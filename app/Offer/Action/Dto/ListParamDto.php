<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ListParamDto extends Data
{
    public function __construct(
        #[IntegerType, Min(1), Example(2)]
        public readonly ?int $page,
    ) {}
}
