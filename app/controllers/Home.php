<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
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

}
