<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Accounts;

use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Persistence.
 *
 * Вся логика по валидации, санитизации и тд идет в слое выше. Здесь я пишу чистые SQL запросы.
 * Так как Storage реализует интерфейс, я могу легко подменять его на другие: PostGRE, MSSQL и другие
 * реализации хранилищь не заботясь о логике валидации и тд. Просто чистые запросы.
 *
 * Class AccountMySQLStorage
 * @package Vulpix\Engine\AAIS\Domains\Accounts
 */
class AccountMySQLStorage implements IAccountStorage
{
    private $_connection;

    /**
     * AccountMySQLStorage constructor.
     * @param IConnector $connector
     */
    public function __construct(IConnector $connector)
    {
        $this->_connection = $connector::getConnection();
    }

    public function getById(int $id) : Account
    {
        $query = ("SELECT `id` AS `userId`, `user_name` AS `userName`, `password_hash` AS `passwordHash` FROM `user_accounts` WHERE `id` = :id");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'id' => $id
        ]);
        if ($result->rowCount() > 0){
            $account = $result->fetch();
            return new Account($account['userId'], $account['userName'], $account['passwordHash']);
        }
        return new Account();
    }

    /**
     * Вернет instance Account, либо его же но в виде заглушки с пустыми параметрами.
     *
     * @param string $userName
     * @return Account
     */
    public function getByName(string $userName): Account
    {
        $query = ("SELECT `id` AS `userId`, `user_name` AS `userName`, `password_hash` AS `passwordHash` FROM `user_accounts` WHERE `user_name` = :userName");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'userName' => $userName
        ]);
        if ($result->rowCount() > 0){
            $account = $result->fetch();
            return new Account($account['userId'], $account['userName'], $account['passwordHash']);
        }
        return new Account();
    }

    /**
     * Вернет заданное количество аккаунтов из расчета о заданным параметрам.
     *
     * @param int $start
     * @param int $perPage
     * @return AccountsCollection
     */
    public function getPartly(int $start, int $offset): AccountsCollection
    {
        $query = ("SELECT `id` AS `userId`, `user_name` AS `userName`
                   FROM `user_accounts` LIMIT :start, :offset");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'start' => $start,
            'offset' => $offset
        ]);
        $collection = new AccountsCollection();
        if ($result->rowCount() > 0){
            $i = 0;
            while ($row = $result->fetch()){
                $collection->offsetSet($i, new Account($row['userId'], $row['userName']));
                $i ++;
            }
        }
        return $collection;
    }

    /**
     * Обновит параметры учтной записи.
     *
     * @param Account $account
     */
    public function updateAccount(Account $account) : void
    {
        $query = ("UPDATE `user_accounts` SET `user_name` = :userName WHERE `id` = :id");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'userName' => $account->getUserName(),
            'id' => $account->getId()
        ]);
    }

    public function updatePassword(string $password, int $accountId): void
    {
        $query = ("UPDATE `user_accounts` SET `password_hash` = :password WHERE `id` = :id");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'password' => $password,
            'id' => $accountId
        ]);
    }

    /**
     * Создаст новую учетную запись.
     *
     * @param Account $account
     * @return int
     */
    public function insert(Account $account): int
    {
        $query = ("INSERT INTO `user_accounts` (`user_name`, `password_hash`) VALUES (:userName, :passwordHash)");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'userName' => $account->getUserName(),
            'passwordHash' => $account->getPasswordHash()
        ]);
        return (int)$this->_connection->lastInsertId();
    }

    /**
     * Удалить учетную запись
     *
     * @param int $id
     */
    public function delete(array $accountIDs): void
    {
        $accountIDs = implode(', ', $accountIDs);
        $query = ("DELETE FROM `user_accounts` WHERE `id` IN ($accountIDs)");
        $result = $this->_connection->prepare($query);
        $result->execute();
    }
}