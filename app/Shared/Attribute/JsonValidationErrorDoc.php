<?php
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Attribute;
use Knuckles\Scribe\Attributes\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Attribute(Attribute::TARGET_METHOD)]
class JsonValidationErrorDoc extends Response
{
    public function __construct()
    {
        parent::__construct(<<<'JSON'
{
    "message": "Error message for field1. (and 1 more error)",
    "errors": {
        "field1": ["Example error message for field1."],
        "field2": ["Example error message for field2."]
    }
}
JSON, HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
