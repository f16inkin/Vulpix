<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Domains;


class PermissionVerificator
{
    public static function verify(RoleCollection $roles, string $permission) : bool {
        foreach ($roles->getRoles() as $role){
            /**
             * @var Role $role
             */
            if (($role->getPermissions())->hasPermission($permission)){
                return true;
            }
        }
        return false;
    }
}