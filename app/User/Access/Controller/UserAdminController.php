<?php

declare(strict_types=1);

namespace Module\User\Access\Controller;

use Abrha\LaravelDataDocs\Attributes\ResponseData;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\User\Action\Command\AdminLoginCmd;
use Module\User\Action\Dto\AdminLoginRequestDto;
use Module\User\Action\Dto\SanctumTokenResponseDto;

#[Group('CMS')]
class UserAdminController
{
    #[Endpoint('ONYLY FOR DEMO: Login CMS', 'Enpoint returns token for supplied CMS user (email). If given user does not exist in database - will be created.')]
    #[Unauthenticated]
    #[ResponseDoc(SanctumTokenResponseDto::class), ResponseData(SanctumTokenResponseDto::class)]
    #[JsonValidationErrorDoc]
    public function login(AdminLoginRequestDto $dto, AdminLoginCmd $action): SanctumTokenResponseDto
    {
        return $action->handle($dto);
    }
}
