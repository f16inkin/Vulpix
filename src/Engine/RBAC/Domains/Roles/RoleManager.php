<?php

declare (strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Roles;

use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;

/**
 * Менеджер управляющий созданием, получением информации, обновлением и удалением ролей.
 *
 * Class RoleManager
 * @package Vulpix\Engine\RBAC\Domains\Roles
 */
class RoleManager
{
    private IRolesDataProvider $_dataProvider;

    /**
     * RoleManager constructor.
     * @param IRolesDataProvider $dataProvider
     */
    public function __construct(IRolesDataProvider $dataProvider)
    {
        $this->_dataProvider = $dataProvider;
    }

    /**
     * @param int|null $id
     * @return Role
     */
    public function getById(?int $id) : Role
    {
        $id = Sanitizer::transformToInt($id);
        return $this->_dataProvider->getById($id);
    }

    /**
     * @param string|null $name
     * @return Role
     */
    public function getByName(?string $name) : Role
    {
        $name = Sanitizer::sanitize($name);
        return $this->_dataProvider->getByName($name);
    }

    /**
     * @param int|null $userId
     * @return RolesCollection
     */
    public function getByUserId(?int $userId) : RolesCollection {
        $userId = Sanitizer::transformToInt($userId);
        return $this->_dataProvider->getByUserId($userId);
    }

    /**
     * @param array|null $partlyDetails
     * @return HttpResultContainer
     */
    public function getPartly(?array $partlyDetails) : HttpResultContainer
    {
        $partlyDetails = Sanitizer::transformToInt($partlyDetails);
        /** @var int  $partlyDetails */
        $collection = $this->_dataProvider->getPartly($partlyDetails['start'], $partlyDetails['offset']);
        if ($collection->count() > 0){
            return new HttpResultContainer($collection, 200);
        }
        return new HttpResultContainer('Ролей в системе не найдено', 204);
    }

    /**
     * @param array|null $roleDetails
     * @return HttpResultContainer
     */
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
     * @param array|null $roleDetails
     * @return HttpResultContainer
     */
    public function edit(?array $roleDetails) : HttpResultContainer  {
        $roleDetails = Sanitizer::sanitize($roleDetails);
        $role = new Role((int)$roleDetails['roleId'], $roleDetails['roleName'], $roleDetails['roleDescription']);
        $this->_dataProvider->update($role);
        return new HttpResultContainer((int)$roleDetails['roleId'], 200);
    }

    /**
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
        $this->_dataProvider->delete($roleIDs);
        return new HttpResultContainer;
    }


}