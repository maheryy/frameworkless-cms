<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function defaultAction() {
        $this->setView('default');
        $this->setArrayData([
            'name' => 'nonnn',
            'test' => 'bla',
            'age' => 8
        ]);
        $this->setData('test2', 'oui');
    }

    public function logInAction() {
        echo 'login';
    }
}