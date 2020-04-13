<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

class RoleCollection
{
    private $_roles = [];
    private $_dbConnector;

    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    /**
     * @return array
     */
    public function getRoles(){
        return $this->_roles;
    }

    /**
     * Инициализация ролей выбранного пользователя
     *
     * @param int $userId
     * @return $this
     */
    public function initRoles(int $userId) : RoleCollection {
        $query = ("SELECT `roles`.`id`, `role_name`, `role_description` FROM `user_role`
                    INNER JOIN `roles` ON `roles`.`id` = `user_role`.role_id
                    WHERE `user_role`.user_id = :userId");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'userId' => $userId
        ]);
        while ($row = $result->fetch()){
            $permissionCollection = (new PermissionCollection($this->_dbConnector))->initPermissions($row['id']);
            $role = (new Role($this->_dbConnector))->setPermissions($permissionCollection);
            $this->_roles[] = $role;
        }
        return $this;
    }
}