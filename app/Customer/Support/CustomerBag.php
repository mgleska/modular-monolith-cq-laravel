<?php

declare(strict_types=1);

namespace Module\Customer\Support;

class CustomerBag
{
    public function __construct(
        public readonly int $customerId,
        public readonly int $selectedStoreId,
    ) {}
}
