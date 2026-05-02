<?php

declare(strict_types=1);

namespace Module\Shared\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Module\Customer\Action\Command\ValidateAccessTokenCmd;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = $request->bearerToken();

        if ($jwt === null) {
            throw new AuthenticationException;
        }

        try {
            resolve(ValidateAccessTokenCmd::class)->validate($jwt);
        }
        catch (Exception $e) {
            report($e);
            throw new AuthenticationException;
        }

        return $next($request);
    }
}
