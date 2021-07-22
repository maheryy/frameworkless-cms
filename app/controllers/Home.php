<?php

namespace App\Controllers;

use App\Core\Controller;

class Home extends Controller
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    public function dashboardView()
    {

        # Quick monitoring
        $view_data = [
            'pages' => count($this->repository->post->findAll()),
            'users' => count($this->repository->user->findAll()),
            'roles' => count($this->repository->role->findAll()),
            'menus' => count($this->repository->menu->findAll()),
            'debug' => $this->repository->settings->findAll(),
            'visitors' => $this->repository->visitor->countTotalUniqueVisitors(),
        ];
        $this->render('dashboard', $view_data);
    }

}
