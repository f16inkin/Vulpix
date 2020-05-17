<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountRepository;
use Vulpix\Engine\AAIS\Responders\AccountEditResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;

/**
 * Class AccountEditAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountEditAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'AAIS_ACCOUNT_EDIT';

    private AccountRepository $_repository;
    private AccountEditResponder $_responder;

    /**
     * AccountEditAction constructor.
     * @param AccountRepository $repository
     * @param AccountEditResponder $responder
     */
    public function __construct(AccountRepository $repository, AccountEditResponder $responder)
    {
        $this->_repository = $repository;
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
                $result = $this->_repository->edit($putData);
                $account = $this->_repository->get($result->getBody());
                $response = $this->_responder->respond($request, $result->setBody($account));
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено редактировать учетную запись.', 403);
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}