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

    /**
     * Render a view in from any controller
     * 
     * @param string $view
     * @param string $template
     * 
     * @return void
     */
    protected function render(string $view, string $template = 'default')
    {
        new View($view, $template, $this->view_data);
    }

    /**
     * Set a view param (the variable of $key name can be used in the view)
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    protected function setParam(string $key, $value)
    {
        $this->view_data[$key] = $value;
    }

    /**
     * Set multiple view params (the variable of $key name can be used in the view)
     * 
     * @param array $data
     * 
     * @return void
     */
    protected function setData(array $data)
    {
        $this->view_data = !empty($this->view_data)
            ? array_merge($this->view_data, $data)
            : $data;
    }

    /**
     * Send back a text message
     * 
     * @param string $message
     * 
     * @return void
     */
    protected function send(string $message)
    {
        echo $message;
    }

    /**
     * Send back a JSON data 
     * 
     * @param array $data
     * 
     * @return void
     */
    protected function sendJSON(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
