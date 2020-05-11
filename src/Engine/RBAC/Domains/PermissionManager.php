<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;

use Vulpix\Engine\Core\DataStructures\Entity\ResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;
use Vulpix\Engine\RBAC\DataStructures\Collections\PermissionsCollection;
use Vulpix\Engine\RBAC\DataStructures\Entity\Permission;

/**
 * Класс для управления привелегиями: Добавить, Получить(разница), Удалить.
 *
 * Class PermissionManager
 * @package Vulpix\Engine\RBAC\Domains
 */
class PermissionManager
{
    public const LISTED = 'listed';
    public const GROUPED = 'grouped';

    private $_dbConnection;

    /**
     * PermissionManager constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    /**
     * Вернет список привелегий. Полезно для линейно обхода по коллекции, как одномерному массиву.
     *
     * @param int|null $roleId
     * @return PermissionsCollection
     */
    private function getListed(? int $roleId) : PermissionsCollection
    {
        if (isset($roleId)){
            $query = ("SELECT `permission_id`,`permission_name`, `permission_description` FROM `role_permission`
                    INNER JOIN `permissions` ON permissions.id = role_permission.permission_id
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `permission_name`");
            $result = $this->_dbConnection->prepare($query);
            $result->execute([
                'roleId' => $roleId
            ]);
            $collection = new PermissionsCollection();
            while ($row = $result->fetch()){
                $id = $row['permission_id'];
                $name = $row['permission_name'];
                $description = $row['permission_description'];
                $permission = new Permission($id, $name, $description);
                $collection->offsetSet($name, $permission);
            }
        }
        return $collection;
    }

    /**
     * Вернет привелегии объединенные по группам.
     *
     * @param int|null $roleId
     * @return array
     */
    private function getGrouped(? int $roleId) : array {
        if (isset($roleId)){
            $query = ("SELECT `permissions`.`id` AS `permission_id`,`permission_name`, `permission_description`, `permission_group`
                    `group_name`, `group_description`, `group`.`id` AS `groupId` 
                    FROM `role_permission`
                    INNER JOIN `permissions` ON permissions.id = role_permission.permission_id
                    INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `permission_name`");
            $result = $this->_dbConnection->prepare($query);
            $result->execute([
                'roleId' => $roleId
            ]);
            $collections = [];
            while ($row = $result->fetch()){
                $id = $row['permission_id'];
                $group = $row['group_description'];
                $name = $row['permission_name'];
                $description = $row['permission_description'];
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
        return $collections;
    }

    /**
     * Получит пару из role_id = permission_id.
     * Функция нужна для отсечения найденных совпадений по роли - привелегии, чтобы не дублировать их(роль - привелегия)
     * в таблице.
     *
     * @param int|null $roleId
     * @param array|null $permissionIDs
     * @return array
     */
    public function findRolePermissionIDs(? int $roleId, ? array $permissionIDs) : array {
        $roleId = Sanitizer::sanitize($roleId);
        $permissionIDs = Sanitizer::sanitize($permissionIDs);
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
     * Получить привелегии которые не принадлежат выбранной роли.
     *
     * @param int|null $roleId
     * @return ResultContainer
     */
    public function getDifferentPermissions(? int $roleId) : ResultContainer {
        $roleId = Sanitizer::transformToInt($roleId);
        /**
         * Step 1. Поиск доступных привелегий.
         */
        $query = ("SELECT `permissions`.`id` AS `id`, `permission_name`, `permission_description`, 
                    `group_name`, `group_description`, `group`.`id` AS `groupId`
                    FROM `permissions`
                    INNER JOIN `role_permission` ON `permissions`.`id` = `role_permission`.`permission_id`
                    INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                    WHERE `role_permission`.role_id = :roleId
                    ORDER BY `groupId`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        $availablePermissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $availablePermissions[$row['id']] = $row;
            }
        }
        /**
         * Step 2. Поиск всех првиелегий
         */
        $query = ("SELECT `permissions`.`id` AS `id`, `permission_name`, `permission_description`, 
                    `group_name`, `group_description`, `group`.`id` AS `groupId` 
                   FROM `permissions`
                   INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                   ORDER BY `groupId`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        $allPermissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){

                $allPermissions[$row['id']] = $row;
            }
        }
        /**
         * Step 3. Сравнение и нахождение различий.
         */
        $differentPermissions = array_diff_key($allPermissions, $availablePermissions);
        if (!empty($differentPermissions)){
            /**
             * Step 4. Формирую массив нужного вида.
             */
            $collections = [];
            foreach ($differentPermissions as $key => $value){
                $id = $value['id'];
                $group = $value['group_description'];
                $name = $value['permission_name'];
                $description = $value['permission_description'];
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
            return new ResultContainer($collections, 200);
        }
        return new ResultContainer();
    }

    /**
     * Получить все зарегистрированные в системе привелегии.
     * Привелегии объединены по группам.
     *
     * @return ResultContainer
     */
    public function getAllPermissions() : ResultContainer{
        $query = ("SELECT `permissions`.`id` AS `id`, `permission_name`, `permission_description`, 
                    `group_name`, `group_description`, `group`.`id` AS `groupId` 
                   FROM `permissions`
                   INNER JOIN `permission_groups` AS `group` ON `permissions`.`permission_group` = `group`.`id`
                   ORDER BY `groupId`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        if ($result->rowCount() > 0){
            $collections = [];
            while ($row = $result->fetch()){
                $id = $row['id'];
                $group = $row['group_description'];
                $name = $row['permission_name'];
                $description = $row['permission_description'];
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
                $permissions[$row['group_description']][$row['permission_name']] = $row['permission_description'];
            }
            return new ResultContainer($collections, 200);
        }
        /**
         * Ресурс был найден, значит не 404.
         * Не было найдено данных для отображения, значит 204.
         */
        return new ResultContainer();
    }

    /**
     * Добавить в систему привелегии, для определенной по ID роли.
     *
     * @param int|null $roleId
     * @param array|null $permissionIDs
     * @return ResultContainer
     */
    public function addPermissions(? int $roleId, ? array $permissionIDs) : ResultContainer {
        if (!empty($permissionIDs)){
            $query = ("INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES ");
            foreach ($permissionIDs as $permissionId){
                $query .= sprintf("(%s, %s),", $roleId, $permissionId);
            }
            $query = rtrim($query, ',');
            $result = $this->_dbConnection->prepare($query);
            $result->execute();
            return new ResultContainer($roleId, 201);
        }
        return new ResultContainer($roleId, 200);
    }

    /**
     * Удалить выбранные привелегии у определенной роли.
     *
     * @param int|null $roleId
     * @param array|null $permissionIDs
     * @return ResultContainer
     */
    public function deletePermissions(? int $roleId, ? array $permissionIDs) : ResultContainer {
        $roleId = Sanitizer::sanitize($roleId);
        $permissionIDs = Sanitizer::sanitize($permissionIDs);
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
        /**
         * 204 - No Content
         */
        return new ResultContainer();
    }

    /**
     * Над-метод, для формирования коллекции(й) првиелегий в зависимости от mode.
     *
     * @param int|null $roleId
     * @param string $mode
     * @return array|PermissionsCollection
     */
    public function initPermissions(? int $roleId, $mode = self::LISTED) {
        if ($mode === self::LISTED){
            return $this->getListed($roleId);
        }else{
            return $this->getGrouped($roleId);
        }
    }

}
