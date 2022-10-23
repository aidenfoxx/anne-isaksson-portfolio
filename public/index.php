<?php

/**
 * Phoxx MVC - Init.
 *
 * @package phoxx-mvc
 * @author  Aiden Foxx <aiden@foxx.io>
 */
use Phoxx\Core\Exceptions\ResponseException;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Http\Router;
use Phoxx\Core\Http\ServerRequest;
use Phoxx\Core\System\Services;

define('PATH_PUBLIC', __DIR__);
define('PATH_BASE', realpath(__DIR__ . '/..'));
define('PATH_CACHE', realpath(PATH_BASE . '/cache'));
define('PATH_VENDOR', realpath(PATH_BASE . '/vendor'));

/**
 * Register application.
 */
$bootstrap = [];

function register_bootstrap(callable $callback) {
  global $bootstrap;
  $bootstrap[] = $callback;
}

require PATH_VENDOR . '/autoload.php';

/**
 * Initialize application.
 */
function send_response(Response $response) {
  if (headers_sent()) {
    throw new ResponseException('Response headers already sent.');
  }

  http_response_code($response->getStatus());

  foreach ($response->getHeaders() as $name => $value) {
    header($name . ': ' . $value);
  }

  echo $response->getContent();
}

$services = new Services();
$router = new Router($services);

error_reporting(0);

set_exception_handler(function (Throwable $e) use ($router) {
  error_log($e);

  if (($response = $router->dispatch(new ServerRequest('_500_'))) instanceof Response) {
    send_response($response);
    exit;
  }

  http_response_code(500);

  echo '<h1>Error 500</h1><p>An unknown error has occured.</p>';
});

foreach ($bootstrap as $callback) {
  $callback($router, $services);
}

if (($response = $router->dispatch(new ServerRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']))) instanceof Response) {
  send_response($response);
  exit;
}

if (($response = $router->dispatch(new ServerRequest('_404_'))) instanceof Response) {
  send_response($response);
  exit;
}

http_response_code(404);

echo '<h1>Error 404</h1><p>The requested page could not be found.</p>';
