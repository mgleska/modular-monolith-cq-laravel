<?php

declare(strict_types=1);

namespace Module\Customer\Action\Enum;

enum CustomerStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DEACTIVATING = 'deactivating';
}
