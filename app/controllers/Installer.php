<?php

namespace App\Controllers;

use App\Core\Controller;

class Installer extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    # /installer-register
    public function installerRegisterView()
    {
        $this->setData([

        ]);
        $this->render('installer_register', 'installer');
    }

    # /installer-db
    public function installerDbView()
    {
        $this->setData([

        ]);
        $this->render('installer_db', 'installer');
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
