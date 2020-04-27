<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Actions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\RBAC\Domains\RBACExceptionsHandler;
use Vulpix\Engine\RBAC\Domains\Role;
use Vulpix\Engine\RBAC\Responders\RoleCreateResponder;

/**
 * Class RoleCreateAction
 * @package Vulpix\Engine\RBAC\Actions
 */
class RoleCreateAction implements RequestHandlerInterface
{
    private $_role;
    private $_responder;

    /**
     * RoleCreateAction constructor.
     * @param Role $role
     * @param RoleCreateResponder $responder
     */
    public function __construct(Role $role, RoleCreateResponder $responder)
    {
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
            $postData = json_decode(file_get_contents("php://input"),true) ?: null;
            $exec = $this->_role->create($postData);
            $role = $this->_role->get($exec->_body);
            $response = $this->_responder->respond($request, $exec->setBody($role));
            return $response;
        }catch (\Exception $e){
            return (new RBACExceptionsHandler())->handle($e);
        }

    }
}
