<?php
/**
 * Examples of the routes
 *
 * $app->get('card.get', '/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class)->tokens(['id' => '\d+']);
 * $app->route('card.get', '/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class, 'GET')->tokens(['id' => '\d+']);
 */

$app->get('card.get', '/api/v1/patient-cards/{id}', \Vulpix\Application\PatientCard\Card\Actions\CardGetAction::class)->tokens(['id' => '\d+']);
$app->post('authenticate', '/authenticate', \Vulpix\Engine\AAIS\Actions\AuthenticateAction::class);


