<?php

namespace Phoxx\Controllers;

use Phoxx\Models\SlideModel;
use Phoxx\Models\UserModel;

use Phoxx\Core\Controllers\FrontController;
use Phoxx\Core\Database\EntityManager;
use Phoxx\Core\Exceptions\FileException;
use Phoxx\Core\Exceptions\ImageException;
use Phoxx\Core\File\Image;
use Phoxx\Core\File\FileManager;
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

    $fileManager = $this->getService(FileManager::class);

    $fileManager->convert($image, $filePath . '_thumb.webp', Image::FORMAT_WEBP, null, 90);
    $fileManager->convert($image, $filePath . '_small.webp', Image::FORMAT_WEBP, null, 90);
    $fileManager->convert($image, $filePath . '_medium.webp', Image::FORMAT_WEBP, null, 90);
    $fileManager->convert($image, $filePath . '_large.webp', Image::FORMAT_WEBP, null, 90);

    $fileManager->resize(new Image($filePath . '_thumb.webp'), 100, 100, Image::SCALE_COVER, null, 90);
    $fileManager->resize(new Image($filePath . '_small.webp'), 480, 270, Image::SCALE_COVER, null, 90);
    $fileManager->resize(new Image($filePath . '_medium.webp'), 1280, 720, Image::SCALE_COVER, null, 90);
    $fileManager->resize(new Image($filePath . '_large.webp'), 1920, 1080, Image::SCALE_COVER, null, 90);

    $slide = new SlideModel();
    $slide->setTitle($title);
    $slide->setFilename($fileName);
    $slide->setExtension('webp');

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
          error_log($e);
          $this->errors[] = 'Failed to process image.';
        }
      }
    }

    return $this->index();
  }
}
