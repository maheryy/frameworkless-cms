<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

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

    public function loginAction() {
    
    }
    
    public function registerAction() {
        $data = [
            'login' => 'Bonjour',
            'password' => 'bjr^wd',
            'username' => 'Bonj',
            'email' => 'yoqqsdzs@test.com',
            'role' => '4',
            'status' => '1'
        ];

        $test_user = new User();
        $test_user->populate($data);
        $test_user->save();

        $test_user->setId(33);
        $test_user->setUsername('AHAHA');
        $test_user->setEmail('noooon@test.com');
        $test_user->save();
        $this->setView('default');

    }
}