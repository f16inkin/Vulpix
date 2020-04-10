<?php

$app->pipe(\Vulpix\Engine\Middleware\ProfilerMiddleware::class);
$app->pipe(\Vulpix\Engine\Middleware\RouteMiddleware::class);