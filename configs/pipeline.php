<?php

$app->pipe(\Vulpix\Engine\Core\Middleware\ProfilerMiddleware::class);
$app->pipe(\Vulpix\Engine\Core\Middleware\MemoryUsageMiddleware::class);
$app->pipe(\Vulpix\Engine\Core\Middleware\RouterMiddleware::class);
$app->pipe(\Vulpix\Engine\RBAC\Middleware\InitRolesMiddleware::class);
$app->pipe(\Vulpix\Engine\Core\Middleware\DispatcherMiddleware::class);