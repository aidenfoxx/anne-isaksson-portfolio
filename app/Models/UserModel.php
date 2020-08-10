<?php

namespace Phoxx\Models;

use Phoxx\Core\Database\Model;

class UserModel extends Model
{
  protected $id;

  protected $email;

  protected $username;

  protected $password;

  public function getId(): int
  {
    return $this->id;
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function verifyPassword(string $password): bool
  {
    return password_verify($password, $this->password);
  }
}
