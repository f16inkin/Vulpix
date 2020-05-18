<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\DataStructures\Collections\PermissionsCollection;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\PermissionsAddResponder;

/**
 * Добавление привелегий выбранной роли.
 *
 * Class PermissionsAddAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class PermissionsAddAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'PERMISSIONS_ADD';

    private $_manager;
    private $_roleManager;
    private $_permissions;
    private $_responder;

    /**
     * PermissionsAddAction constructor.
     * @param PermissionManager $manager
     * @param PermissionsCollection $permissions
     * @param RoleManager $roleManager
     * @param PermissionsAddResponder $responder
     */
    public function __construct(PermissionManager $manager, PermissionsCollection $permissions, RoleManager $roleManager, PermissionsAddResponder $responder)
    {
        $this->_manager = $manager;
        $this->_roleManager = $roleManager;
        $this->_permissions = $permissions;
        $this->_responder = $responder;
    }

    /**
     * По итогу исполнения вернет Роль в которую добавлялись привелегии в независимости от результата.
     * Добавились или нет.
     *
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try{
            if (PermissionVerificator::verify($request->getAttribute('Roles'), self::ACCESS_PERMISSION)){
                $postData = json_decode(file_get_contents("php://input"),true) ?: null;
                $roleId = (int)$postData['roleId'] ?: null;
                //Добавляемые привелегии
                $addingPermissionsIDs = $postData['permissionIDs'];
                $result = $this->_manager->addPermissions($roleId, $addingPermissionsIDs);
                //Полная ифнормация по роли
                $role = $this->_roleManager->getById($result->getBody());
                $permissions = $this->_manager->initPermissions($roleId, $this->_manager::GROUPED);
                //Роль с инициализированными привелегиями
                $roleWithPermissions = $role->setPermissions($permissions);
                return $this->_responder->respond($request, $result->setBody($roleWithPermissions));
            }
            return new JsonResponse('Access denied. Вам запрещено добавлять првиелегии роли.', 403);
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
