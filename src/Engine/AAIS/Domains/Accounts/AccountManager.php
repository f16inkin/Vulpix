<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Accounts;

use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;

/**
 * Repository.
 *
 * Class AccountManager
 * @package Vulpix\Engine\AAIS\Domains\Accounts
 */
class AccountManager
{
    private IAccountDataProvider $_dataProvider;

    public function __construct(IAccountDataProvider $dataProvider)
    {
        $this->_dataProvider = $dataProvider;
    }

    /**
     * Получить информацию аккаунта по его id.
     *
     * @param int $id
     * @return Account
     */
    public function getById(?int $id) : Account
    {
        $id = Sanitizer::transformToInt($id);
        return $this->_dataProvider->getById($id);
    }

    /**
     * Найти учетную запись по ее имени.
     *
     * @param string $username
     * @return Account
     */
    public function getByName(?string $username) : Account
    {
        $username = Sanitizer::sanitize($username);
        return $this->_dataProvider->getByName($username);
    }

    /**
     * @param array|null $partlyDetails
     * @return HttpResultContainer
     */
    public function getPartly(?array $partlyDetails) : HttpResultContainer
    {
        $partlyDetails = Sanitizer::transformToInt($partlyDetails);
        /**
         * @var int $partlyDetails['start']
         * @var int $partlyDetails['offset]
         */
        $collection = $this->_dataProvider->getPartly($partlyDetails['start'], $partlyDetails['offset']);
        if ($collection->count() > 0){
            return new HttpResultContainer($collection, 200);
        }
        return new HttpResultContainer('Учетных записей в системе не найдено', 204);
    }

    /**
     * Обновить параметры учетной записи.
     *
     * @param Account $account
     * @return int
     */
    public function editAccount(?array $accountDetails) : HttpResultContainer
    {
        $id = Sanitizer::transformToInt($accountDetails['accountId']);
        $name = Sanitizer::sanitize($accountDetails['accountName']);
        $account = new Account($id, $name);
        $this->_dataProvider->updateAccount($account);
        return new HttpResultContainer($id, 200);
    }

    /**
     * Смена пароля учетной записи.
     *
     * @param array|null $passwordDetails
     * @return HttpResultContainer
     */
    public function changePassword(?array $passwordDetails) : HttpResultContainer
    {
        $accountId = Sanitizer::transformToInt($passwordDetails['accountId']);
        $oldPassword = Sanitizer::sanitize($passwordDetails['oldPassword']);
        $newPassword = Sanitizer::sanitize($passwordDetails['newPassword']);
        $account = $this->getById($accountId);
        if ($account->getId()){
            if (password_verify($oldPassword, $account->getPasswordHash())){
                $newPassword = password_hash($newPassword, PASSWORD_ARGON2I);
                $this->_dataProvider->updatePassword($newPassword, $accountId);
                return new HttpResultContainer('Пароль обновлен', 200);
            }
        }
        return new HttpResultContainer('Данный аккаунт не найден', 404);
    }

    /**
     * Сброс пароля, установка нового
     *
     * @param array|null $passwordDetails
     * @return HttpResultContainer
     */
    public function resetPassword(?array $passwordDetails) : HttpResultContainer
    {
        $accountId = Sanitizer::transformToInt($passwordDetails['accountId']);
        $password = Sanitizer::sanitize($passwordDetails['newPassword']);
        $newPassword = password_hash($password, PASSWORD_ARGON2I);
        $this->_dataProvider->updatePassword($newPassword, $accountId);
        return new HttpResultContainer('Старый пароль сброшен. Установлен новый пароль', 200);
    }

    /**
     * Создать новую учетную запись.
     *
     * @param Account $account
     * @return int
     */
    public function create(?array $accountDetails) : HttpResultContainer
    {
        $accountDetails = Sanitizer::sanitize($accountDetails);
        $account = $this->getByName($accountDetails['userName']);
        if (!$account->getId()){
            $account = new Account(0, $accountDetails['userName'], password_hash($accountDetails['userPassword'],PASSWORD_ARGON2I));
            return new HttpResultContainer($this->_dataProvider->insert($account), 201);
        }
        return new HttpResultContainer($account->getId(), 409);
    }

    /**
     * Удалить учетную запись / учетные записи.
     *
     * @param int $id
     */
    public function delete(?array $accountIDs) : HttpResultContainer
    {
        $accountIDs = Sanitizer::transformToInt($accountIDs);
        $accountIDs = implode(', ', $accountIDs);
        $this->_dataProvider->delete($accountIDs);
        return new HttpResultContainer;
    }
}