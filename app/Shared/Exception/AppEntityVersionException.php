<?php
declare(strict_types=1);

namespace Module\Shared\Exception;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class AppEntityVersionException extends HttpException
{
    /**
     * @param  array<string,string>  $headers
     */
    public function __construct(
        string $message = 'Entity version in database is different than version received in API call.',
        ?Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'versionMismatch' => $this->getMessage(),
        ], $this->getStatusCode());
    }
}
