<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Permissions;

/**
 * Interface IPermissionsDataProvider
 * @package Vulpix\Engine\RBAC\Domains\Permissions
 */
interface IPermissionsDataProvider
{
    /**
     * @param int $roleId
     * @return PermissionsCollection
     */
    public function getListed(int $roleId) : PermissionsCollection;

    /**
     * @param int $roleId
     * @return array
     */
    public function getGrouped(int $roleId) : array;

    /**
     * @param int $roleId
     * @return array
     */
    public function getAvailable(int $roleId) : array;

    /**
     * @return array
     */
    public function getAll() : array;

    /**
     * @param int $roleId
     * @param string $permissionIDs
     * @return array
     */
    public function findRolePermissionsIDs(int $roleId, string $permissionIDs) : array;

    /**
     * @param int $start
     * @param int $offset
     * @return array
     */
    public function getPartly(int $start, int $offset) : array;

    /**
     * @param int $roleId
     * @param array $permissionIDs
     */
    public function add(int $roleId, array $permissionIDs) : void;

    /**
     * @param int $roleId
     * @param string $permissionIDs
     */
    public function delete(int $roleId, string $permissionIDs) : void;
}