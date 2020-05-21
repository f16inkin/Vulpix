<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;
use Vulpix\Engine\RBAC\Domains\Roles\RolesCollection;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;

/**
 * Full RBAC Control Middleware.
 * Совмещает в себе поиск и валидацию привелегий.
 *
 * Class RBACMiddleware
 * @package Vulpix\Engine\RBAC\Middleware
 */
class RBACMiddleware implements MiddlewareInterface
{
    private RoleManager $_roleManager;
    private PermissionManager $_permissionManager;

    /**
     * RBACMiddleware constructor.
     * @param RoleManager $roleManager
     * @param PermissionManager $permissionManager
     */
    public function __construct(RoleManager $roleManager, PermissionManager $permissionManager)
    {
        $this->_roleManager = $roleManager;
        $this->_permissionManager = $permissionManager;
    }

    /**
     * Склеивает stack правила дял модулей фреймворка и custom правила из папки configs приложения если фреймворк
     * используется как package
     *
     * @return array
     */
    private function compareRules() : array
    {
        $configs = realpath(__DIR__ . '/../../../../configs/permissions.php');
        $stackRules = include $configs;
        $customRules = include 'configs/permissions.php';
        return array_merge($stackRules, $customRules);
    }

    /**
     * @param ServerRequestInterface $request
     * @return RolesCollection
     */
    private function initRoles(ServerRequestInterface $request) : RolesCollection{
        $userId = $request->getAttribute('User')['userId'];
        $collection = $this->_roleManager->getByUserId($userId);
        /**
         * Проинициализирую роли привелегиями
         */
        foreach ($collection as $key => $role){
            $permissions = $this->_permissionManager->initPermissions($role->getId());
            $role->setPermissions($permissions);
        }
        return $collection;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed|string
     */
    private function getRules(ServerRequestInterface $request) {
        $rules = $this->compareRules();
        $currentRule = $rules[$request->getAttribute('Action')];
        return $currentRule ?? 'FULL_ACCESS';
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
        $roles = $this->initRoles($request);
        $rules = $this->getRules($request);
        if ($rules === 'FULL_ACCESS' OR PermissionVerificator::verify($roles, $rules['permission'])){
            /**
             * Роли необходимы и могут понадобится для частных проверок далее в контролерах.
             */
            $request = $request->withAttribute('Roles', $roles);
            return $response = $handler->handle($request);
        }
        return new JsonResponse($rules['deny_message'], 403);
    }
}