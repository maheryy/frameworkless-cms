<?php

namespace App\Controllers;

use App\Core\Controller;


class Website extends Controller
{

    public function __construct(array $options = [])
    {

        parent::__construct($options);
    }


    public function display()
    {
        
        echo 'display';
    }

    public function redirect()
    {
        echo 'redirect';
    }

    public function sendAction()
    {
        echo 'sendAction';
    }
}
