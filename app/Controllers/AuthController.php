<?php

namespace Phoxx\Controllers;

use Phoxx\Models\SlideModel;
use Phoxx\Models\UserModel;

use Phoxx\Core\Controllers\FrontController;
use Phoxx\Core\Database\EntityManager;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\View;
use Phoxx\Core\Session\Session;

class AuthController extends FrontController
{
  private $errors = [];

  public function index(array $errors = []): Response
  {
    $request = $this->main();

    return $this->render(new View('./AuthController/index', [
      'baseUrl' => $request->getUrl() . $request->getBasePath(),
      'errors' => $this->errors,
      'title' => 'Auth',
      'username' => $this->main()->getRequest('username')
    ]));
  }

  public function auth(): Response
  {
    $request = $this->main();

    if ($request->getRequest('auth') !== null) {
      $username = $request->getRequest('username');
      $password = $request->getRequest('password');

      if ($username !== null && $password !== null) {
        $user = $this->getService(EntityManager::class)
                     ->getRepository(UserModel::class)
                     ->findOneBy(['username' => $username]);

        if ($user !== null && $user->verifyPassword($password)) {
          $session = $this->getService(Session::class);
          $session->open();
          $session->setValue('authenticated', true);
          $session->close();

          return new Response('', Response::HTTP_FOUND, [
            'Location' => $request->getBasePath() . '/account'
          ]);
        } else {
          $this->errors[] = 'Invalid username or password.';
        }
      }
    }

    return $this->index();
  }
}
