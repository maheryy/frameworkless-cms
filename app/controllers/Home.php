<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\UrlBuilder;

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


    private function getUserPublishedPages()
    {
        $pages = $this->repository->post->findPublishedPagesByAuthor($this->session->getUserId());
        $res = [];
        foreach ($pages as $key => $page) {
            $res[$key]['title'] = $page['title'];
            $res[$key]['link_edit'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $res[$key]['link'] = $page['slug'];
        }
        return $res;
    }

    private function getTraffic()
    {
        $res = $this->repository->visitor->findUniqueVisitorsPerDay(date('Y-m-d', strtotime('-5 days')));
        return $res;
    }

    private function getPopularPages()
    {
        $res = $this->repository->visitor->findVisitorsPerPages(5);
        return $res;
    }

    private function getQuickLinks()
    {
        return [
            [
                'icon' => '',
                'label' => 'Ajouter une page',
                'link' => '#',
            ],
            [
                'icon' => '',
                'label' => 'Ajouter un menu',
                'link' => '#',
            ],
            [
                'icon' => '',
                'label' => 'Personnaliser mon site',
                'link' => '#',
            ],
            [
                'icon' => '',
                'label' => '',
                'link' => '#',
            ],
        ];
    }


}
