<?php

declare(strict_types = 1);

namespace Vulpix\Engine\Infrastructure;


use Aura\Router\RouterContainer;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Container\ContainerInterface;
use Vulpix\Application\PatientCard\Card\Actions\EmptyRouteAction;
use Vulpix\Engine\Core\Application;

/**
 * Class ApplicationFactory
 * @package Vulpix\Engine\Infrastructure
 */
class ApplicationFactory
{
    /**
     * @param ContainerInterface $container
     * @return Application
     */
    public function __invoke(ContainerInterface $container) : Application
    {
        return new Application(
            $container->get(MiddlewareFactory::class),
            $container->get(RouterContainer::class),
            $container->get(MiddlewarePipe::class),
            $container->get(EmptyRouteAction::class)
        );
    }

    /**
     * @param ContainerInterface $container
     * @return Application
     */
    public static function create(ContainerInterface $container) : Application{
        return new Application(
            $container->get(MiddlewareFactory::class),
            $container->get(RouterContainer::class),
            $container->get(MiddlewarePipe::class),
            $container->get(EmptyRouteAction::class)
        );
    }

}