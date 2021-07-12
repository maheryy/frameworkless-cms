<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Utils\UrlBuilder;

class Role extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /roles
    public function roleView()
    {
        $this->setCSRFToken();

        $default_role = $this->request->get('id') ?? 1;
        $permissions = $this->repository->permission->findAll();
        $role_permissions = $this->repository->rolePermission->findAllPermissionsByRole($default_role);
        $permissions = $this->getDiff2DArray($permissions, $role_permissions, 'id');
        $roles = $this->repository->role->findAll();

        $role_name = 'Nouveau rôle';
        foreach ($roles as $role) {
            if ($role['id'] == $default_role) {
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
                if ($role['id'] == $role_id) {
                    $role_name = $role['name'];
                    break;
                }
            }
            if (!isset($role_name)) {
                throw new \Exception("le role $role_id n'existe pas");
            }
        } else {
            $role_name = 'Nouveau rôle';
        }

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
        $this->validateCSRF();
        $role_id = $this->request->post('ref');
        $permissions = $this->request->post('permissions');

        if (!$role_id) {
            $this->sendError('Une erreur est survenue', ['role_id' => $role_id]);
        }
        if (!$this->request->post('role_name')) {
            $this->sendError('Veuillez nommer le rôle');
        }
        if (empty($permissions)) {
            $this->sendError('Veuillez ajouter au moins une permission');
        }

        try {
            Database::beginTransaction();
            # New role
            if ($role_id == -1) {
                $role_id = $this->repository->role->create(['name' => $this->request->post('role_name')]);
                $success_msg = 'Un nouveau rôle a été ajouté';
            } else {
                $this->repository->rolePermission->deleteAllByRole((int)$role_id);
                $this->repository->role->update($role_id, ['name' => $this->request->post('role_name')]);
                $success_msg = 'Informations sauvegardées';
            }

            $role_permissions = array_map(fn($perm_id) => ['role_id' => $role_id, 'permission_id' => $perm_id], $permissions);
            $this->repository->rolePermission->create($role_permissions);

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('Role', 'roleView', ['id' => $role_id]),
                'url_next_delay' => 1
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }

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
