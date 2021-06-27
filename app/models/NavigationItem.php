<?php

namespace App\Models;

use App\Core\Model;

class NavigationItem extends Model
{
    private $id;

    public function __construct()
    {
        parent::__construct();
    }

    public function setId($id)
    {
        $this->id = $id;
        $this->hydrate();
    }

    public function getId()
    {
        return $this->id;
    }

}
