<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Middleware;


use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterMiddleware implements MiddlewareInterface
{
    private $_aura;

    public function __construct(RouterContainer $aura)
    {
        $this->_aura = $aura;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matcher = $this->_aura->getMatcher();
        if ($route = $matcher->match($request)){
            foreach ($route->attributes as $key => $val) {
                $request = $request->withAttribute($key, $val);
            }
            $request = $request->withAttribute('Action', $route->handler);
        }
        return $handler->handle($request);
    }
}