<?php
declare(strict_types=1);

namespace Module\Shared\Exception;

use Illuminate\Validation\ValidationException;

class AppValidationException extends ValidationException
{
    /**
     * Create a new validation exception from a plain array of messages.
     *
     * @param  array<int|string, mixed>  $messages
     * @return static
     */
    public static function withMessages(array $messages): self
    {
        return parent::withMessages($messages);
    }
}
