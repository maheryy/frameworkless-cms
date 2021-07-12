<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Role extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }


    # /new-page-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $validator = new Validator();
            if (!$validator->validateRequiredOnly(['title' => $this->request->post('title')])) {
                $this->sendError('Le titre est obligatoire');
            }

            Database::beginTransaction();
            $page_id = $this->repository->post->create([
                'author_id' => $this->session->getUserId(),
                'title' => $this->request->post('title'),
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_PAGE,
                'status' => $this->request->post('action_publish') ? Constants::STATUS_PUBLISHED : Constants::STATUS_DRAFT,
                'published_at' => $this->request->post('action_publish') ? Formatter::getDateTime(null, Formatter::DATE_TIME_FORMAT) : null
            ]);
            $this->repository->pageExtra->create([
                'post_id' => $page_id,
                'slug' => $this->request->post('slug') ?? Formatter::slugify($this->request->post('title')),
                'visibility' => $this->request->post('visibility'),
                'allow_comments' => $this->request->post('allow_comments') ? 1 : 0,
                'meta_title' => $this->request->post('meta_title') ?? $this->request->post('title'),
                'meta_description' => $this->request->post('meta_description'),
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
            ]);

            Database::commit();
            $this->sendSuccess('Page créée', [
                'url_next' => UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page_id])
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /page
    public function pageView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cette page n\'existe pas');
        }
        $page = $this->repository->post->findPage($this->request->get('id'));
        if (!$page) {
            throw new NotFoundException('Cette page n\'est pas trouvé');
        }

        if (!empty($page['published_at'])) {
            $page['published_at'] = Formatter::getDateTime($page['published_at'], Formatter::DATE_FORMAT);
        }
        $users = $this->repository->user->findAll();
        $this->setContentTitle($page['title']);
        $this->setCSRFToken();
        $view_data = [
            'page' => $page,
            'users' => $users,
            'visibility_types' => Constants::getVisibilityTypes(),
            'url_form' => UrlBuilder::makeUrl('Page', 'pageAction', ['id' => $page['id']]),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]),
        ];
        $this->render('page_detail', $view_data);
    }

    # /page-save
    public function pageAction()
    {
        $this->validateCSRF();
        try {
            $validator = new Validator();
            if (!$validator->validateRequiredOnly(['title' => $this->request->post('title')])) {
                $this->sendError('Le titre est obligatoire');
            }
            $post_fields = [
                'title' => $this->request->post('title'),
                'author_id' => $this->request->post('author'),
                'content' => $_POST['post_content'],
                'updated_at' => Formatter::getDateTime()
            ];

            if ($this->request->post('action_publish')) {
                $post_fields['status'] = Constants::STATUS_PUBLISHED;
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            if ($this->request->post('published_at')) {
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            $page_fields = [
                'slug' => $this->request->post('slug'),
                'meta_title' => $this->request->post('meta_title'),
                'meta_description' => $this->request->post('meta_description'),
                'allow_comments' => $this->request->post('allow_comments') ? 1 : 0,
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
                'visibility' => $this->request->post('visibility'),
            ];

            Database::beginTransaction();
            $this->repository->post->update($this->request->get('id'), $post_fields);
            $this->repository->pageExtra->update($this->request->get('id'), $page_fields);
            Database::commit();

            $this->sendSuccess('Informations sauvegardées');
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }

    # /roles
    public function roleView()
    {
        $this->setCSRFToken();

        $default_role = 4;
        $permissions = $this->repository->permission->findAll();
        $role_permissions = $this->repository->rolePermission->findAllPermissionsByRole($default_role);
        $permissions = $this->getDiff2DArray($permissions, $role_permissions, 'id');
        $roles = $this->repository->role->findAll();

        $role_name = 'Nouveau rôle';
        foreach ($roles as $role) {
            if($role['id'] == $default_role) {
                $role_name = $role['name'];
                break;
            }
        }

        $view_data = [
            'roles' => $roles,
            'default_tab' => $default_role,
            'default_tab_view' => PATH_VIEWS . 'role_tab_default.php',
            'referer' => $default_role,
            'role_name' => $role_name,
            'permissions' => $permissions,
            'role_permissions' => $role_permissions,
            'url_form' => UrlBuilder::makeUrl('Role', 'roleAction'),
            'tab_options' => [
                'url_tab_view' => UrlBuilder::makeUrl('Role', 'roleTabView'),
                'container_id' => 'tab-content'
            ]
        ];
        $this->render('role_default', $view_data);
    }

    # /role-tab
    public function roleTabView()
    {
        $role_id = $this->request->get('ref');
        if (!$role_id) {
            throw new \Exception('ref ne peut pas être null');
        }

        $roles = $this->repository->role->findAll();
        $permissions = $this->repository->permission->findAll();
        $role_permissions = [];
        if ($role_id > 0) {
            $role_permissions = $this->repository->rolePermission->findAllPermissionsByRole($role_id);
            $permissions = $this->getDiff2DArray($permissions, $role_permissions, 'id');
            foreach ($roles as $role) {
                if($role['id'] == $role_id) {
                    $role_name = $role['name'];
                    break;
                }
            }
        } else {
            $role_name = 'Nouveau rôle';
        }

//        echo '<pre>';
//        print_r($permissions);
//        print_r($role_permissions);
//        die();

        $view_data = [
            'referer' => $role_id,
            'role_name' => $role_name,
            'permissions' => $permissions,
            'role_permissions' => $role_permissions,
            'url_form' => UrlBuilder::makeUrl('Role', 'roleAction'),
        ];
        $this->renderViewOnly('role_tab_default', $view_data);
    }

    # /role-save
    public function roleAction()
    {

    }

    private function getDiff2DArray(array $a, array $b, $key_comp)
    {
        return !empty($a) && !empty($b)
            ? array_filter(
                $a,
                fn($el_a) => !in_array($el_a[$key_comp], array_map(fn($el_b) => $el_b[$key_comp], $b))
            )
            : $a;
    }
}
