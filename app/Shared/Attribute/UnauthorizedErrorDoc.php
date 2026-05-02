<?php
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Attribute;
use Knuckles\Scribe\Attributes\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Attribute(Attribute::TARGET_METHOD)]
class UnauthorizedErrorDoc extends Response
{
    public function __construct()
    {
        parent::__construct('{"message": "Unauthenticated."}', HttpResponse::HTTP_UNAUTHORIZED);
    }
}
