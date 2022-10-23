<?php

namespace Phoxx\Controllers;

use Phoxx\Models\SlideModel;

use Phoxx\Core\Controllers\FrontController;
use Phoxx\Core\Database\EntityManager;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\View;

class PortfolioController extends FrontController
{
  public function index(): Response
  {
    $request = $this->main();
    $slides = $this->getService(EntityManager::class)
                   ->getRepository(SlideModel::class)
                   ->findBy([], ['position' => 'ASC']);

    return $this->render(new View('./PortfolioController/index', [
      'baseUrl' => $request->getUrl() . $request->getBasePath(),
      'slides' => $slides,
      'title' => 'Portfolio'
    ]));
  }

  public function pageNotFound(): Response
  {
    $request = $this->main();

    return $this->render(new View('./PortfolioController/pageNotFound', [
      'baseUrl' => $request->getUrl() . $request->getBasePath(),
      'title' => '404'
    ]), Response::HTTP_NOT_FOUND);
  }

  public function internalServerError(): Response
  {
    $request = $this->main();

    return $this->render(new View('./PortfolioController/internalServerError', [
      'baseUrl' => $request->getUrl() . $request->getBasePath(),
      'title' => '500'
    ]), Response::HTTP_INTERNAL_SERVER_ERROR);
  }
}
