<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Utils\Constants;
use App\Core\Utils\UrlBuilder;

class Appearance extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /navigation
    public function navigationView()
    {
        $this->setCSRFToken();

        $navs = $this->repository->navigation->findAll();
        $default_nav_id = $this->request->get('id') ?? $navs[0]['id'] ?? -1;
        $nav_items = $this->repository->navigationItem->findNavigationItems($default_nav_id);
        $is_empty_table = empty($navs);

        $view_data = [
            'pages' => $this->repository->post->findPublishedPages(),
            'navs' => $navs,
            'nav_items' => $nav_items,
            'referer' => !$is_empty_table ? $default_nav_id : -1,
            'nav_data' => !$is_empty_table ? $nav_items[0] : null,
            'nav_types' => Constants::getNavigationTypes(),
            'url_form' => UrlBuilder::makeUrl('Appearance', 'navigationAction'),
            'url_delete' => !$is_empty_table ? UrlBuilder::makeUrl('Appearance', 'deleteNavigationAction', ['id' => $default_nav_id]) : null,
            'default_tab' => $default_nav_id,
            'default_tab_view' => PATH_VIEWS . 'nav_tab_default.php',
            'tab_options' => [
                'url_tab_view' => UrlBuilder::makeUrl('Appearance', 'navigationTabView'),
                'container_id' => 'tab-content'
            ]
        ];
        $this->render('nav_default', $view_data);
    }

    # /navigation-tab
    public function navigationTabView()
    {
        $nav_id = $this->request->get('ref');
        if (!$nav_id) {
            throw new \Exception('ref ne peut pas être null');
        }
        $nav_items = [];
        if ($nav_id > 0) {
            $nav_items = $this->repository->navigationItem->findNavigationItems($nav_id);
            $nav_data = $nav_items[0];
            $url_delete = UrlBuilder::makeUrl('Appearance', 'deleteNavigationAction', ['id' => $nav_id]);
        }

        $view_data = [
            'referer' => $nav_id,
            'pages' => $this->repository->post->findPublishedPages(),
            'nav_data' => $nav_data ?? null,
            'nav_types' => Constants::getNavigationTypes(),
            'nav_items' => $nav_items,
            'url_form' => UrlBuilder::makeUrl('Appearance', 'navigationAction'),
            'url_delete' => $url_delete ?? null,
        ];
        $this->renderViewOnly('nav_tab_default', $view_data);
    }

    # /navigation-save
    public function navigationAction()
    {
        $this->validateCSRF();
        $nav_id = $this->request->post('ref');
        $nav_items = $this->request->post('nav_items');
        $nav_labels = $this->request->post('nav_labels');

        if (!$nav_id) {
            $this->sendError('Une erreur est survenue', ['nav_id' => $nav_id]);
        }
        if (!$this->request->post('nav_name')) {
            $this->sendError('Veuillez nommer la navigation');
        }
        if (empty($nav_items)) {
            $this->sendError('Veuillez ajouter au moins une page');
        }


        $items = [];
        foreach ($nav_items as $key => $item) {
            if (empty($nav_labels[$key])) $this->sendError('Le label d\'une page ne peut être vide.');
            $items[] = ['navigation_id' => $nav_id, 'post_id' => (int)$item, 'label' => $nav_labels[$key]];
        }

        try {
            Database::beginTransaction();

            if ($this->request->post('nav_active')) {
                $this->repository->navigation->setAllInactive($this->request->post('nav_type'));
            }

            # New navigation
            if ($nav_id == -1) {
                $nav_id = $this->repository->navigation->create([
                    'title' => $this->request->post('nav_name'),
                    'type' => $this->request->post('nav_type'),
                    'status' => $this->request->post('nav_active') ? Constants::STATUS_ACTIVE : Constants::STATUS_INACTIVE,
                ]);
                foreach ($items as $key => $item) {
                    $items[$key]['navigation_id'] = $nav_id;
                }
                $success_msg = 'Une nouvelle navigation a été ajouté';
            } else {
                $this->repository->navigationItem->deleteAllByNavigation((int)$nav_id);
                $this->repository->navigation->update($nav_id, [
                    'title' => $this->request->post('nav_name'),
                    'type' => $this->request->post('nav_type'),
                    'status' => $this->request->post('nav_active') ? Constants::STATUS_ACTIVE : Constants::STATUS_INACTIVE,
                ]);
                $success_msg = 'Informations sauvegardées';
            }


            $this->repository->navigationItem->create($items);

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('Appearance', 'navigationView', ['id' => $nav_id]),
                'url_next_delay' => 1
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }

    # /delete-navigation
    public function deleteNavigationAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }
        $this->repository->navigation->remove($this->request->get('id'));
        $this->sendSuccess('Navigation supprimée', [
            'url_next' => UrlBuilder::makeUrl('Appearance', 'navigationView'),
            'delay_url_next' => 0,
        ]);
    }

    # /themes
    public function themeListView()
    {

    }

    # /import-theme
    public function importThemeView()
    {

    }

    # /import-theme-save
    public function importThemeAction()
    {

    }
}
