<?php

declare(strict_types=1);

namespace Module\User\Action\Command;

use Module\User\Action\Dto\AdminLoginRequestDto;
use Module\User\Action\Dto\SanctumTokenResponseDto;
use Module\User\Model\User;

class AdminLoginCmd
{
    public function handle(AdminLoginRequestDto $dto): SanctumTokenResponseDto
    {
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (! $user) {
            $user = User::create(['email' => $dto->email, 'name' => $dto->email, 'password' => '!']);
            $user->refresh();
        }

        return new SanctumTokenResponseDto($user->createToken('apiToken')->plainTextToken);
    }
}
