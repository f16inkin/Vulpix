<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MemoryUsageMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $memoryGetUsage = memory_get_usage(true);
        $unit = array('b','kb','mb','gb','tb','pb');
        $memoryUsage = @round($memoryGetUsage/pow(1024,($i=floor(log($memoryGetUsage,1024)))),2).' '.$unit[$i];
        $memoryPeakUsage = memory_get_peak_usage();
        $response = $response->withHeader('X-Memory-Usage', $memoryUsage);
        $response = $response->withHeader('X-Memory-Peak-Usage', $memoryPeakUsage);
        return $response;
    }
}