<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\PermissionManager;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Exceptions\AddPermissionException;
use Vulpix\Engine\RBAC\Responders\AddPermissionsResponder;

/**
 * Class AddPermissionsAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class AddPermissionsAction implements RequestHandlerInterface
{
    private $_manager;
    private $_role;
    private $_responder;

    /**
     * AddPermissionsAction constructor.
     * @param PermissionManager $manager
     * @param Role $role
     * @param AddPermissionsResponder $responder
     */
    public function __construct(PermissionManager $manager, Role $role, AddPermissionsResponder $responder)
    {
        $this->_manager = $manager;
        $this->_role = $role;
        $this->_responder = $responder;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try{
            $postData = json_decode(file_get_contents("php://input"),true);
            $roleId = (int)$postData['roleId'];
            //Добавляемые привелегии
            $addingPermissionsIDs = $postData['permissionIDs'];
            //Найденные привелегии для текущей роли
            $foundPermissionIDs = $this->_manager->findRolePermissionIDs($roleId, $addingPermissionsIDs);
            //Те привелегии которые добавляются, которых нет в имеющихся
            $permissionsIDs = array_diff($addingPermissionsIDs, $foundPermissionIDs);
            $roleId = $this->_manager->addPermissions($roleId, $permissionsIDs);
            //Полная ифнормация по роли
            $role = $this->_role->read($roleId);
            $response = $this->_responder->respond($request, $role);
            return $response;
        }catch (AddPermissionException $e){
            return new JsonResponse(['Ошибка работы БД' => $e->getMessage()], 500);
        }
    }
}
