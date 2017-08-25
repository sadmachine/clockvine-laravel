<?php

namespace Imarc\clockvine\Http;

use Symfony\Component\HttpFoundation\Response;
use Imarc\clockvine\Http\ApiResponse;
use Closure;

class apiResponses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $original = $response->getOriginalContent();

        if ($original instanceof Response || is_string($original)) {
            return $response;
        }

        $new_response = new ApiResponse(
            '',
            $response->getStatusCode(),
            $response->headers->all()
        );

        if ($response->getStatusCode() > 399) {
            $new_response->addError($original);
        } else {
            $new_response->setContent($original);
        }

        return $new_response;
    }
}
