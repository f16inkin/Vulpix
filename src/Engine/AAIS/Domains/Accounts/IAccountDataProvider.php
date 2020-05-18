<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Accounts;

/**
 * Data Provider Interface.
 *
 * Благодаря этому интерфейсу, я могу смело подменять реализации хранилишь его имплементирующих.
 * В хранилище содержатся голые SQL запросы / запросы. Теперь если мне нужно переехать с одно БД на другую,
 * достаточно сделать реализацию этого интерефейса под эту БД. Логика выше не будет затронута изменениями.
 *
 * Interface IAccountDataProvider
 * @package Vulpix\Engine\AAIS\Domains\Accounts
 */
interface IAccountDataProvider
{
    public function getById(int $id) : Account;

    public function getByName(string $userName) : Account;

    public function getPartly(int $start, int $offset) : AccountsCollection;

    public function insert(Account $account) : int;

    public function updateAccount(Account $account) : void;

    public function updatePassword(string $password, int $accountId) : void;

    public function delete(string $accountIDs) : void;
}