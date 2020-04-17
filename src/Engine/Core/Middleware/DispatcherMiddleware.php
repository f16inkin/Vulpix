<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core\Middleware;


use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Core\Infrastructure\ActionFactory;
use Vulpix\Engine\Core\Infrastructure\Exceptions\UnknownActionException;

class DispatcherMiddleware implements MiddlewareInterface
{
    private $_factory;

    public function __construct(ActionFactory $factory)
    {
        $this->_factory = $factory;
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
        try{
            if ($action = $request->getAttribute('Action')){
                return ($this->_factory->create($action))->handle($request);
            }
            return $handler->handle($request);
        }catch (UnknownActionException $e){
            //Здесь сделать логирование например того экшена который работает не верно
            return new TextResponse($e->getMessage());
        }

    }
}