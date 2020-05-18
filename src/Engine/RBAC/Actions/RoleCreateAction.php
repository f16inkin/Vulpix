<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\Roles\RoleManager;
use Vulpix\Engine\RBAC\Service\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Responders\RoleCreateResponder;

/**
 * Создать новую роль.
 *
 * Class RoleCreateAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleCreateAction implements RequestHandlerInterface
{
    private RoleManager $_manager;
    private RoleCreateResponder $_responder;

    /**
     * RoleCreateAction constructor.
     * @param RoleManager $manager
     * @param RoleCreateResponder $responder
     */
    public function __construct(RoleManager $manager, RoleCreateResponder $responder)
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
            $postData = json_decode(file_get_contents("php://input"),true) ?: null;
            $result = $this->_manager->create($postData);
            /**
             * Только что созданная роль и не должна содержать привелегий.
             */
            $role = $this->_manager->getById($result->getBody());
            $response = $this->_responder->respond($request, $result->setBody($role));
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }

    }
}
