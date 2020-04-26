<?php
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

#RBAC - Permissions
$app->get('permissions.get', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsGetAction::class);
$app->post('permissions.add', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsAddAction::class);
$app->delete('permissions.delete', '/api/v1/permissions', \Vulpix\Engine\RBAC\Actions\PermissionsDeleteAction::class);

#RBAC - Roles
$app->post('role.create', '/api/v1/roles', \Vulpix\Engine\RBAC\Actions\RoleCreateAction::class);
