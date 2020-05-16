<?php

declare(strict_types = 1);

namespace Vulpix\Engine\AAIS\Domains\Accounts;

/**
 * Persistence Interface.
 *
 * Interface IAccountStorage
 * @package Vulpix\Engine\AAIS\Domains\Accounts
 */
interface IAccountStorage
{
    public function get(int $id) : Account;

    public function find(string $userName) : Account;

    public function getPartly(int $start = 0, int $offset = 10) : AccountsCollection;

    public function insert(Account $account) : int;

    public function update(Account $account) : void;

    public function delete(int $id) : void;
}