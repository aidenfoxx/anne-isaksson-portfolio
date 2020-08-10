<?php

/**
 * Phoxx MVC - Init.
 *
 * @package phoxx-mvc
 * @author  Aiden Foxx <aiden@foxx.io>
 */
use Phoxx\Core\Framework\ServiceContainer;
use Phoxx\Core\Http\Dispatcher;
use Phoxx\Core\Http\Helpers\SimpleRequest;
use Phoxx\Core\Http\RequestStack;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Router\RouteContainer;

define('PATH_BASE', realpath(__DIR__.'/..'));
define('PATH_CACHE', realpath(PATH_BASE.'/cache'));
define('PATH_VENDOR', realpath(PATH_BASE.'/vendor'));
define('PATH_PUBLIC', realpath(PATH_BASE.'/public'));

/**
 * Register application.
 */
$bootstrap = [];

function register_bootstrap(callable $callback) {
  global $bootstrap;
  $bootstrap[] = $callback;
}

require PATH_VENDOR.'/autoload.php';

/**
 * Initialize application.
 */
$routeContainer = new RouteContainer();
$serviceContainer = new ServiceContainer();
$dispatcher = new Dispatcher($routeContainer, $serviceContainer, new RequestStack());

error_reporting(0);

set_exception_handler(function () use ($dispatcher) {
  http_response_code(500);

  if (($response = $dispatcher->dispatch(new SimpleRequest('_500_'))) instanceof Response) {
    $dispatcher->send($response);
    exit;
  }

  echo '<h1>Error 500</h1><p>An unknown error has occured.</p>';
});

foreach ($bootstrap as $callback) {
  $callback($routeContainer, $serviceContainer);
}

/**
 * Dispatch request.
 */
if (($response = $dispatcher->dispatch(new SimpleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']))) instanceof Response) {
  $dispatcher->send($response);
  exit;
}

http_response_code(404);

if (($response = $dispatcher->dispatch(new SimpleRequest('_404_'))) instanceof Response) {
  $dispatcher->send($response);
  exit;
}

echo '<h1>Error 404</h1><p>The requested page could not be found.</p>';
