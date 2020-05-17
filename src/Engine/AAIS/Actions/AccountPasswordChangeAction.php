<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Actions;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Vulpix\Engine\AAIS\Domains\Accounts\AccountRepository;
use Vulpix\Engine\AAIS\Responders\AccountPasswordChangeResponder;
use Vulpix\Engine\AAIS\Service\AAISExceptionsHandler;
use Vulpix\Engine\RBAC\Service\PermissionVerificator;

/**
 * Class AccountPasswordChangeAction
 * @package Vulpix\Engine\AAIS\Actions
 */
class AccountPasswordChangeAction implements RequestHandlerInterface
{
    private const ACCESS_PERMISSION = 'AAIS_ACCOUNT_PASSWORD_CHANGE';

    private AccountRepository $_repository;
    private AccountPasswordChangeResponder $_responder;

    /**
     * AccountPasswordChangeAction constructor.
     * @param AccountRepository $repository
     * @param AccountPasswordChangeResponder $responder
     */
    public function __construct(AccountRepository $repository, AccountPasswordChangeResponder $responder)
    {
        $this->_repository = $repository;
        $this->_responder = $responder;
    }

    /**
     * Узнать редактирует пользователь свой пароль или от другой учетной записи.
     *
     * @param ServerRequestInterface $request
     * @param int $accountId
     * @return bool
     */
    private function verify(ServerRequestInterface $request, int $accountId) : bool
    {
        $userId = $request->getAttribute('User')['userId'];
        return $accountId === $userId ? true : false;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try{
            /**
             * Получу данные в виде массива. Старый пароль, новый пароль, id учетной записи которой меняется пароль.
             * Два варианта:
             * Если пользователь наделен правами менять пароли учетных записей(Администратор), он сможет его сменить.
             * Если пользователь меняет свой пароль.
             */
            $putData = json_decode(file_get_contents("php://input"),true);
            if (PermissionVerificator::verify($request->getAttribute('Roles'), self::ACCESS_PERMISSION)
                || $this->verify($request, (int)$putData['accountId']))
            {
                $result = $this->_repository->changePassword($putData);
                $response = $this->_responder->respond($request, $result);
                return $response;
            }
            return new JsonResponse('Access denied. Вам запрещено менять пароли учетных записей.', 403);
        }catch (\Exception $e){
            return (new AAISExceptionsHandler())->handle($e);
        }
    }
}