<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Permissions\PermissionManager;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\PermissionsDeleteResponder;

/**
 * Удалить привелегии у выбранной роли.
 *
 * Class PermissionsDeleteAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class PermissionsDeleteAction implements RequestHandlerInterface
{
    private $_manager;
    private $_responder;

    /**
     * PermissionsDeleteAction constructor.
     * @param PermissionManager $manager
     * @param PermissionsDeleteResponder $responder
     */
    public function __construct(PermissionManager $manager, PermissionsDeleteResponder $responder)
    {
        $this->_manager = $manager;
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
            $deleteData = json_decode(file_get_contents("php://input"),true) ?: null;
            $roleId = (int)$deleteData['roleId'] ?: null;
            $deletingPermissionsIDs = $deleteData['permissionIDs'];
            $result = $this->_manager->deletePermissions($roleId, $deletingPermissionsIDs);
            return $this->_responder->respond($request, $result);

        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
