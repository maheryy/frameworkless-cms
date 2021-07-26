<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\UrlBuilder;

class Home extends Controller
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    public function dashboardView()
    {
        $view_data = [
            'user_pages' => $this->getUserPublishedPages(),
            'traffic' => $this->getTraffic(),
            'popular_pages' => $this->getPopularPages(),
            'latest_reviews' => $this->getLatestReviews(),
            'quick_links' => $this->getQuickLinks(),
            'can_edit_page' => $this->hasPermission(Constants::PERM_UPDATE_PAGE),
            'username' => $this->repository->user->find($this->session->getUserId())['username'],
            'count_visitors' => $this->repository->visitor->countTotalUniqueVisitors(),
            'count_pages' => $this->getTotalPages(),
            'count_pending_reviews' => count($this->repository->review->findAllPending()),
            'link_all_pages' => UrlBuilder::makeUrl('Page', 'listView'),
            'link_all_reviews' => UrlBuilder::makeUrl('Review', 'listView')
        ];
        $this->render('dashboard', $view_data);
    }


    private function getTotalPages()
    {
        $pages = $this->repository->post->findAllPages();
        $published = $draft = $total = 0;
        foreach ($pages as $page) {
            if ($page['status'] == Constants::STATUS_PUBLISHED) $published++;
            elseif ($page['status'] == Constants::STATUS_DRAFT) $draft++;
            $total++;
        }

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
        ];
    }

    private function getUserPublishedPages()
    {
        $pages = $this->repository->post->findPublishedPagesByAuthor($this->session->getUserId(), 5);
        $res = [];
        foreach ($pages as $key => $page) {
            $res[$key]['title'] = $page['title'];
            $res[$key]['link_edit'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $res[$key]['link'] = $page['slug'];
        }
        return $res;
    }

    private function getLatestReviews()
    {
        $res = $this->repository->review->findAllPending(5);
        return $res;
    }

    private function getTraffic()
    {
        $visitors = $this->repository->visitor->findUniqueVisitorsPerDay(date('Y-m-d', strtotime('-5 days')));

        $res = [];
        foreach ($visitors as $item) {
            $res['x_axis'][] = Formatter::getDateTime($item['date'], Formatter::DATE_DISPLAY_FORMAT);
            $res['y_axis'][] = (int)$item['count'];
        }
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
                'label' => 'Voir mon site',
                'icon' => '',
                'link' => '/',
            ],
            [
                'label' => 'Personnaliser mon site',
                'icon' => '',
                'link' => UrlBuilder::makeUrl('Appearance', 'customizationView'),
            ],
            [
                'label' => 'Ajouter une page',
                'icon' => '',
                'link' => UrlBuilder::makeUrl('Page', 'createView'),
            ],
            [
                'label' => 'Ajouter un menu',
                'icon' => '',
                'link' => UrlBuilder::makeUrl('Appearance', 'menuView'),
            ],
        ];
    }


}
