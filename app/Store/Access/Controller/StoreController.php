<?php

declare(strict_types=1);

namespace Module\Store\Access\Controller;

use Abrha\LaravelDataDocs\Attributes\ResponseData;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use Module\Store\Action\Dto\ListResponseDto;
use Module\Store\Action\Dto\StoreDto;
use Module\Store\Action\Query\GetStoreDetailsQry;
use Module\Store\Action\Query\GetStoreListQry;

#[Group('mobile API')]
class StoreController
{
    #[Endpoint('List stores', 'Show list of stores.')]
    #[ResponseDoc(ListResponseDto::class, status: 200), ResponseData(ListResponseDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function list(GetStoreListQry $action): ListResponseDto
    {
        return new ListResponseDto($action->handle());
    }

    #[Endpoint('Show store', 'Show details of specific store.')]
    #[UrlParam(name: 'id', type: 'integer', example: 25)]
    #[ResponseDoc(StoreDto::class, status: 200), ResponseData(StoreDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(int $id, GetStoreDetailsQry $action): StoreDto
    {
        return $action->handle($id);
    }
}
