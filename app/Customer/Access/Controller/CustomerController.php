<?php
declare(strict_types=1);

namespace Module\Customer\Access\Controller;

use Abrha\LaravelDataDocs\Attributes\ResponseData;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Module\Customer\Action\Command\ChangeStoreCmd;
use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Action\Dto\ChangeStoreRequestDto;
use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;

#[Group('mobile API')]
class CustomerController
{
    #[Unauthenticated]
    #[Endpoint('ONLY FOR DEMO: Login', 'Endpoint returns new JWT access token for supplied mobile user ID. If given user does not exist in database - will be created.')]
    #[ResponseDoc(TokenResponseDto::class, status: 200), ResponseData(TokenResponseDto::class)]
    #[JsonValidationErrorDoc]
    public function login(LoginRequestDto $dto, LoginCmd $action): TokenResponseDto
    {
        return $action->handle($dto);
    }

    #[Endpoint('Set store for mobile user', 'Endpoint sets given store for mobile user and returns new JWT access token with this store.')]
    #[ResponseDoc(TokenResponseDto::class, status: 200), ResponseData(TokenResponseDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function changeStore(ChangeStoreRequestDto $dto, ChangeStoreCmd $action): TokenResponseDto
    {
        return $action->handle($dto);
    }
}
