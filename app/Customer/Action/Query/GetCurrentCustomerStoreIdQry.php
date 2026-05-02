<?php

declare(strict_types=1);

namespace Module\Customer\Action\Query;

use Illuminate\Http\Request;
use Module\Customer\Support\CustomerBag;
use Module\Shared\Exception\AppNeverException;

class GetCurrentCustomerStoreIdQry
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function handle(): int
    {
        /** @var CustomerBag|null $bag */
        $bag = $this->request->attributes->get('customerBag');

        if (! $bag) {
            throw new AppNeverException('Customer is not authenticated.');
        }

        return $bag->selectedStoreId;
    }
}
