<?php

declare(strict_types=1);

namespace Module\Offer\Access\Controller;

use Abrha\LaravelDataDocs\Attributes\ResponseData;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\UrlParam;
use Module\Offer\Action\Command\ChangeVisibilityCmd;
use Module\Offer\Action\Dto\Admin\ChangeVisibilityDto;
use Module\Offer\Action\Dto\Admin\FiltersResponseDto;
use Module\Offer\Action\Dto\Admin\ListParamDto;
use Module\Offer\Action\Dto\Admin\ListResponseDto;
use Module\Offer\Action\Dto\Admin\OfferDto;
use Module\Offer\Action\Query\AdminGetListFiltersQry;
use Module\Offer\Action\Query\AdminGetOfferDetailsQry;
use Module\Offer\Action\Query\AdminGetOfferListQry;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use Throwable;

#[Group('CMS')]
class OfferAdminController
{
    #[Endpoint('Filters for offer list')]
    #[ResponseDoc(FiltersResponseDto::class, status: 200), ResponseData(FiltersResponseDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function filters(AdminGetListFiltersQry $action): FiltersResponseDto
    {
        return $action->handle();
    }

    #[Endpoint('List offers', 'Show list of offers. With selection by store or product name.')]
    #[ResponseDoc(ListResponseDto::class, status: 200), ResponseData(ListResponseDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function list(ListParamDto $dto, AdminGetOfferListQry $action): ListResponseDto
    {
        return $action->handle($dto);
    }

    #[Endpoint('Show offer', 'Show details of specific offer.')]
    #[UrlParam(name: 'id', type: 'integer', description: 'Offer ID', required: true)]
    #[ResponseDoc(OfferDto::class, status: 200), ResponseData(OfferDto::class)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(int $id, AdminGetOfferDetailsQry $action): OfferDto
    {
        return $action->handle($id);
    }

    /**
     * @throws Throwable
     */
    #[Endpoint('Change visibility', 'The endpoint allows to change the visibility of offer.')]
    #[Response('200 HTTP code on succes', 200)]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function changeVisibility(ChangeVisibilityDto $dto, ChangeVisibilityCmd $action): void
    {
        $action->handle($dto);
    }
}
