<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Seeds;

class Home extends Controller
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    public function defaultView()
    {
        $seeds = Seeds::getAvailableSeeds();
        foreach ($seeds as $seed) {
            $this->repository->{$seed}->runSeed();
        }

        $view_data = [
            'name' => 'John',
            'age' => 25,
            'test' => '25/12/2015',
            'test2' => PHP_INT_MAX
        ];
        $this->render('default', $view_data);
    }

}
