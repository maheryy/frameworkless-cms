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
            ]
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
            $this->sendError('Une erreur est survenue', ['menu_id' => $menu_id]);
        }
        if (!$this->request->post('menu_name')) {
            $this->sendError('Veuillez nommer le menu');
        }
        if (empty($menu_items)) {
            $this->sendError('Veuillez ajouter au moins un élément');
        }

        $data_length = count($menu_items['pages']);
        $labels = $items = [];
        $i = 0;
        while ($i < $data_length) {
            if (empty($menu_items['labels'][$i])) $this->sendError('Le label d\'une page ne peut être vide.');
            if (empty($menu_items['links'][$i])) $this->sendError('Le lien ne peut être vide.');
            if (in_array($menu_items['labels'][$i], $labels)) $this->sendError('Un label doit être unique.');

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
                $success_msg = 'Informations sauvegardées';
            }

            $this->repository->menuItem->create($items);

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('Appearance', 'menuView', ['id' => $menu_id]),
                'url_next_delay' => 1
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }

    # /delete-menu
    public function deleteMenuAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }
        $this->repository->menu->remove($this->request->get('id'));
        $this->sendSuccess('Menu supprimée', [
            'url_next' => UrlBuilder::makeUrl('Appearance', 'menuView'),
            'delay_url_next' => 0,
        ]);
    }

    # /customization
    public function customizationView()
    {
        $this->setCSRFToken();
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Appearance', 'customizationAction'),
            'link_menus' => $this->repository->menu->findMenuLinks(),
            'link_socials' => $this->repository->menu->findMenuSocials(),
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
        $footer_sections = [];
        while ($i < $data_length) {
            if (empty($footer_items['labels'][$i])) $this->sendError('Le label d\'une section ne peut être vide.');

            $links = [];
            if (!empty($footer_items['menus'][$i])) {
                $links = array_map(
                    fn($el) => ['label' => $el['label'], 'link' => $el['page_link'] ?? $el['url']],
                    $this->repository->menuItem->findMenuItems((int)$footer_items['menus'][$i])
                );
            }

            $footer_sections[] = [
                'type' => $footer_items['types'][$i],
                'label' => $footer_items['labels'][$i],
                'data' => $footer_items['types'][$i] == Constants::FOOTER_LINKS ? $links : $footer_items['data'][$i],
            ];
            $i++;
        }

        # Social medias storage
        $social_items = [];
        if ($this->request->post('socials_footer')) {
            $social_items = array_map(
                fn($el) => ['icon' => $el['icon'], 'link' => $el['url']],
                $this->repository->menuItem->findMenuItems((int) $this->request->post('socials_footer'))
            );
        }

        # Header menu storage
        $header_items = [];
        if ($this->request->post('main_header')) {
            $header_items = array_map(
                fn($el) => ['label' => $el['label'], 'link' => $el['page_link'] ?? $el['url']],
                $this->repository->menuItem->findMenuItems((int) $this->request->post('main_header'))
            );
        }

        try {
            $this->repository->settings->updateSettings([
                'footer_layout' => json_encode(['sections' => $footer_sections, 'socials' => $social_items]),
                'header_layout' => json_encode($header_items),
            ]);
            $this->sendSuccess('Informations sauvegardées');
        } catch (\Exception $e) {
            $this->sendError('Une erreur est survenue : '. $e->getMessage());
        }
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
