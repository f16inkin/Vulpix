<?php

$app->pipe(\Vulpix\Engine\Middleware\ProfilerMiddleware::class);
$app->pipe(\Vulpix\Engine\Middleware\MemoryUsageMiddleware::class);
$app->pipe(\Vulpix\Engine\Middleware\RouterMiddleware::class);
$app->pipe(\Vulpix\Engine\Middleware\DispatcherMiddleware::class);