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
        # Quick monitoring
        $view_data = [
            'pages' => count($this->repository->post->findAll()),
            'users' => count($this->repository->user->findAll()),
            'roles' => count($this->repository->role->findAll()),
            'navs' => count($this->repository->navigation->findAll()),
            'debug' => $this->repository->settings->findAll(),
        ];
        $this->render('dashboard', $view_data);
    }

}
