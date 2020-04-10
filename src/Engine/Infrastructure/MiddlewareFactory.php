<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Infrastructure;


use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Vulpix\Engine\Middleware\Exceptions\UnknownMiddlewareException;

/**
 * Class MiddlewareFactory
 * @package Vulpix\Engine\Infrastructure
 */
class MiddlewareFactory
{
    private $_container;

    /**
     * MiddlewareFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string|null $handler
     * @return MiddlewareInterface
     * @throws UnknownMiddlewareException
     */
    public function create(?string $handler) : MiddlewareInterface {
        if ($this->_container->has($handler)){
            return $this->_container->get($handler);
        }
        throw new UnknownMiddlewareException('Данный Middleware не зарегистрирован '.$handler);
    }

}