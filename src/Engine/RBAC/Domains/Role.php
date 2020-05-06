<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


use JsonSerializable;
use Vulpix\Engine\Core\DataStructures\ExecutionResponse;
use Vulpix\Engine\Core\Utility\Sanitizer\Exceptions\WrongParamTypeException;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Класс для CRUD действий над ролями.
 *
 * Class Role
 * @package Vulpix\Engine\RBAC\Domains
 */
class Role implements JsonSerializable
{
    private $_id;
    private $_roleName;
    private $_roleDescription;
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

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param array|null $roleDetails
     * @return ExecutionResponse
     * @throws WrongParamTypeException
     */
    public function create(? array $roleDetails) : ExecutionResponse {
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
            $result = $this->_dbConnection->prepare($query);
            $result->execute([
                'roleName' => $roleDetails['roleName'],
                'roleDescription' => $roleDetails['roleDescription']
            ]);
            return (new ExecutionResponse())->setBody((int)$this->_dbConnection->lastInsertId())->setStatus(201);
        }
        return (new ExecutionResponse())->setBody($roleId)->setStatus(200);
    }

    /**
     * @param int|null $id
     * @return Role
     * @throws WrongParamTypeException
     */
    public function get(? int $id) : Role {
        $id = Sanitizer::sanitize($id);
        $query = ("SELECT * FROM `roles` WHERE `id` = :id");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'id' => $id
        ]);
        if ($result->rowCount() > 0){
            $row = $result->fetchAll();
            $this->_id = $row[0]['id'];
            $this->_roleName = $row[0]['role_name'];
            $this->_roleDescription = $row[0]['role_description'];
        }
        return $this;
    }

    /**
     * @param array|null $roleDetails
     * @return ExecutionResponse
     * @throws WrongParamTypeException
     */
    public function edit(? array $roleDetails) : ExecutionResponse  {
        $roleDetails = Sanitizer::sanitize($roleDetails);
        $query = ("UPDATE `roles` SET `role_name` = :roleName, `role_description` = :roleDescription
                WHERE `id` = :roleId");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleId' => $roleDetails['roleId'],
            'roleName' => $roleDetails['roleName'],
            'roleDescription' => $roleDetails['roleDescription']
        ]);
        return (new ExecutionResponse())->setBody((int)$roleDetails['roleId'])->setStatus(200);
    }

    /**
     * @param array|null $roleIDs
     * @return ExecutionResponse
     * @throws WrongParamTypeException
     */
    public function delete(? array $roleIDs) : ExecutionResponse {
        /**
         * Зачистить массив
         */
        $roleIDs= Sanitizer::sanitize($roleIDs);
        /**
         * Склеить строку для массового удаления
         */
        $roleIDs = implode(', ', $roleIDs);
        $query = ("DELETE FROM `roles` WHERE `id` IN ($roleIDs)");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        return (new ExecutionResponse())->setBody('Роль удалена')->setStatus(204);
    }

    public function getAll() : ExecutionResponse{
        $query = ("SELECT * FROM `roles`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        if ($result->rowCount() > 0){
            $roles = $result->fetchAll();
            return (new ExecutionResponse())->setBody($roles)->setStatus(200);
        }
        return (new ExecutionResponse())->setBody('Ролей в системе не найдено')->setStatus(204);
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
            'roleDescription' => $this->_roleDescription,
            'permissions' => $this->_permissions
        ];
    }

    /**
     * Проверка на наличие роли с таким именем (уникально) в БД
     * Если роль найдена вернет ее id иначе false
     *
     * @param string $roleName
     * @return bool|int
     */
    public function isRoleExist(string $roleName) {
        $query = ("SELECT `id` FROM `roles` WHERE `role_name` = :roleName");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleName' => $roleName
        ]);
        if ($result->rowCount() > 0){
            $roleId = $result->fetch()['id'];
            return $roleId;
        }
        return false;
    }

}
