<?php

declare(strict_types=1);

namespace Module\Customer\Action\Dto;

use Abrha\LaravelDataDocs\Attributes\Example;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TokenResponseDto extends Data
{
    public function __construct(
        #[Example('JWT access_token')]
        public readonly string $token,
    ) {}

    /** @codeCoverageIgnore */
    protected function calculateResponseStatus(Request $request): int
    {
        return HttpResponse::HTTP_OK;
    }
}
