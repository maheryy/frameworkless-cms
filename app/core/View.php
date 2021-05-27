<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;

class View
{
    private string $view;
    private string $template;
    private array $data;

    public function __construct(string $view, string $template, array $data = [])
    {
        $this->setView($view);
        $this->setTemplate($template);
        $this->setData($data);
    }

    /**
     * @param string $view
     *
     * @return void
     */
    public function setView(string $view)
    {
        $view_path = PATH_VIEWS . $view . '.view.php';
        if (!file_exists($view_path)) {
            throw new NotFoundException('La vue ' . $view_path . ' n\'existe pas');
        }
        $this->view = $view_path;
    }

    /**
     * @param string $template
     *
     * @return void
     */
    public function setTemplate(string $template)
    {
        $template_path = PATH_TEMPLATES . $template . '.tpl.php';
        if (!file_exists($template_path)) {
            throw new NotFoundException('La template ' . $template_path . ' n\'existe pas');
        }
        $this->template = $template_path;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __destruct()
    {
        extract($this->data);
        include $this->template;
    }

    public static function getHtml(string $template, array $data)
    {
        $template_path = PATH_TEMPLATES . $template . '.html';
        if (!file_exists($template_path)) {
            throw new NotFoundException('Le template HTML ' . $template . ' n\'existe pas');
        }

        $html = file_get_contents($template_path);

        return !$html ? false
            : str_replace(
                array_map(fn($key) => "%{$key}%", array_keys($data)),
                array_values($data),
                $html
            );
    }
}
