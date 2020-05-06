<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Core\DataStructures\ExecutionResponse;
use Vulpix\Engine\Core\Foundation\Domain;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Класс для управления привелегиями: Добавить, Получить(разница), Удалить.
 *
 * Class PermissionManager
 * @package Vulpix\Engine\RBAC\Domains
 */
class PermissionManager extends Domain
{
    private $_dbConnection;

    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    public function findRolePermissionIDs( ? int $roleId, ? array $permissionIDs) : array {
        $roleId = $this->sanitize($roleId);
        $permissionIDs = $this->sanitize($permissionIDs);
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

    public function addPermissions(? int $roleId, ? array $permissionIDs) : ExecutionResponse {
        if (!empty($permissionIDs)){
            $query = ("INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES ");
            foreach ($permissionIDs as $permissionId){
                $query .= sprintf("(%s, %s),", $roleId, $permissionId);
            }
            $query = rtrim($query, ',');
            $result = $this->_dbConnection->prepare($query);
            $result->execute();
            return (new ExecutionResponse())->setBody($roleId)->setStatus(201);
        }
        return (new ExecutionResponse())->setBody($roleId)->setStatus(200);
    }

    public function deletePermissions(? int $roleId, ? array $permissionIDs) : void {
        $roleId = $this->sanitize($roleId);
        $permissionIDs = $this->sanitize($permissionIDs);
        $permissionIDs = implode(', ', $permissionIDs);
        $query = ("DELETE FROM `role_permission` WHERE `role_id` = :roleId AND `permission_id` IN ($permissionIDs)");
        $result = $this->_dbConnection->prepare($query);
        /**
         * Здесь нет необходимости делать логическую проверку.
         * Если запрос по какой-то причине не пройдет, сервер бросит PDOException которое обработается и вернет
         * статус 500.
         * Если запрос обработается, то сервер вернет в ответ 204 No Content.
         */
        $result->execute(['roleId' => $roleId]);
    }

}
