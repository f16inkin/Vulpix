<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\RoleEditResponder;

/**
 * Редактирует роль.
 *
 * Class RoleEditAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleEditAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'RBAC_ROLE_EDIT';

    private RoleManager $_manager;
    private RoleEditResponder $_responder;

    /**
     * RoleEditAction constructor.
     * @param RoleManager $manager
     * @param RoleEditResponder $responder
     */
    public function __construct(RoleManager $manager, RoleEditResponder $responder)
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
                $putData = json_decode(file_get_contents("php://input"),true);
                $result= $this->_manager->edit($putData);
                $role = $this->_manager->getById($result->getBody());
                $response = $this->_responder->respond($request, $result->setBody($role));
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено редактирвоать роли.', 403);
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }
    }
}
