<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountRepository;
use Vulpix\Engine\AAIS\Responders\AccountCreateResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;

/**
 * Class AccountCreateAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountCreateAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'AAIS_ACCOUNT_CREATE';

    private AccountRepository $_repository;
    private AccountCreateResponder $_responder;

    /**
     * AccountCreateAction constructor.
     * @param AccountRepository $repository
     * @param AccountCreateResponder $responder
     */
    public function __construct(AccountRepository $repository, AccountCreateResponder $responder)
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
                $postData = json_decode(file_get_contents("php://input"),true) ?: null;
                $result = $this->_repository->create($postData);
                $account = $this->_repository->getById($result->getBody());
                $response = $this->_responder->respond($request, $result->setBody($account));
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено регистрировать новых пользователей.', 403);
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}