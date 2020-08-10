<?php

use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Router\Route;
use Phoxx\Core\Router\RouteContainer;
use Phoxx\Core\Utilities\Config;

if (!function_exists('register_bootstrap')) {
  return;
}

/**
* Bootstrap application.
*/
register_bootstrap(function (RouteContainer $routeContainer, ServiceContainer $serviceContainer) {
  $config = $serviceContainer->getService(Config::class);

  /**
   * Load and register routes from config.
   */
  foreach ($config->open('routes') as $method => $routes) {
    foreach ($routes as $pattern => $action) {
      $routeContainer->setRoute(new Route($pattern, $action, $method));
    }
  }
});
