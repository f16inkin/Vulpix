<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;

use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Реализация интерфейса DataProvider, для работы с MySQL.
 *
 * Class RolesMySQLDataProvider
 * @package Vulpix\Engine\RBAC\Domains\Roles
 */
class RolesMySQLDataProvider implements IRolesDataProvider
{
    private $_connection;

    /**
     * RolesMySQLDataProvider constructor.
     * @param IConnector $connector
     */
    public function __construct(IConnector $connector)
    {
        $this->_connection = $connector::getConnection();
    }

    /**
     * @param int $id
     * @return Role
     */
    public function getById(int $id): Role
    {
        $query = ("SELECT `id` AS `roleId`, `role_name` AS `roleName`, `role_description` AS `roleDescription` 
                   FROM `roles` WHERE `id` = :id");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'id' => $id
        ]);
        if ($result->rowCount() > 0) {
            $role = $result->fetch();
            return new Role($role['roleId'], $role['roleName'], $role['roleDescription']);
        }
        return new Role();
    }

    /**
     * @param string $name
     * @return Role
     */
    public function getByName(string $name): Role
    {
        $query = ("SELECT `id` AS `roleId`, `role_name` AS 'roleName', `role_description` AS `roleDescription` 
                   FROM `roles` WHERE `role_name` = :roleName");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleName' => $name
        ]);
        if ($result->rowCount() > 0){
            $role = $result->fetch();
            return new Role($role['roleId'], $role['roleName'], $role['roleDescription']);
        }
        return new Role();
    }

    /**
     * @param int $userId
     * @return RolesCollection
     */
    public function getByUserId(int $userId): RolesCollection
    {
        $query = ("SELECT `roles`.`id` AS `roleId`, `role_name` AS `roleName`, `role_description` AS `roleDescription`
                   FROM `user_role`
                   INNER JOIN `roles` ON `roles`.`id` = `user_role`.role_id
                   WHERE `user_role`.user_id = :userId");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'userId' => $userId
        ]);
        /**
         * Создаю коллекцию
         * И далее инициализирую ее
         */
        $collection = new RolesCollection();
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $collection->offsetSet($row['roleName'], new Role($row['roleId'], $row['roleName'], $row['roleDescription']));
            }
        }
        return $collection;
    }

    /**
     * @param int $start
     * @param int $offset
     * @return RolesCollection
     */
    public function getPartly(int $start, int $offset): RolesCollection
    {
        $query = ("SELECT `id` AS `roleId`, `role_name` AS `roleName`, `role_description` AS `roleDescription`
                   FROM `roles` LIMIT :start, :offset");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'start' => $start,
            'offset' => $offset
        ]);
        $collection = new RolesCollection();
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $collection->offsetSet($row['roleName'], new Role($row['roleId'], $row['roleName'], $row['roleDescription']));
            }
        }
        return $collection;
    }

    /**
     * @param Role $role
     * @return int
     */
    public function insert(Role $role): int
    {
        $query = ("INSERT INTO `roles` (role_name, role_description) VALUES (:roleName, :roleDescription)");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleName' => $role->getName(),
            'roleDescription' => $role->getDescription()
        ]);
        return (int)$this->_connection->lastInsertId();
    }

    /**
     * @param Role $role
     */
    public function update(Role $role): void
    {
        $query = ("UPDATE `roles` SET `role_name` = :roleName, `role_description` = :roleDescription
                WHERE `id` = :roleId");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleId' => $role->getId(),
            'roleName' => $role->getName(),
            'roleDescription' => $role->getDescription()
        ]);
    }

    /**
     * @param string $roleIDs
     */
    public function delete(string $roleIDs): void
    {
        $query = ("DELETE FROM `roles` WHERE `id` IN ($roleIDs)");
        $result = $this->_connection->prepare($query);
        $result->execute();
    }
}