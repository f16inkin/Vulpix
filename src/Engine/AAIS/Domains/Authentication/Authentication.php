<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Authentication;

use Vulpix\Engine\AAIS\Domains\Accounts\AccountManager;
use Vulpix\Engine\AAIS\Service\JWTCreator;
use Vulpix\Engine\AAIS\Service\RTCreator;
use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;

/**
 * Аутентификация - процесс проверки учетных данных пользователя.
 *
 * Class Authentication
 * @package Vulpix\Engine\AAIS\Domains
 */
class Authentication
{
    private AccountManager $_repository;
    private RTCreator $_rtCreator;
    private HttpResultContainer $_resultContainer;

    /**
     * Authentication constructor.
     * @param AccountManager $repository
     * @param RTCreator $rtCreator
     * @param HttpResultContainer $resultContainer
     */
    public function __construct(AccountManager $repository, RTCreator $rtCreator, HttpResultContainer $resultContainer)
    {
        $this->_repository = $repository;
        $this->_rtCreator = $rtCreator;
        $this->_resultContainer = $resultContainer;
    }

    /**
     * Проводит аутентификацию пользователя по заданным параметрам.
     * Может отвечать сообщениями с необходимой информацией об ошибке аутентификации.
     * В случае успеха вернет пару из access / refresh tokens + expire time for access token.
     *
     * @param string|null $userName
     * @param string|null $userPassword
     * @return HttpResultContainer
     */
    public function authenticate(? string $userName, ? string $userPassword) : HttpResultContainer {
        $account = $this->_repository->getByName($userName);
        if ($account->getId()){
            $hash = $account->getPasswordHash();
            if (password_verify($userPassword, $hash)){
                $tokens = [
                    'accessToken' => JWTCreator::create($account),
                    'refreshToken' => $this->_rtCreator->create($account),
                    'expiresIn' => JWTCreator::getExpiresIn() - 60
                ];
                return $this->_resultContainer->setBody($tokens)->setStatus(200);
            }
            return $this->_resultContainer->setBody('Пароль не верен.')->setStatus(403);
        }
        return $this->_resultContainer->setBody('Такой учетной записи не существует в системе')->setStatus(403);
    }

}