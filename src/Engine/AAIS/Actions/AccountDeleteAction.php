<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Responders\AccountDeleteResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;

/**
 * Class AccountDeleteAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountDeleteAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'AAIS_ACCOUNT_DELETE';

    private AccountManager $_manager;
    private AccountDeleteResponder $_responder;

    /**
     * AccountDeleteAction constructor.
     * @param AccountManager $manager
     * @param AccountDeleteResponder $responder
     */
    public function __construct(AccountManager $manager, AccountDeleteResponder $responder)
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
            if(PermissionVerificator::verify($request->getAttribute('Roles'), self::ACCESS_PERMISSION)){
                $deleteData = json_decode(file_get_contents("php://input"),true) ?: null;
                $accountIDs = $deleteData['accountIDs'];
                $result = $this->_manager->delete($accountIDs);
                $response = $this->_responder->respond($request, $result);
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено удалять аккаунты пользователей.', 403);
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}