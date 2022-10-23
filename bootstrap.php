<?php

use Phoxx\Core\Http\Route;
use Phoxx\Core\Http\Router;
use Phoxx\Core\System\Config;
use Phoxx\Core\System\Services;

if (!function_exists('register_bootstrap')) {
  return;
}

// Bootstrap application
register_bootstrap(function (Router $router, Services $services) {
  $config = $services->getService(Config::class);

  // Load and register routes from config
  // TODO: Validate
  foreach ($config->open('routes') as $method => $routes) {
    foreach ($routes as $pattern => $action) {
      $router->addRoute(new Route($pattern, $action, $method));
    }
  }
});
