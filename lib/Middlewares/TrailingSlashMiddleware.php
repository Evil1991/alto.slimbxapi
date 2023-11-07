<?php

namespace Alto\Slimbxapi\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TrailingSlashMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            // recursively remove slashes when its more than 1 slash
            $path = rtrim($path, '/');

            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath($path);
            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}