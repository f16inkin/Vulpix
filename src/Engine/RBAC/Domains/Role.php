<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

class Role implements \JsonSerializable
{
    private $_id;
    private $_roleName;
    private $_rolePermission;
    /**
     * @var PermissionCollection $_permissions
     */
    private $_permissions;
    private $_dbConnection;

    /**
     * Role constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function create(string $roleName, string $roleDescription) : int {
        $query = ("INSERT INTO `roles` (role_name, role_description) VALUES (:roleName, :roleDescription)");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleName' => $roleName,
            'roleDescription' => $roleDescription
        ]);
        return (int)$this->_dbConnection->lastInsertId();
    }

    /**
     * Получить информацию по роли
     *
     * @param int $id
     * @return Role
     */
    public function read(int $id) : Role {
        $query = ("SELECT * FROM `roles` WHERE `id` = :id");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'id' => $id
        ]);
        if ($result->rowCount() > 0){
            $row = $result->fetchAll();
            $this->_id = $row[0]['id'];
            $this->_roleName = $row[0]['role_name'];
            $this->_rolePermission = $row[0]['role_description'];
        }
        return $this;
    }

    public function update(int $id){
        //обновить роль с текущим id
    }

    public function delete(int $id){
        //удалить роль по текущему id
    }

    /**
     * Получить весь список ролей приложения
     *
     * @return array
     */
    public function getAll() : array {
        $query = ("SELECT * FROM `roles`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        $roles = [];
        if ($result->rowCount() > 0){
            $roles = $result->fetchAll();
        }
        return $roles;
    }

    /**
     * ПОлучить все привелегии данной роли
     *
     * @return PermissionCollection
     */
    public function getPermissions() : PermissionCollection {
        return $this->_permissions;
    }


    /**
     * Записывает в текущую роль все ее привелегии в виде коллекции
     *
     * @param PermissionCollection $permissions
     * @return Role
     */
    public function setPermissions(PermissionCollection $permissions) : Role {
        $this->_permissions = $permissions;
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
        return [
            'id' => $this->_id,
            'roleName' => $this->_roleName,
            'rolePermission' => $this->_rolePermission
        ];
    }

    public function isROleExist(string $roleName) : bool {
        $query = ("SELECT `id` FROM `roles` WHERE `role_name` = :roleName");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleName' => $roleName
        ]);
        if ($result->rowCount() >0){
            return true;
        }
        return false;
    }

}
