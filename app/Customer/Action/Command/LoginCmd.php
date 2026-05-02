<?php

declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Customer\Action\Enum\CustomerStatusEnum;
use Module\Customer\Model\Customer;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppValidationException;

class LoginCmd
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function handle(LoginRequestDto $dto): TokenResponseDto
    {
        $customer = Customer::query()
            ->find($dto->customerId);

        if (! $customer) {
            $customer = Customer::forceCreate(['id' => $dto->customerId, 'name' => 'Customer ' . $dto->customerId, 'selected_store' => 0]);
        }

        if ($customer->status === CustomerStatusEnum::INACTIVE) {
            throw AppValidationException::withMessages(['customerId' => 'Customer is inactive.']);
        }

        if ($customer->status === CustomerStatusEnum::DEACTIVATING) {
            $customer->status = CustomerStatusEnum::ACTIVE;
            $customer->save();
        }

        $jwt = $this->tokenService->newAccessToken($dto->customerId, $customer->selected_store);

        return new TokenResponseDto(token: $jwt);
    }
}
