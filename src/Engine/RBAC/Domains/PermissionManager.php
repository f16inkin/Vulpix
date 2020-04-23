<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Класс для управления привелегиями: Добавить, Получить(разница), Удалить.
 *
 * Class PermissionManager
 * @package Vulpix\Engine\RBAC\Domains
 */
class PermissionManager
{
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    /**
     * Найдет ID всех привелегий заданной роли
     *
     * @param int $roleId
     * @param array $permissionsIDs
     * @return array
     */
    public function findRolePermissionIDs(int $roleId, array $permissionIDs) : array {
        $permissions = [];
        $permissionIDs = implode(', ', $permissionIDs);
        if ($permissionIDs !== ""){
            $query = ("SELECT `permission_id` FROM `role_permission` WHERE role_id = :roleId AND `permission_id` IN ($permissionIDs)");
            $result = $this->_dbConnection->prepare($query);
            $result->execute([
                'roleId' => $roleId
            ]);

            if ($result->rowCount() > 0){
                while ($row = $result->fetch()){
                    $permissions[] = $row['permission_id'];
                }
            }
        }
        return $permissions;

    }

    /**
     * Добавит заданной роли новые привелегии
     *
     * @param int $roleId
     * @param array $permissionIDs
     * @return int
     */
    public function addPermissions(int $roleId, array $permissionIDs) : int {
        if (!empty($permissionIDs)){
            $query = ("INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES ");
            foreach ($permissionIDs as $permissionId){
                $query .= sprintf("(%s, %s),", $roleId, $permissionId);
            }
            $query = rtrim($query, ',');
            $result = $this->_dbConnection->prepare($query);
            $result->execute();
        }
        return $roleId;
    }

    /**
     * Удалит у выбранной роли, заданные привелегии
     *
     * @param int $roleId
     * @param array $permissionIDs
     * @return bool
     */
    public function deletePermissions(int $roleId, array $permissionIDs) : bool {
        $permissionIDs = implode(', ', $permissionIDs);
        $query = ("DELETE FROM `role_permission` WHERE `role_id` = :roleId AND `permission_id` IN ($permissionIDs)");
        $result = $this->_dbConnection->prepare($query);
        if( $result->execute([
            'roleId' => $roleId
        ])){
            return true;
        }
        return false;
    }

}
