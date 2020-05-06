<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;

use Vulpix\Engine\Core\Utility\Sanitizer\Exceptions\WrongParamTypeException;
use Vulpix\Engine\Core\Utility\Sanitizer\Sanitizer;
use Vulpix\Engine\Database\Connectors\IConnector;

/**
 * Класс для работы с привелегиями
 *
 * Class Permission
 * @package Vulpix\Engine\RBAC\Domains
 */
class Permission
{
    private $_dbConnection;

    /**
     * Permission constructor.
     * @param IConnector $dbConnector
     */
    public function __construct(IConnector $dbConnector)
    {
        $this->_dbConnection = $dbConnector::getConnection();
    }

    /**
     * Найти привелегии для текущей роли
     *
     * @param int $roleId
     * @return array
     * @throws WrongParamTypeException
     */
    public function getByRole(? int $roleId) : array {
        $roleId = Sanitizer::sanitize($roleId);
        $query = ("SELECT `permissions`.`id` as `id`, `permission_name`, `permission_description` FROM `permissions`
                    INNER JOIN `role_permission` ON `permissions`.`id` = `role_permission`.`permission_id`
                    WHERE `role_permission`.role_id = :roleId");
        $result = $this->_dbConnection->prepare($query);
        $result->execute([
            'roleId' => $roleId
        ]);
        $permissions = [];
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $permissions[$row['permission_name']] = $row['permission_description'];
            }
        }
        return $permissions;
    }

    /**
     * Вернуть все привелегии
     *
     * @return array
     */
    public function getAll() : array {
        $query = ("SELECT * FROM `permissions`");
        $result = $this->_dbConnection->prepare($query);
        $result->execute();
        if ($result->rowCount() > 0){
            while ($row = $result->fetch()){
                $permissions[$row['permission_name']] = $row['permission_description'];
            }
        }
        return $permissions;
    }

}
