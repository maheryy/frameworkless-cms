<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\HttpNotFoundException;
use App\Core\Utils\Formatter;


class Website extends Controller
{
    private string $uri;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->uri = $this->router->getUri();
        $this->setTemplate('website');
    }

    private function setNewVisitor(?string $ip, ?string $agent, string $uri, string $date)
    {
        if (!$this->repository->visitor->findUniqueVisitor($ip, $uri, $date)) {
            $this->repository->visitor->create([
                'ip' => $ip ?? '',
                'agent' => $agent ?? '',
                'uri' => $uri,
                'date' => $date,
            ]);
        }
    }

    public function display()
    {
        $page = $this->repository->post->findPageBySlug('/first-page');
//        $page = $this->repository->post->findPageBySlug($this->uri);
        if (!$page) {
            throw new HttpNotFoundException($this->uri);
        }
        $this->setNewVisitor($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $this->uri, date('Y-m-d'));

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
