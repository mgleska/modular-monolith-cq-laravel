<?php

declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Illuminate\Http\Request;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppNeverException;

class ValidateAccessTokenCmd
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly Request $request,
    ) {}

    public function validate(string $jwt): void
    {
        $payload = $this->tokenService->decodeAccessToken($jwt);

        if (! isset($payload['uid'])) {
            throw new AppNeverException('Missing "uid" in JWT token');
        }

        $bag = new CustomerBag(
            customerId: $payload['uid'],
            selectedStoreId: $payload['stid'] ?? 0,
        );
        $this->request->attributes->set('customerBag', $bag);
    }
}
