<?php

namespace LunoxHoshizaki\Middleware;

use LunoxHoshizaki\Http\Request;
use LunoxHoshizaki\Http\Response;
use Closure;

interface MiddlewareInterface
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response;
}
