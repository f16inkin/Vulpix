<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

class PermissionCollection
{
    private $_permissions = [];
    private $_dbConnector;

    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnector = $dbConnector;
    }

    public function hasPermission(string $permission) : bool{
        return isset($this->_permissions[$permission]);
    }

    public function initPermissions(int $roleId) : PermissionCollection {
        $query = ("SELECT `permission_name`, `permission_description` FROM `role_permission`
                    INNER JOIN `permissions` ON permissions.id = role_permission.permission_id
                    WHERE `role_permission`.role_id = :roleId");
        $result = $this->_dbConnector::getConnection()->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        while ($row = $result->fetch()){
            $this->_permissions[$row['permission_name']] = $row['permission_description'];
        }
        return $this;
    }

}