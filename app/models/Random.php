<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Random extends Model
{
    private $id;
    protected $name;
    protected $password;
    protected $email;
    protected $state;
    protected $status;

    public function setId($id)
    {
        $this->id = $id;
        $this->hydrate();
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }
    public function setState($state)
    {
        $this->state = $state;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getState()
    {
        return $this->state;
    }
    public function getStatus()
    {
        return $this->status;
    }


    public function __construct()
    {
        parent::__construct();
    }
}
