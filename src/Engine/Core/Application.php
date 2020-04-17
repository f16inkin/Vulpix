<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Core;


use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Laminas\Stratigility\MiddlewarePipe;
use function Laminas\Stratigility\path;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Core\Infrastructure\MiddlewareFactory;
use Vulpix\Engine\Core\Infrastructure\Exceptions\UnknownMiddlewareException;

/**
 * Class Application
 * @package Vulpix\Engine\Core
 */
class Application implements RequestHandlerInterface
{
    private $_factory;
    private $_routerMap;
    private $_pipeline;
    private $_defaultHandler;

    /**
     * Application constructor.
     * @param MiddlewareFactory $factory
     * @param RouterContainer $router
     * @param MiddlewarePipe $pipeline
     * @param RequestHandlerInterface $defaultHandler
     */
    public function __construct(MiddlewareFactory $factory, RouterContainer $router, MiddlewarePipe $pipeline, RequestHandlerInterface $defaultHandler)
    {
        $this->_factory = $factory;
        $this->_routerMap = $router->getMap();
        $this->_pipeline = $pipeline;
        $this->_defaultHandler = $defaultHandler;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $handler
     * @param string $method
     * @return Route
     */
    public function route(string $name, string $path, string $handler, string $method) : Route
    {
        return $this->_routerMap->route($name, $path, $handler)->allows($method);
    }

    public function get(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->get($name, $path, $handler);
    }

    public function post(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->post($name, $path, $handler);
    }

    public function put(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->put($name, $path, $handler);
    }

    public function patch(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->patch($name, $path, $handler);
    }

    public function delete(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->delete($name, $path, $handler);
    }

    public function options(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->options($name, $path, $handler);
    }

    public function head(string $name, string $path, string $handler) : Route {
        return $this->_routerMap->head($name, $path, $handler);
    }

    /**
     * Загрузка middleware в pipeline
     *
     * @param $path
     * @param null $middleware
     */
    public function pipe($path, $middleware = null) : void {
        try{
            if ($middleware === null){
                $this->_pipeline->pipe($this->_factory->create($path));
            }else{
                $this->_pipeline->pipe(path($path, $this->_factory->create($middleware)));
            }
        }catch (UnknownMiddlewareException $e){
            //Здесь сделать логирование например того мидлвара который работает не верно
        }

    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->_pipeline->process($request, $this->_defaultHandler);
    }

}