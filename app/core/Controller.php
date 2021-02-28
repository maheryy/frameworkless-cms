<?php

namespace App\Core;

use App\Core\Utils\Session;

abstract class Controller
{
    protected $template;
    protected $view_data = [];

    protected function __construct()
    {
        Session::start();
    }

    protected function render(string $view, string $template = 'default')
    {
        new View($view, $template, $this->view_data);
    }

    protected function setParam(string $key, $value)
    {
        $this->view_data[$key] = $value;
    }

    protected function setData(array $data)
    {
        $this->view_data = !empty($this->view_data)
            ? array_merge($this->view_data, $data)
            : $data;
    }

    protected function send(string $message)
    {
        echo $message;
    }

    protected function sendJSON(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
