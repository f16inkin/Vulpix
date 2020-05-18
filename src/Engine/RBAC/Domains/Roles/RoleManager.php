<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;

use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\MySQLConnector;
use Vulpix\Engine\RBAC\Domains\PermissionManager;

class RoleManager
{
    private IRolesDataProvider $_dataProvider;

    public function __construct(IRolesDataProvider $dataProvider)
    {
        $this->_dataProvider = $dataProvider;
    }

    public function getById(?int $id) : Role
    {
        $id = Sanitizer::transformToInt($id);
        return $this->_dataProvider->getById($id);
    }

    public function getByName(?string $name) : Role
    {
        $name = Sanitizer::sanitize($name);
        return $this->_dataProvider->getByName($name);
    }

    public function getPartly(?array $partlyDetails)
    {
        $partlyDetails = Sanitizer::transformToInt($partlyDetails);
        /** @var int  $partlyDetails */
        $collection = $this->_dataProvider->getPartly($partlyDetails['start'], $partlyDetails['offset']);
        if ($collection->count() > 0){
            return new HttpResultContainer($collection, 200);
        }
        return new HttpResultContainer('Ролей в системе не найдено', 204);
    }

    public function create(?array $roleDetails) : HttpResultContainer {
        /**
         * Если ничего не было передано или же провлена санитизация, будет брошено исключение
         */
        $roleDetails = Sanitizer::sanitize($roleDetails);
        /**
         * Проверю, может роль уже создана и присутсвует в системе.
         */
        $role = $this->getByName($roleDetails['roleName']);
        if (!$role->getId()){
            $role = new Role(0, $roleDetails['roleName'], $roleDetails['roleDescription']);
            return new HttpResultContainer($this->_dataProvider->insert($role), 201);
        }
        return new HttpResultContainer($role->getId(), 409);
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

    public function initRoles(?int $userId) : RolesCollection {
        $userId = Sanitizer::transformToInt($userId);
        $collection = $this->_dataProvider->getByUserId($userId);
        if ($collection->count() > 0){
            /**
             * @var Role $role
             */
            foreach ($collection as $key => $role){
                $permissions = (new PermissionManager(new MySQLConnector()))->initPermissions($role->getId());
                $role->setPermissions($permissions);
            }
        }
        return $collection;
    }
}