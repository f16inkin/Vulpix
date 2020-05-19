<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains\Permissions;

use Vulpix\Engine\Core\DataStructures\Entity\HttpResultContainer;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;

class PermissionManager
{
    public const LISTED = 'listed';
    public const GROUPED = 'grouped';

    private IPermissionsDataProvider $_dataProvider;

    /**
     * PermissionManager constructor.
     * @param IPermissionsDataProvider $dataProvider
     */
    public function __construct(IPermissionsDataProvider $dataProvider)
    {
        $this->_dataProvider = $dataProvider;
    }

    /**
     * Получить привелегии которые не принадлежат выбранной роли.
     *
     * @param int|null $roleId
     * @return HttpResultContainer
     */
    public function getDifferentPermissions(? int $roleId) : HttpResultContainer {
        $roleId = Sanitizer::transformToInt($roleId);
        $availablePermissions = $this->_dataProvider->getAvailable($roleId);
        $allPermissions = $this->_dataProvider->getAll();
        /**
         * Step 1. Сравнение и нахождение различий.
         */
        $differentPermissions = array_diff_key($allPermissions, $availablePermissions);
        if (!empty($differentPermissions)){
            /**
             * Step 2. Формирую массив нужного вида.
             */
            $collections = [];
            foreach ($differentPermissions as $key => $value){
                $id = $value['permissionId'];
                $group = $value['groupDescription'];
                $name = $value['permissionName'];
                $description = $value['permissionDescription'];
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
            return new HttpResultContainer($collections, 200);
        }
        return new HttpResultContainer();
    }

    public function getPartly(?array $partlyDetails) : HttpResultContainer
    {
        $partlyDetails = Sanitizer::transformToInt($partlyDetails);
        /** @var int  $partlyDetails */
        $collection = $this->_dataProvider->getPartly($partlyDetails['start'], $partlyDetails['offset']);
        if (!empty($collection)){
            return new HttpResultContainer($collection, 200);
        }
        return new HttpResultContainer('Привелегий в системе не найдено', 204);
    }

    /**
     * Добавить в систему привелегии, для определенной по ID роли.
     *
     * @param int|null $roleId
     * @param array|null $permissionIDs
     * @return HttpResultContainer
     */
    public function addPermissions(? int $roleId, ? array $permissionIDs) : HttpResultContainer {
        $roleId = Sanitizer::transformToInt($roleId);
        $addingPermissionsIDs = Sanitizer::transformToInt($permissionIDs);
        $foundPermissionIDs = $this->_dataProvider->findRolePermissionsIDs($roleId, implode(', ', $addingPermissionsIDs));
        $permissionIDs = array_diff($addingPermissionsIDs, $foundPermissionIDs);
        if(!empty($permissionIDs)){
            $this->_dataProvider->add($roleId, $permissionIDs);
        }
        return new HttpResultContainer($roleId, 200);
    }

    /**
     * Удалить выбранные привелегии у определенной роли.
     *
     * @param int|null $roleId
     * @param array|null $permissionIDs
     * @return HttpResultContainer
     */
    public function deletePermissions(? int $roleId, ? array $permissionIDs) : HttpResultContainer {
        $roleId = Sanitizer::transformToInt($roleId);
        $permissionIDs = Sanitizer::transformToInt($permissionIDs);
        $permissionIDs = implode(', ', $permissionIDs);
        $this->_dataProvider->delete($roleId, $permissionIDs);
        /**
         * 204 - No Content
         */
        return new HttpResultContainer();
    }

    /**
     * Над-метод, для формирования коллекции(й) првиелегий в зависимости от mode.
     *
     * @param int|null $roleId
     * @param string $mode
     * @return array|PermissionsCollection
     */
    public function initPermissions(? int $roleId, $mode = self::LISTED) {
        Sanitizer::transformToInt($roleId);
        if ($mode === self::LISTED){
            return $this->_dataProvider->getListed($roleId);
        }else{
            return $this->_dataProvider->getGrouped($roleId);
        }
    }
}