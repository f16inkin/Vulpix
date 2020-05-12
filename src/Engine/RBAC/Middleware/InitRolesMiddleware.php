<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Database\Connectors\IConnector;
use Vulpix\Engine\RBAC\Domains\RoleManager;

/**
 * Class InitRolesMiddleware
 * @package Vulpix\Engine\RBAC\Middleware
 */
class InitRolesMiddleware implements MiddlewareInterface
{
    private $_dbConnector;
    private $_manager;

    /**
     * InitRolesMiddleware constructor.
     * @param IConnector $dbConnector
     * @param RoleManager $manager
     */
    public function __construct(IConnector $dbConnector, RoleManager $manager)
    {
        $this->_dbConnector = $dbConnector;
        $this->_manager = $manager;
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
        $userId = $request->getAttribute('User')['userId'];
        $roles = $this->_manager->initRoles($userId);
        $request = $request->withAttribute('Roles', $roles);
        return $response = $handler->handle($request);
    }
}