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

        $menus = $this->repository->menu->findAll();
        $default_menu_id = $this->request->get('id') ?? $menus[0]['id'] ?? -1;
        $menu_items = $this->repository->menuItem->findMenuItems($default_menu_id);
        $is_empty_table = empty($menu_items);
        $menu_data = !$is_empty_table ? ($menu_items[0] ?? null) : null;

        $view_data = [
            'pages' => $this->repository->post->findPublishedPages(),
            'menus' => $menus,
            'menu_items' => $menu_items,
            'referer' => $default_menu_id,
            'menu_data' => $menu_data,
            'menu_types' => Constants::getMenusTypes(),
            'url_form' => UrlBuilder::makeUrl('Appearance', 'navigationAction'),
            'url_delete' => !$is_empty_table ? UrlBuilder::makeUrl('Appearance', 'deleteNavigationAction', ['id' => $default_menu_id]) : null,
            'default_tab' => $default_menu_id,
            'default_tab_view' => PATH_VIEWS . 'nav_tab_default.php',
            'social_medias' => Constants::getSocialList(),
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
        $menu_id = $this->request->get('ref');
        if (!$menu_id) {
            throw new \Exception('ref ne peut pas être null');
        }
        $menu_items = [];
        if ($menu_id > 0) {
            $menu_items = $this->repository->menuItem->findMenuItems($menu_id);
            $menu_data = $menu_items[0] ?? null;
            $url_delete = UrlBuilder::makeUrl('Appearance', 'deleteNavigationAction', ['id' => $menu_id]);
        }

        $view_data = [
            'referer' => $menu_id,
            'pages' => $this->repository->post->findPublishedPages(),
            'menu_data' => $menu_data ?? null,
            'menu_types' => Constants::getMenusTypes(),
            'menu_items' => $menu_items,
            'social_medias' => Constants::getSocialList(),
            'url_form' => UrlBuilder::makeUrl('Appearance', 'navigationAction'),
            'url_delete' => $url_delete ?? null,
        ];
        $this->renderViewOnly('nav_tab_default', $view_data);
    }

    # /navigation-save
    public function navigationAction()
    {
        $this->validateCSRF();
        $menu_id = $this->request->post('ref');
        $menu_items = $this->request->post('menu_items');
//        $menu_labels = $this->request->post('menu_labels');
//        $menu_links = $this->request->post('menu_links');

        if (!$menu_id) {
            $this->sendError('Une erreur est survenue', ['menu_id' => $menu_id]);
        }
        if (!$this->request->post('menu_name')) {
            $this->sendError('Veuillez nommer le menu');
        }
        if (empty($menu_items)) {
            $this->sendError('Veuillez ajouter au moins un élément');
        }

//        $this->sendError(',qdz,', $menu_items);
        $data_length = count($menu_items['pages']);
        $labels = [];
        $i = 0;
        while ($i < $data_length) {
            if (empty($menu_items['labels'][$i])) $this->sendError('Le label d\'une page ne peut être vide.');
            if (empty($menu_items['links'][$i])) $this->sendError('Le lien ne peut être vide.');
            if (in_array($menu_items['labels'][$i], $labels)) $this->sendError('Un label doit être unique.', [$menu_items['labels'][$i], $menu_items['labels']]);

            $labels[] = $menu_items['labels'][$i];
            $items[] = [
                'menu_id' => $menu_id,
                'post_id' => !empty($menu_items['pages'][$i]) ? $menu_items['pages'][$i] : null,
                'label' => $menu_items['labels'][$i],
                'icon' => !empty($menu_items['icons'][$i]) ? $menu_items['icons'][$i] : null,
                'url' => !empty($menu_items['pages'][$i]) ? null : $menu_items['links'][$i],
            ];
            $i++;
        }
//        echo '<pre>';
//        print_r($items);
//        die();


        try {
            Database::beginTransaction();

//            if ($this->request->post('nav_active')) {
//                $this->repository->menu->setAllInactive($this->request->post('nav_type'));
//            }

            # New menu
            if ($menu_id == -1) {
                $menu_id = $this->repository->menu->create([
                    'title' => $this->request->post('menu_name'),
                    'type' => !empty($items[0]['icon']) ? Constants::MENU_SOCIALS : Constants::MENU_LINKS,
                    'status' => Constants::STATUS_ACTIVE,
                ]);
                foreach ($items as $key => $item) {
                    $items[$key]['menu_id'] = (int)$menu_id;
                }
                $success_msg = 'Un nouveau menu a été ajouté';
            } else {
                $this->repository->menuItem->deleteAllByMenu((int)$menu_id);
                $this->repository->menu->update($menu_id, [
                    'title' => $this->request->post('menu_name'),
                    'type' => !empty($items[0]['icon']) ? Constants::MENU_SOCIALS : Constants::MENU_LINKS,
                ]);
                $success_msg = 'Informations sauvegardées';
            }

//            var_dump($items);die;
//            echo '<pre>';
//            print_r($items);die;
            $this->repository->menuItem->create($items);

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('Appearance', 'navigationView', ['id' => $menu_id]),
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
        $this->repository->menu->remove($this->request->get('id'));
        $this->sendSuccess('Menu supprimée', [
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
