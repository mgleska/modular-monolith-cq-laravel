<?php

declare(strict_types=1);

namespace Module\Customer\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Data;

class LoginRequestDto extends Data
{
    public function __construct(
        #[IntegerType, Example(15)]
        public readonly int $customerId,
    ) {}
}
