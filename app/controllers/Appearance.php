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

    # /menu
    public function menuView()
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
            'url_form' => UrlBuilder::makeUrl('Appearance', 'menuAction'),
            'url_delete' => !$is_empty_table ? UrlBuilder::makeUrl('Appearance', 'deleteMenuAction', ['id' => $default_menu_id]) : null,
            'default_tab' => $default_menu_id,
            'default_tab_view' => PATH_VIEWS . 'menu_tab_default.php',
            'social_medias' => Constants::getSocialList(),
            'tab_options' => [
                'url_tab_view' => UrlBuilder::makeUrl('Appearance', 'menuTabView'),
                'container_id' => 'tab-content'
            ],
            'can_create' => $this->hasPermission(Constants::PERM_CREATE_MENU),
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_MENU),
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_MENU),
        ];
        $this->render('menu_default', $view_data);
    }

    # /menu-tab
    public function menuTabView()
    {
        $menu_id = $this->request->get('ref');
        if (!$menu_id) {
            throw new \Exception('ref ne peut pas être null');
        }
        $menu_items = [];
        if ($menu_id > 0) {
            $menu_items = $this->repository->menuItem->findMenuItems($menu_id);
            $menu_data = $menu_items[0] ?? null;
            $url_delete = UrlBuilder::makeUrl('Appearance', 'deleteMenuAction', ['id' => $menu_id]);
        }

        $view_data = [
            'referer' => $menu_id,
            'pages' => $this->repository->post->findPublishedPages(),
            'menu_data' => $menu_data ?? null,
            'menu_types' => Constants::getMenusTypes(),
            'menu_items' => $menu_items,
            'social_medias' => Constants::getSocialList(),
            'url_form' => UrlBuilder::makeUrl('Appearance', 'menuAction'),
            'url_delete' => $url_delete ?? null,
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_MENU),
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_MENU),
        ];
        $this->renderViewOnly('menu_tab_default', $view_data);
    }

    # /menu-save
    public function menuAction()
    {
        $this->validateCSRF();
        $menu_id = $this->request->post('ref');
        $menu_items = $this->request->post('menu_items');

        if (!$menu_id) {
            $this->sendError(Constants::ERROR_UNKNOWN, ['menu_id' => $menu_id]);
        }
        if (!$this->request->post('menu_name')) {
            $this->sendError('Veuillez nommer le menu');
        }
        if (empty($menu_items)) {
            $this->sendError('Veuillez ajouter au moins un élément');
        }

        if ($this->repository->menu->findByTitle($this->request->post('menu_name'), ($menu_id != -1 ? $menu_id : null))) {
            $this->sendError('Ce nom est déjà pris');
        }

        $data_length = count($menu_items['pages']);
        $labels = $items = [];
        $i = 0;
        while ($i < $data_length) {
            if (empty($menu_items['labels'][$i])) $this->sendError('Le label d\'une page ne peut être vide');
            if (empty($menu_items['links'][$i])) $this->sendError('Le lien ne peut être vide');
            if (in_array($menu_items['labels'][$i], $labels)) $this->sendError('Un label doit être unique');

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

        try {
            Database::beginTransaction();

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
                $success_msg = Constants::SUCCESS_SAVED;
            }

            $this->repository->menuItem->create($items);

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('Appearance', 'menuView', ['id' => $menu_id]),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /delete-menu
    public function deleteMenuAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
        $this->repository->menu->remove($this->request->get('id'));
        $this->sendSuccess('Menu supprimé', [
            'url_next' => UrlBuilder::makeUrl('Appearance', 'menuView'),
            'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
        ]);
    }

    # /customization
    public function customizationView()
    {
        $this->setCSRFToken();
        $layout = $this->getLayoutData();
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Appearance', 'customizationAction'),
            'socials_menu' => $layout['footer_socials'] ?? [],
            'footer_sections' => $layout['footer_sections'] ?? [],
            'header_menu' => $layout['main_menu'] ?? [],
            'hero_data' => json_decode($this->getValue(Constants::STG_HERO_DATA), true),
            'link_menus' => $this->repository->menu->findMenuLinks(),
            'link_socials' => $this->repository->menu->findMenuSocials(),
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_CUSTOMIZATION),
        ];
        $this->render('layout_custom', $view_data);
    }

    # /customization-save
    public function customizationAction()
    {
        $this->validateCSRF();
        $footer_items = $this->request->post('footer_items');

        # Footer sections storage
        $data_length = !empty($footer_items['types']) ? count($footer_items['types']) : 0;
        $i = 0;
        $data = [];
        while ($i < $data_length) {
            if (empty($footer_items['labels'][$i])) $this->sendError('Le label d\'une section ne peut être vide.');

            $data[] = [
                'type' => $footer_items['types'][$i],
                'menu_id' => $footer_items['types'][$i] == Constants::LS_FOOTER_LINKS ? $footer_items['menus'][$i] : null,
                'label' => $footer_items['labels'][$i],
                'data' => $footer_items['types'][$i] == Constants::LS_FOOTER_LINKS ? null : $footer_items['data'][$i],
            ];
            $i++;
        }

        # Header storage
        $data[] = [
            'type' => Constants::LS_HEADER_MENU,
            'menu_id' => $this->request->post('main_header')
        ];

        # Social medias storage
        $data[] = [
            'type' => Constants::LS_FOOTER_SOCIALS,
            'menu_id' => $this->request->post('socials_footer')
        ];

        try {
            $this->repository->settings->updateSettings([
                Constants::STG_SITE_LAYOUT => json_encode($data),
                Constants::STG_HERO_DATA => json_encode([
                    'status' => $this->request->post('hero_status'),
                    'title' => $this->request->post('hero_title'),
                    'description' => $this->request->post('hero_description'),
                    'image' => $this->request->post('hero_image'),
                ]),
            ]);
            $this->sendSuccess(Constants::SUCCESS_SAVED, [
                'url_next' => UrlBuilder::makeUrl('Appearance', 'customizationView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    public function getLayoutData()
    {
        $data = json_decode($this->getValue(Constants::STG_SITE_LAYOUT), true);
        if (empty($data)) return ['header_menu' => [], 'footer_data' => []];

        $res = [];
        foreach ($data as $item) {
            switch ($item['type']) {
                case Constants::LS_HEADER_MENU :
                    $res['main_menu'] = $item['menu_id'];
                    break;
                case Constants::LS_FOOTER_SOCIALS :
                    $res['footer_socials'] = $item['menu_id'];
                    break;
                default :
                    $res['footer_sections'][] = $item;
                    break;
            }
        }
        return $res;
    }

}
