<?php

namespace Phoxx\Models;

use Phoxx\Core\Database\Model;

class SlideModel extends Model
{
  protected $id;

  protected $title;

  protected $filename;

  protected $extension;

  /**
   * TODO: 0 in the mapping file isn't working.
   * @var integer
   */
  protected $position = 0;

  public function getId(): int
  {
    return $this->id;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function getFileName(): string
  {
    return $this->filename;
  }

  public function getExtension(): string
  {
    return $this->extension;
  }

  public function getPosition(): string
  {
    return $this->position;
  }

  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  public function setFilename(string $filename): void
  {
    $this->filename = $filename;
  }

  public function setExtension(string $extension): void
  {
    $this->extension = $extension;
  }

  public function setPosition(int $position): void
  {
    $this->position = $position;
  }
}
