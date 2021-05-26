<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class User extends Model
{
    private $id;
    protected $login;
    protected $password;
    protected $username;
    protected $email;
    protected $role;
    protected $status;

    protected $createdAt;
    protected $updatedAt;

    public function setId($id)
    {
        $this->id = $id;
        $this->hydrate();
    }
    public function setLogin($login)
    {
        $this->login = $login;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setRole($role)
    {
        $this->role = $role;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getLogin()
    {
        return $this->login;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    public function __construct()
    {
        parent::__construct();
    }
}
