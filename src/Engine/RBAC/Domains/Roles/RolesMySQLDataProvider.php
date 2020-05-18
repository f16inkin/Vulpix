<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;

use Vulpix\Engine\Database\Connectors\IConnector;

class RolesMySQLDataProvider implements IRolesDataProvider
{
    private $_connection;

    public function __construct(IConnector $connector)
    {
        $this->_connection = $connector::getConnection();
    }

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

    public function update(Role $role): void
    {

    }

    public function delete(array $roleIDs): void
    {
        // TODO: Implement delete() method.
    }
}