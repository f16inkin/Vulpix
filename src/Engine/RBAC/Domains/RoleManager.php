<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;

use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;
use Vulpix\Engine\RBAC\DataStructures\Collections\RolesCollection;
use Vulpix\Engine\RBAC\DataStructures\Entity\Role;

/**
 * Класс для управления ролями. Create, read, update, delete.
 *
 * Class RoleManager
 * @package Vulpix\Engine\RBAC\Domains
 */
class RoleManager
{
    private $_dbConnector;

    /**
     * RoleManager constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    /**
     * Создает новую роль.
     *
     * @param array|null $roleDetails
     * @return HttpResultContainer
     */
    public function create(?array $roleDetails) : HttpResultContainer {
        /**
         * Если ничего не было передано или же провлена санитизация, будет брошено исключение
         */
        $roleDetails = Sanitizer::sanitize($roleDetails);
        /**
         * Получаю id роли если она имеется, либо создаю новую и возвращаю ее id
         */
        $roleId = $this->isRoleExist($roleDetails['roleName']);
        if (!$roleId){
            $query = ("INSERT INTO `roles` (role_name, role_description) VALUES (:roleName, :roleDescription)");
            $result = $this->_dbConnector::getConnection()->prepare($query);
            $result->execute([
                'roleName' => $roleDetails['roleName'],
                'roleDescription' => $roleDetails['roleDescription']
            ]);
            return new HttpResultContainer((int)$this->_dbConnector::getConnection()->lastInsertId(), 201);
        }
        return new HttpResultContainer($roleId, 200);
    }

    /**
     * Получает информацию по выбранной роли.
     *
     * @param int|null $id
     * @return Role
     */
    public function get(?int $id) : Role
    {
        $id = Sanitizer::sanitize($id);
        $query = ("SELECT * FROM `roles` WHERE `id` = :id");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'id' => $id
        ]);
        $role = new Role();
        if ($result->rowCount() > 0) {
            $row = $result->fetchAll();
            $role->setId($row[0]['id']);
            $role->setName($row[0]['role_name']);
            $role->setDescription($row[0]['role_description']);
        }
        return $role;
    }

    /**
     * Редактирует роль.
     *
     * @param array|null $roleDetails
     * @return HttpResultContainer
     */
    public function edit(?array $roleDetails) : HttpResultContainer  {
        $roleDetails = Sanitizer::sanitize($roleDetails);
        $query = ("UPDATE `roles` SET `role_name` = :roleName, `role_description` = :roleDescription
                WHERE `id` = :roleId");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'roleId' => $roleDetails['roleId'],
            'roleName' => $roleDetails['roleName'],
            'roleDescription' => $roleDetails['roleDescription']
        ]);
        return new HttpResultContainer((int)$roleDetails['roleId'], 200);
    }

    /**
     * Удаляет роль / роли по заданным ID
     *
     * @param array|null $roleIDs
     * @return HttpResultContainer
     */
    public function delete(?array $roleIDs) : HttpResultContainer {
        /**
         * Зачистить массив
         */
        $roleIDs= Sanitizer::transformToInt($roleIDs);
        /**
         * Склеить строку для массового удаления
         */
        $roleIDs = implode(', ', $roleIDs);
        $query = ("DELETE FROM `roles` WHERE `id` IN ($roleIDs)");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute();
        return new HttpResultContainer;
    }

    /**
     * Получает список всех ролей системы.
     *
     * @return HttpResultContainer
     */
    public function getAll() : HttpResultContainer
    {
        $query = ("SELECT * FROM `roles`");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute();
        if ($result->rowCount() > 0){
            $roles = $result->fetchAll();
            return new HttpResultContainer($roles, 200);
        }
        return new HttpResultContainer('Ролей в системе не найдено', 204);
    }

    /**
     * Проверяет есть ли указанная роль в системе. Если есть вернет ее ID.
     *
     * @param string $roleName
     * @return bool|int
     */
    public function isRoleExist(string $roleName) {
        $query = ("SELECT `id` FROM `roles` WHERE `role_name` = :roleName");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'roleName' => $roleName
        ]);
        if ($result->rowCount() > 0){
            $roleId = $result->fetch()['id'];
            return $roleId;
        }
        return false;
    }

    /**
     * Проинициализирует роли, проинициализирует им привелегии и вернет в виде коллекции.
     *
     * @param int $userId
     * @return RolesCollection
     */
    public function initRoles(int $userId) : RolesCollection {
        $query = ("SELECT `roles`.`id`, `role_name`, `role_description` FROM `user_role`
                    INNER JOIN `roles` ON `roles`.`id` = `user_role`.role_id
                    WHERE `user_role`.user_id = :userId");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'userId' => $userId
        ]);
        /**
         * Создаю коллекцию
         * И далее инициализирую ее
         */
        $collection = new RolesCollection();
        while ($row = $result->fetch()){
            /**
             * Нет смысла групировать найденные привелегии, так конкретно по этой коллекции будет далее линейный
             * перебор, на поиск требуемого разрешения для выполнения действия контроллера.
             */
            $permissionCollection = (new PermissionManager($this->_dbConnector))->initPermissions($row['id']);
            $role = new Role();
            $role->setId($row['id']);
            $role->setName($row['role_name']);
            $role->setDescription($row['role_description']);
            $role->setPermissions($permissionCollection);
            /**
             * Инициализация по имени.
             */
            $collection->offsetSet($row['role_name'], $role);
        }
        return $collection;
    }
}