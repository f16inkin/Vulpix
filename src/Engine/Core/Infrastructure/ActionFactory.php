<?php


namespace Vulpix\Engine\Core\Infrastructure;


use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Core\Infrastructure\Exceptions\UnknownActionException;

class ActionFactory
{
    private $_container;

    /**
     * ActionFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string|null $handler
     * @return RequestHandlerInterface
     * @throws UnknownActionException
     */
    public function create(?string $handler) : RequestHandlerInterface {
        if ($this->_container->has($handler)){
            return $this->_container->get($handler);
        }
        throw new UnknownActionException("Данный $handler не зарегистрирован.");
    }

}