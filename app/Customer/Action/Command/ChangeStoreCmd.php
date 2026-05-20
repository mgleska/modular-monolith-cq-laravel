<?php
declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Illuminate\Http\Request;
use Module\Customer\Action\Dto\ChangeStoreRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Customer\Model\Customer;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Query\CheckStoreExistsQry;

class ChangeStoreCmd
{
    public function __construct(
        private readonly CheckStoreExistsQry $checkStoreExistsQry,
        private readonly Request $request,
        private readonly TokenService $tokenService,
    ) {}

    public function handle(ChangeStoreRequestDto $dto): TokenResponseDto
    {
        if (! $this->checkStoreExistsQry->check($dto->storeId)) {
            throw AppValidationException::withMessages(['storeId' => 'Invalid store ID.']);
        }

        /** @var CustomerBag|null $bag */
        $bag = $this->request->attributes->get('customerBag');
        if (! $bag) {
            throw new AppNeverException('Customer is not authenticated.');
        }

        $count = Customer::query()
            ->where('id', $bag->customerId)
            ->update(['selected_store' => $dto->storeId]);

        if ($count === 0) {
            throw new AppNeverException('Customer not found.');
        }

        $jwt = $this->tokenService->newAccessToken($bag->customerId, $dto->storeId);

        return new TokenResponseDto(token: $jwt);
    }
}
