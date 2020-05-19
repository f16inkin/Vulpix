<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Permissions;

use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Class PermissionsMySQLDataProvider
 * @package Vulpix\Engine\RBAC\Domains\Permissions
 */
class PermissionsMySQLDataProvider implements IPermissionsDataProvider
{
    private IConnector $_connection;

    /**
     * PermissionsMySQLDataProvider constructor.
     * @param IConnector $connector
     */
    public function __construct(IConnector $connector)
    {
        $this->_connection = $connector::getConnection();
    }

    /**
     * @param int $roleId
     * @return PermissionsCollection
     */
    public function getListed(int $roleId) : PermissionsCollection
    {
        $query = ("SELECT `permission_id` AS `permissionId`,`permission_name` AS `permissionName`, `permission_description` AS `permissionDescription` 
                    FROM `role_permission`
                    INNER JOIN `permissions` ON permissions.id = role_permission.permission_id
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `permission_name`");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        $collection = new PermissionsCollection();
        while ($row = $result->fetch()){
            $id = $row['permissionId'];
            $name = $row['permissionName'];
            $description = $row['permissionDescription'];
            $permission = new Permission($id, $name, $description);
            $collection->offsetSet($name, $permission);
        }
        return $collection;
    }

    /**
     * @param int $roleId
     * @return array
     */
    public function getGrouped(int $roleId) : array
    {
        if (isset($roleId)){
            $query = ("SELECT `permissions`.`id` AS `permissionId`, `permission_name` AS `permissionName`, 
                       `permission_description` AS `permissionDescription`, 
                       `permission_group` AS `permissionGroup`, `group_name` AS `groupName`, 
                       `group_description` AS `groupDescription`, `group`.`id` AS `groupId` 
                    FROM `role_permission`
                    INNER JOIN `permissions` ON permissions.id = role_permission.permission_id
                    INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `permissionId`");
            $result = $this->_connection->prepare($query);
            $result->execute([
                'roleId' => $roleId
            ]);
            $collections = [];
            while ($row = $result->fetch()){
                $id = $row['permissionId'];
                $group = $row['groupDescription'];
                $name = $row['permissionName'];
                $description = $row['permissionDescription'];
                $permission = new Permission($id, $name, $description);
                if (isset($collections[$group])){
                    /**
                     * @var PermissionsCollection
                     */
                    $collections[$group]->offsetSet($name, $permission);
                }else{
                    $collections[$group] = new PermissionsCollection();
                    $collections[$group]->offsetSet($name, $permission);
                }
            }
        }
        /**
         * Для большего порядка сортировка по алфавиту.
         */
        ksort($collections);
        return $collections;
    }

    /**
     * @param int $roleId
     * @return array
     */
    public function getAvailable(int $roleId) : array
    {
        $query = ("SELECT `permissions`.`id` AS `permissionId`, `permission_name` AS `permissionName`, 
                    `permission_description` AS `permissionDescription`, `group_name` AS `groupName`, 
                    `group_description` AS `groupDescription`, 
                    `group`.`id` AS `groupId`
                    FROM `permissions`
                    INNER JOIN `role_permission` ON `permissions`.`id` = `role_permission`.`permission_id`
                    INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `groupId`");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        $permissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $permissions[$row['permissionId']] = $row;
            }
        }
        return $permissions;
    }

    /**
     * @return array
     */
    public function getAll() : array
    {
        $query = ("SELECT `permissions`.`id` AS `permissionId`, `permission_name` AS `permissionName`, 
                   `permission_description` AS `permissionDescription`, `group_name` AS `groupName`, 
                   `group_description` AS `groupDescription`, 
                   `group`.`id` AS `groupId` 
                   FROM `permissions`
                   INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                   ORDER BY `groupId`");
        $result = $this->_connection->prepare($query);
        $result->execute();
        $permissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){

                $permissions[$row['permissionId']] = $row;
            }
        }
        return $permissions;
    }

    /**
     * @param int $roleId
     * @param string $permissionIDs
     * @return array
     */
    public function findRolePermissionsIDs(int $roleId, string $permissionIDs) : array
    {
        $query = ("SELECT `permission_id` FROM `role_permission` WHERE role_id = :roleId AND `permission_id` IN ($permissionIDs)");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        $permissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $permissions[] = $row['permission_id'];
            }
        }
        return $permissions;
    }

    /**
     * @param int $start
     * @param int $offset
     * @return array
     */
    public function getPartly(int $start, int $offset) : array
    {
        $query = ("SELECT `permissions`.`id` AS `permissionId`, `permission_name` AS `permissionName`, `permission_description` AS `permissionDescription`,
                   `group`.`id` AS `groupId`, `group_description` AS `groupDescription` 
                   FROM `permissions` 
                   INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                   ORDER BY `groupId` 
                   LIMIT :start, :offset");
        $result = $this->_connection->prepare($query);
        $result->execute([
            'start' => $start,
            'offset' => $offset
        ]);
        $collections = [];
        while ($row = $result->fetch()){
            $id = $row['permissionId'];
            $group = $row['groupDescription'];
            $name = $row['permissionName'];
            $description = $row['permissionDescription'];
            $permission = new Permission($id, $name, $description);
            if (isset($collections[$group])){
                /**
                 * @var PermissionsCollection
                 */
                $collections[$group]->offsetSet($name, $permission);
            }else{
                $collections[$group] = new PermissionsCollection();
                $collections[$group]->offsetSet($name, $permission);
            }
        }
        return $collections;
    }

    /**
     * @param int $roleId
     * @param array $permissionIDs
     */
    public function add(int $roleId, array $permissionIDs) : void
    {
        $query = ("INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES ");
        foreach ($permissionIDs as $permissionId){
            $query .= sprintf("(%s, %s),", $roleId, $permissionId);
        }
        $query = rtrim($query, ',');
        $result = $this->_connection->prepare($query);
        $result->execute();
    }

    /**
     * @param int $roleId
     * @param string $permissionIDs
     */
    public function delete(int $roleId, string $permissionIDs) : void
    {
        $query = ("DELETE FROM `role_permission` WHERE `role_id` = :roleId AND `permission_id` IN ($permissionIDs)");
        $result = $this->_connection->prepare($query);
        $result->execute(['roleId' => $roleId]);
    }
}