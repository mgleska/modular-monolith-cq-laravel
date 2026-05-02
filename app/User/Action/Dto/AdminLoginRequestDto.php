<?php

declare(strict_types=1);

namespace Module\User\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Spatie\LaravelData\Data;

class AdminLoginRequestDto extends Data
{
    public function __construct(
        #[Example('user@my.company.com')]
        public string $email,
    ) {}
}
