<?php

declare(strict_types = 1);

namespace Vulpix\Engine\RBAC\Service;


use Vulpix\Engine\RBAC\Domains\Permissions\PermissionsCollection;
use Vulpix\Engine\RBAC\Domains\Roles\RolesCollection;

class PermissionVerificator
{
    public static function verify(RolesCollection $roles, string $permission) : bool {
        foreach ($roles as $role){
            /**
             * @var PermissionsCollection
             */
            if (($role->getPermissions())->offsetExists($permission)){
                return true;
            }
        }
        return false;
    }
}