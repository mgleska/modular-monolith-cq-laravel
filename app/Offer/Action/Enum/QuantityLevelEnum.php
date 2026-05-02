<?php

declare(strict_types=1);

namespace Module\Offer\Action\Enum;

enum QuantityLevelEnum: string
{
    case UNKNOWN = 'unknown';
    case AVAILABLE = 'available';
    case AVAILABLE_LOW = 'available_low';
}
