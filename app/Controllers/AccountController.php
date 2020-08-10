<?php

namespace Phoxx\Controllers;

use Phoxx\Models\SlideModel;
use Phoxx\Models\UserModel;

use Phoxx\Core\Controllers\Helpers\FrontController;
use Phoxx\Core\Database\EntityManager;
use Phoxx\Core\File\Exceptions\FileException;
use Phoxx\Core\File\Exceptions\ImageException;
use Phoxx\Core\File\Image;
use Phoxx\Core\File\ImageManager;
use Phoxx\Core\Http\Request;
use Phoxx\Core\Http\Response;
use Phoxx\Core\Renderer\View;
use Phoxx\Core\Session\Session;

class AccountController extends FrontController
{
  private $errors = [];

  private function verifyUser(): bool
  {
    $session = $this->getService(Session::class);
    $session->open();

    $authenticated = $session->getValue('authenticated');

    $session->close();

    return (bool)$authenticated;
  }

  private function addSlide(string $title, Image $image): void
  {
    $fileName = uniqid();
    $filePath = PATH_PUBLIC . '/assets/upload/' . $fileName;

    $imageManager = $this->getService(ImageManager::class);

    $imageManager->convert($image, $filePath . '_thumb.jpg');
    $imageManager->convert($image, $filePath . '_small.jpg');
    $imageManager->convert($image, $filePath . '_medium.jpg');
    $imageManager->convert($image, $filePath . '_large.jpg');

    $imageManager->resize(new Image($filePath . '_thumb.jpg'), 100, 100, Image::SCALE_COVER, null, 90);
    $imageManager->resize(new Image($filePath . '_small.jpg'), 480, 270, Image::SCALE_COVER, null, 90);
    $imageManager->resize(new Image($filePath . '_medium.jpg'), 1280, 720, Image::SCALE_COVER, null, 90);
    $imageManager->resize(new Image($filePath . '_large.jpg'), 1920, 1080, Image::SCALE_COVER, null, 90);

    $slide = new SlideModel();
    $slide->setTitle($title);
    $slide->setFilename($fileName);
    $slide->setExtension('jpg');

    $entityManager = $this->getService(EntityManager::class);
    $entityManager->persist($slide);
    $entityManager->flush();
  }

  private function removeSlide(int $slideId): void
  {
    $queryBuilder = $this->getService(EntityManager::class)->createQueryBuilder();
    $queryBuilder->delete(SlideModel::class, 's')
                 ->where('s.id = ?1')
                 ->setParameter(1, (int)$slideId)
                 ->getQuery()
                 ->execute();
  }

  private function updateSlides(array $positions): void
  {
    $positionIndex = 0;
    $queryBuilder = $this->getService(EntityManager::class)->createQueryBuilder();

    foreach ($positions as $slideId) {
      $queryBuilder->update(SlideModel::class, 's')
                   ->set('s.position', $positionIndex)
                   ->where('s.id = ?1')
                   ->setParameter(1, (int)$slideId)
                   ->getQuery()
                   ->execute();

      $positionIndex++;
    }
  }

  public function index(): Response
  {
    $request = $this->main();

    if ($this->verifyUser() === false) {
      return new Response('', Response::HTTP_FOUND, [
        'Location' => $request->getBasePath() . '/auth'
      ]);
    }

    $slides = $this->getService(EntityManager::class)
                   ->getRepository(SlideModel::class)
                   ->findBy([], ['position' => 'ASC']);

    return $this->render(new View('./AccountController/index', [
      'baseUrl' => $request->getUrl() . $request->getBasePath(),
      'errors' => $this->errors,
      'slides' => $slides,
      'title' => 'Account'
    ]));
  }

  public function actions(): Response
  {
    $request = $this->main();

    if ($this->verifyUser() === false) {
      return new Response('', Response::HTTP_FOUND, [
        'Location' => $request->getBasePath() . '/auth'
      ]);
    }

    if (($slideId = $request->getRequest('remove')) !== null) {
      $this->removeSlide((int)$slideId);
    }

    if (($slideId = $request->getRequest('increment')) !== null) {
      $positions = array_values((array)$request->getRequest('positions'));
      $search = array_search($slideId, $positions);

      if (isset($positions[$search - 1]) === true) {
        $replace = $positions[$search - 1];

        $positions[$search] = $replace;
        $positions[$search - 1] = $slideId;

        $this->updateSlides($positions);
      }
    }

    if (($slideId = $request->getRequest('decrement')) !== null) {
      $positions = array_values((array)$request->getRequest('positions'));
      $search = array_search($slideId, $positions);

      if (isset($positions[$search + 1]) === true) {
        $replace = $positions[$search + 1];

        $positions[$search] = $replace;
        $positions[$search + 1] = $slideId;

        $this->updateSlides($positions);
      }
    }

    if ($request->getRequest('upload') !== null) {
      $title = $request->getRequest('title');
      $image = $request->getFile('image');

      if ($title !== null && $image !== null && isset($image['tmp_name']) === true) {
        try {
          $this->addSlide((string)$title, new Image((string)$image['tmp_name']));
        } catch (ImageException | FileException $e) {
          $this->errors[] = 'Failed to process image.';
        }
      }
    }

    return $this->index();
  }
}
