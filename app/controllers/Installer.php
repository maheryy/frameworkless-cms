<?php

namespace App\Controllers;

use App\Core\Controller;

class Installer extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    # /installer
    public function installerView()
    {
        $this->setData([

        ]);
        $this->render('installer', 'installer');
    }

    # /installer-load
    public function installerAction()
    {

    }

    # /installer/test
    public function testAction()
    {

    }
}
