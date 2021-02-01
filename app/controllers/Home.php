<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller {

    public function __construct() 
    {

    }

    public function defaultAction()
    {
        echo 'default';
    }

    public function loginAction()
    {
        echo 'login';
    }
}