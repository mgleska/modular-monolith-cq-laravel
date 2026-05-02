<?php

declare(strict_types=1);

namespace Module\User\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SanctumTokenResponseDto extends Data
{
    public function __construct(
        #[Example('Laravel Sanctum token')]
        public readonly string $token,
    ) {}

    protected function calculateResponseStatus(Request $request): int
    {
        return HttpResponse::HTTP_OK;
    }
}
