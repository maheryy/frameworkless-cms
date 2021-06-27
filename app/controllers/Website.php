<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\HttpNotFoundException;


class Website extends Controller
{
    private string $uri;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->uri = ltrim($this->router->getUri(), '/');
        $this->setTemplate('website');
    }


    public function display()
    {
        $page = $this->repository->post->findPageBySlug($this->uri);
        if (!$page) {
            throw new HttpNotFoundException($this->uri);
        }

        $view_data = [
            'meta_title' => $page['meta_title'],
            'meta_description' => $page['meta_description'],
            'is_indexable' => $page['meta_indexable'],
            'content_title' => $page['title'],
            'content' => $page['content']
        ];
        $this->render('website_default', $view_data);
    }

    public function redirect()
    {
        echo 'redirect';
    }

    public function sendAction()
    {
        echo 'sendAction';
    }
}
