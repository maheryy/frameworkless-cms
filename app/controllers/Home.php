<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->initSession();
        $this->setLayoutParams();
    }

    public function defaultView()
    {
        $this->setPageTitle('Super CMS');
        $this->setContentTitle('CMS');
        $this->setData([
            'name' => 'John',
            'age' => 25,
            'test' => '25/12/2015',
            'test2' => PHP_INT_MAX   
         
        ]);
        $this->render('default', 'default');
    }

    public function loginAction()
    {
    }

    public function logoutAction()
    {
    }

    public function registerView()
    {
    }
}
