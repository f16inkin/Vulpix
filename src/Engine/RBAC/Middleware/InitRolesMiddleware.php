<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\Database\Connectors\IConnector;
use Vulpix\Engine\Database\Connectors\MySQLConnector;
use Vulpix\Engine\RBAC\Domains\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Roles\Role;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;

/**
 * Class InitRolesMiddleware
 * @package Vulpix\Engine\RBAC\Middleware
 */
class InitRolesMiddleware implements MiddlewareInterface
{
    private $_dbConnector;
    private $_roleManager;
    private $_permissionManager;

    /**
     * InitRolesMiddleware constructor.
     * @param IConnector $dbConnector
     * @param RoleManager $manager
     */
    public function __construct(IConnector $dbConnector, RoleManager $roleManager, PermissionManager $permissionManager)
    {
        $this->_dbConnector = $dbConnector;
        $this->_roleManager = $roleManager;
        $this->_permissionManager = $permissionManager;
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
        $collection = $this->_roleManager->getByUserId($userId);
        /**
         * Проинициализирую роли привелегиями
         */
        foreach ($collection as $key => $role){
            $permissions = $this->_permissionManager->initPermissions($role->getId());
            $role->setPermissions($permissions);
        }
        $request = $request->withAttribute('Roles', $collection);
        return $response = $handler->handle($request);
    }
}