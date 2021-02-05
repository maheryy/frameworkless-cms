<?php

namespace App\Core;

abstract class Controller {
    protected $view;
    protected $view_data;

    protected function __construct() {
        
    }

    protected function setView(string $view, string $template = 'default') 
    {
        if( isset($this->view) ) {
            $this->view->setView($view);
            $this->view->setTemplate($template);
        } else {
            $this->view = new View($view, $template);
        }
    }

    protected function setData(string $key, string $value) 
    {
        $this->view_data[$key] = $value;
    }

    protected function setArrayData(array $data) 
    {
        $this->view_data = !empty($this->view_data) 
                            ? array_merge($this->view_data, $data)
                            : $data;
    }

    public function __destruct()
    {
        if( isset($this->view) ) {
            $this->view->setData($this->view_data);
        }
    }
}
