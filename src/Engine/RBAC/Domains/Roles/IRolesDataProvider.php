<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;
/**
 * Интерфейс для всех Data Providers работающих с ролями.
 *
 * Interface IRolesDataProvider
 * @package Vulpix\Engine\RBAC\Domains\Roles
 */
interface IRolesDataProvider
{
    /**
     * Вернет роль без привелегий по ее id.
     *
     * @param int $id
     * @return Role
     */
    public function getById(int $id) : Role;

    /**
     * Вернет роль без привелегий по ее имени.
     *
     * @param string $name
     * @return Role
     */
    public function getByName(string $name) : Role;

    /**
     * Вернет коллекцию ролей относящихся к пользователю, чье ID передано аргументом.
     *
     * @param int $userId
     * @return RolesCollection
     */
    public function getByUserId(int $userId) : RolesCollection;

    /**
     * Вернет коллекцию ролей в заданном количестве.
     *
     * @param int $start
     * @param int $offset
     * @return RolesCollection
     */
    public function getPartly(int $start, int $offset) : RolesCollection;

    /**
     * Создаст новую роль в системе.
     *
     * @param Role $role
     * @return int
     */
    public function insert(Role $role) : int;

    /**
     * Обновит параметры роли.
     *
     * @param Role $role
     */
    public function update(Role $role) : void;

    /**
     * Удалит роль из системы.
     *
     * @param string $roleIDs
     */
    public function delete(string $roleIDs) : void;

}