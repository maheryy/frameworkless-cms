<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Webpage extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /navigation
    public function navigationView()
    {
        $this->setCSRFToken();

        $default_nav = $this->request->get('id') ?? 1;
        $nav_items = $this->repository->navigation->findNavigation($default_nav);
        $navs = $this->repository->navigation->findAll();
        $nav_name = 'Nouvelle navigation';

        foreach ($navs as $nav) {
            if ($nav['id'] == $default_nav) {
                $nav_name = $nav_name['name'];
                break;
            }
        }

        $view_data = [
            'navigations' => $navs,
            'navigation_items' => $nav_items,
            'pages' => $this->repository->post->findPublishedPages(),
            'default_tab' => $default_nav,
            'default_tab_view' => PATH_VIEWS . 'nav_tab_default.php',
            'referer' => $default_nav,
            'nav_name' => $nav_name,
            'url_form' => UrlBuilder::makeUrl('Webpage', 'navigationAction'),
            'tab_options' => [
                'url_tab_view' => UrlBuilder::makeUrl('Webpage', 'navigationTabView'),
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
            $nav_items = $this->repository->navigation->findNavigation($nav_id);
            $nav_name = $nav_items['name'];
        } else {
            $nav_name = 'Nouvelle navigation';
        }

        $view_data = [
            'referer' => $nav_id,
            'pages' => $this->repository->post->findPublishedPages(),
            'nav_name' => $nav_name,
            'navigation_items' => $nav_items,
            'url_form' => UrlBuilder::makeUrl('Webpage', 'navigationAction'),
        ];
        $this->renderViewOnly('nav_tab_default', $view_data);
    }

    # /navigation-save
    public function navigationAction()
    {
        $this->validateCSRF();
        $nav_id = $this->request->post('ref');
        $nav_items = $this->request->post('nav_items');

        if (!$nav_id) {
            $this->sendError('Une erreur est survenue', ['nav_id' => $nav_id]);
        }
        if (!$this->request->post('nav_name')) {
            $this->sendError('Veuillez nommer la navigation');
        }
        if (empty($nav_items)) {
            $this->sendError('Veuillez ajouter au moins une page');
        }

        echo '<pre>';
        print_r($nav_items);
        die();

//        try {
//            Database::beginTransaction();
//            # New role
//            if ($role_id == -1) {
//                $role_id = $this->repository->role->create(['name' => $this->request->post('role_name')]);
//                $success_msg = 'Un nouveau rôle a été ajouté';
//            } else {
//                $this->repository->rolePermission->deleteAllByRole((int)$role_id);
//                $this->repository->role->update($role_id, ['name' => $this->request->post('role_name')]);
//                $success_msg = 'Informations sauvegardées';
//            }
//
//            $role_permissions = array_map(fn($perm_id) => ['role_id' => $role_id, 'permission_id' => $perm_id], $permissions);
//            $this->repository->rolePermission->create($role_permissions);
//
//
//            Database::commit();
//            $this->sendSuccess($success_msg, [
//                'url_next' => UrlBuilder::makeUrl('Role', 'roleView', ['id' => $role_id]),
//                'url_next_delay' => 1
//            ]);
//        } catch (\Exception $e) {
//            Database::rollback();
//            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
//        }
    }

    # /delete-navigation
    public function deleteNavigationAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }
        $this->repository->navigation->remove($this->request->get('id'));
        $this->sendSuccess('Page supprimée', [
            'url_next' => UrlBuilder::makeUrl('Webpage', 'navigationListView'),
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
