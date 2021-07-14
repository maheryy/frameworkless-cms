<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    public function defaultView()
    {
        $res = $this->repository->settings->findAll();
        echo '<pre>';
        print_r($res);
        echo '</pre>';

        $view_data = [
            'name' => 'John',
            'age' => 25,
            'test' => '25/12/2015',
            'test2' => PHP_INT_MAX
        ];
        $this->render('default', $view_data);
    }

}
