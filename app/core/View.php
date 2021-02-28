<?php

namespace App\Core;

class View
{
    private $view;
    private $template;
    private $data;

    public function __construct(string $view, string $template, array $data = [])
    {
        $this->setView($view);
        $this->setTemplate($template);
        $this->setData($data);
    }

    public function setView(string $view)
    {
        $view_path = PATH_VIEWS . $view . '.view.php';
        if (!file_exists($view_path)) {
            throw new \Exception('La vue ' . $view_path . ' n\'existe pas');
        }
        $this->view = $view_path;
    }

    public function setTemplate(string $template)
    {
        $template_path = PATH_TEMPLATES . $template . '.tpl.php';
        if (!file_exists($template_path)) {
            throw new \Exception('La template ' . $template_path . ' n\'existe pas');
        }
        $this->template = $template_path;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __destruct()
    {
        extract($this->data);
        include $this->template;
    }
}
