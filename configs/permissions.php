<?php
/**
 * Конфигурация содаржит в себе защищаемые от непривелегированного доступа части системы (контролеры).
 */

return [
    #AAIS - Accounts
    \Vulpix\Engine\AAIS\Actions\AccountGetAction::class => ['permission' => 'AAIS_ACCOUNT_GET', 'deny_message' => 'Access denied. Вам запререщено просматривать информацию аккаунта'],
    \Vulpix\Engine\AAIS\Actions\AccountsGetAction::class => ['permission' => 'AAIS_ACCOUNTS_GET', 'deny_message' => 'Access denied. Вам запререщено просматривать информацию об аккаунтах'],
    \Vulpix\Engine\AAIS\Actions\AccountCreateAction::class => ['permission' => 'AAIS_ACCOUNTS_CREATE', 'deny_message' => 'Access denied. Вам запререщено создавать учетную запись'],
    \Vulpix\Engine\AAIS\Actions\AccountEditAction::class => ['permission' => 'AAIS_ACCOUNTS_EDIT', 'deny_message' => 'Access denied. Вам запререщено редактировать учетную запись'],
    \Vulpix\Engine\AAIS\Actions\AccountDeleteAction::class => ['permission' => 'AAIS_ACCOUNTS_DELETE', 'deny_message' => 'Access denied. Вам запререщено удалять учетную запись'],

    #AAIS - Passwords
    \Vulpix\Engine\AAIS\Actions\AccountPasswordChangeAction::class => ['permission' => 'AAIS_ACCOUNT_PASSWORD_CHANGE', 'deny_message' => 'Access denied. Вам запререщено менять пароль учетной записи'],
    \Vulpix\Engine\AAIS\Actions\AccountPasswordResetAction::class => ['permission' => 'AAIS_ACCOUNT_PASSWORD_RESET', 'deny_message' => 'Access denied. Вам запререщено сбрасывать пароль учетной записи'],
    #RBAC - Permissions
    \Vulpix\Engine\RBAC\Actions\PermissionsAddAction::class => ['permission' => 'RBAC_PERMISSIONS_ADD', 'deny_message' => 'Access denied. Вам запрещено добавлять привелегии ролям'],
    \Vulpix\Engine\RBAC\Actions\PermissionsDeleteAction::class => ['permission' => 'RBAC_PERMISSIONS_DELETE', 'deny_message' => 'Access denied. Вам запрещено удалять привелегии у ролей'],
    \Vulpix\Engine\RBAC\Actions\PermissionsGetAction::class => ['permission' => 'RBAC_PERMISSIONS_GET_ALL', 'deny_message' => 'Access denied. Вам запрещено просматривать список привелегий'],
    \Vulpix\Engine\RBAC\Actions\PermissionsGetDiffAction::class => ['permission' => 'RBAC_PERMISSIONS_GET_DIFFERENT', 'deny_message' => 'Access denied. Вам запрещено просматривать список привелегий'],
    #RBAC - Roles
    \Vulpix\Engine\RBAC\Actions\RoleCreateAction::class => ['permission' => 'RBAC_ROLES_CREATE' , 'deny_message' => 'Access denied. Вам запрещено создавать роли'],
    \Vulpix\Engine\RBAC\Actions\RoleEditAction::class => ['permission' => 'RBAC_ROLES_EDIT' , 'deny_message' => 'Access denied. Вам запрещено редактировать роли'],
    \Vulpix\Engine\RBAC\Actions\RoleDeleteAction::class => ['permission' => 'RBAC_ROLES_DELETE' , 'deny_message' => 'Access denied. Вам запрещено удалять роли'],
    \Vulpix\Engine\RBAC\Actions\RoleGetAction::class => ['permission' => 'RBAC_ROLE_GET' , 'deny_message' => 'Access denied. Вам запрещено просматривать роли'],
    \Vulpix\Engine\RBAC\Actions\RolesGetAction::class => ['permission' => 'RBAC_ROLES_GET_ALL' , 'deny_message' => 'Access denied. Вам запрещено просматривать роли'],
];