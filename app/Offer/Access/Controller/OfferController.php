<?php

declare(strict_types=1);

namespace Module\Offer\Access\Controller;

use Abrha\LaravelDataDocs\Attributes\ResponseData;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\UrlParam;
use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\ListResponseDto;
use Module\Offer\Action\Dto\OfferDto;
use Module\Offer\Action\Query\GetOfferDetailsQry;
use Module\Offer\Action\Query\GetOfferListQry;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;

#[Group('mobile API')]
class OfferController
{
    #[Endpoint('List offers', 'Show list of offers in selected store.')]
    #[ResponseDoc(ListResponseDto::class), ResponseData(ListResponseDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function list(ListParamDto $dto, GetOfferListQry $action): ListResponseDto
    {
        return $action->handle($dto);
    }

    #[Endpoint('Show offer', 'Show details of specific offer.')]
    #[UrlParam(name: 'id', type: 'integer', required: true)]
    #[ResponseDoc(OfferDto::class), ResponseData(OfferDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(int $id, GetOfferDetailsQry $action): OfferDto
    {
        return $action->handle($id);
    }
}
