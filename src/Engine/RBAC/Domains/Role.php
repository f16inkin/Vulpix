<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use Vulpix\Engine\Database\Connectors\IConnector;

class Role
{
    /**
     * @var PermissionCollection $_permissions
     */
    private $_permissions;
    private $_dbConnection;

    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    public function create(){
        //создать новую роль
    }

    public function read(int $id){
        //получить данные о роли по ее id
    }

    public function update(int $id){
        //обновить роль с текущим id
    }

    public function delete(int $id){
        //удалить роль по текущему id
    }

    public function getPermissions() : PermissionCollection{
        return $this->_permissions;
    }

    public function setPermissions(PermissionCollection $permissions) : Role {
        $this->_permissions = $permissions;
        return $this;
    }

}