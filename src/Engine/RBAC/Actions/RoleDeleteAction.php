<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\RoleManager;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\RoleDeleteResponder;

/**
 * Удаляет роль / роли.
 *
 * Class RoleDeleteAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleDeleteAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'RBAC_ROLE_DELETE';

    private $_manager;
    private $_responder;

    /**
     * RoleDeleteAction constructor.
     * @param RoleManager $manager
     * @param RoleDeleteResponder $responder
     */
    public function __construct(RoleManager $manager, RoleDeleteResponder $responder)
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
            if (PermissionVerificator::verify($request->getAttribute('Roles'), self::ACCESS_PERMISSION)){
                $deleteData = json_decode(file_get_contents("php://input"),true) ?: null;
                $roleId = $deleteData['roleIDs'];
                $result = $this->_manager->delete($roleId);
                $response = $this->_responder->respond($request, $result);
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено удалять роли.', 403);
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }

    }
}
