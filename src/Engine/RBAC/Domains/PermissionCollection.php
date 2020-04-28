<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Class PermissionCollection
 * @package Vulpix\Engine\RBAC\Domains
 */
class PermissionCollection implements \JsonSerializable
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

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->_permissions;
    }
}
