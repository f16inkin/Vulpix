<?php
/** @noinspection PhpParamsInspection */
/**
 * Examples of the routes
 *
 * $app->get('card.get', '/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class)->tokens(['id' => '\d+']);
 * $app->route('card.get', '/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class, 'GET')->tokens(['id' => '\d+']);
 */

$app->get('card.get', '/api/v1/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class)->tokens(['id' => '\d+']);

#AAIS - Tokens
$app->post('authenticate', '/auth/doAuth', \Vulpix\Engine\AAIS\Actions\AuthenticateAction::class);
$app->post('refresh', '/auth/doRefresh', \Vulpix\Engine\AAIS\Actions\RefreshAction::class);

#AAIS - Accounts
$app->get('account.get', '/api/v1/accounts/{id}', \Vulpix\Engine\AAIS\Actions\AccountGetAction::class)->tokens(['id' => '\d+']);
$app->get('accounts.get', '/api/v1/accounts', \Vulpix\Engine\AAIS\Actions\AccountsGetAction::class);
$app->post('account.create', '/api/v1/accounts', \Vulpix\Engine\AAIS\Actions\AccountCreateAction::class);
$app->put('account.edit', '/api/v1/accounts', \Vulpix\Engine\AAIS\Actions\AccountEditAction::class);
$app->delete('accounts.delete', '/api/v1/accounts', \Vulpix\Engine\AAIS\Actions\AccountDeleteAction::class);

#AAIS - Passwords
$app->put('account.password.change', '/api/v1/accounts/password/change', \Vulpix\Engine\AAIS\Actions\AccountPasswordChangeAction::class);
$app->put('account.password.reset', '/api/v1/accounts/password/reset', \Vulpix\Engine\AAIS\Actions\AccountPasswordResetAction::class);

#RBAC - Permissions
$app->get('permissions.get.different', '/api/v1/permissions/different', \Vulpix\Engine\RBAC\Actions\PermissionsGetDiffAction::class);
$app->get('permissions.get.all', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsGetAction::class);
$app->post('permissions.add', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsAddAction::class);
$app->delete('permissions.delete', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsDeleteAction::class);

#RBAC - Roles
$app->get('role.get', '/api/v1/roles/{id}', \Vulpix\Engine\RBAC\Actions\RoleGetAction::class)->tokens(['id' => '\d+']);
$app->get('roles.get', '/api/v1/roles', \Vulpix\Engine\RBAC\Actions\RolesGetAction::class);
$app->post('role.create', '/api/v1/roles', \Vulpix\Engine\RBAC\Actions\RoleCreateAction::class);
$app->put('role.edit', '/api/v1/roles', \Vulpix\Engine\RBAC\Actions\RoleEditAction::class);
$app->delete('roles.delete', '/api/v1/roles', \Vulpix\Engine\RBAC\Actions\RoleDeleteAction::class);
