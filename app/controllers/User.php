<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\ForbiddenAccessException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Request;
use App\Core\Utils\Token;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;

class User extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /users
    public function listView()
    {
        $users = $this->repository->user->findAll();

        foreach ($users as $key => $user) {
            $users[$key]['url_detail'] = UrlBuilder::makeUrl('User', 'userView', ['id' => $user['id']]);
            $users[$key]['url_delete'] = UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]);
        }

        $this->setCSRFToken();
        $view_data = [
            'users' => $users,
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_USER),
            'can_read' => $this->hasPermission(Constants::PERM_READ_USER),
        ];
        $this->render('user_list', $view_data);
    }

    # /new-user
    public function createView()
    {
        $this->setCSRFToken();
        $view_data = [
            'roles' => $this->repository->role->findAll(),
            'default_role' => $this->getValue(Constants::STG_ROLE),
            'url_form' => UrlBuilder::makeUrl('User', 'createAction')
        ];
        $this->render('user_new', $view_data);
    }

    # /new-user-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $form_data = $this->request->allPost();
            $validator = new Validator(FormRegistry::getUserNew());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }
            # Check for duplicate
            if ($found = $this->repository->user->findByUsernameOrEmail($form_data['username'], $form_data['email'], 0)) {
                if ($found['email'] === $form_data['email']) {
                    $this->sendError('Cette adresse email est déjà pris');
                } else {
                    $this->sendError('Ce nom d\'utilisateur est déjà pris');
                }
            }

            Database::beginTransaction();
            $user_id = $this->repository->user->create([
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'role' => $form_data['role'],
                'status' => Constants::STATUS_INACTIVE,
            ]);

            # Create token
            $token_reference = (new Token())->generate(8)->encode();
            $token = (new Token())->generate();

            # Store the token with expiration
            $this->repository->validationToken->create([
                'user_id' => $user_id,
                'type' => Constants::TOKEN_EMAIL_CONFIRM,
                'token' => $token->getHash(),
                'reference' => $token_reference,
                'created_at' => Formatter::getDateTime(),
                'expires_at' => Formatter::getModifiedDateTime('+ ' . Constants::EMAIL_CONFIRM_TIMEOUT . ' minutes'),
            ]);

            # Send confirmation email
            $mail = Mailer::send([
                'to' => $form_data['email'],
                'subject' => 'Confirmation de votre compte',
                'content' => View::getHtml('email/confirmation_email', [
                    'email' => $form_data['email'],
                    'link_confirm' => UrlBuilder::makeAbsoluteUrl('Auth', 'passwordUpdateView', [
                        'ref' => $token_reference->get(),
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            Database::commit();
            $this->sendSuccess('Un email de confirmation a été envoyé', [
                'url_next' => UrlBuilder::makeUrl('User', 'listView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /user-reconfirmation
    public function reconfirmationAction()
    {
        $this->validateCSRF();
        try {
            if (!Validator::isValidEmail($this->request->post('email'))) {
                $this->sendError('Veuillez vérifier l\'adresse email', [['name' => 'email', 'error' => Validator::ERROR_EMAIL_DEFAULT]]);
            }

            Database::beginTransaction();

            # Remove existing token for this user
            $this->repository->validationToken->removeTokenByUser($this->request->post('user_id'), Constants::TOKEN_EMAIL_CONFIRM);

            # Create token
            $token_reference = (new Token())->generate(8)->encode();
            $token = (new Token())->generate();

            # Store the token with expiration
            $this->repository->validationToken->create([
                'user_id' => $this->request->post('user_id'),
                'type' => Constants::TOKEN_EMAIL_CONFIRM,
                'token' => $token->getHash(),
                'reference' => $token_reference,
                'created_at' => Formatter::getDateTime(),
                'expires_at' => Formatter::getModifiedDateTime('+ ' . Constants::EMAIL_CONFIRM_TIMEOUT . ' minutes'),
            ]);

            # Send confirmation email
            $mail = Mailer::send([
                'to' => $this->request->post('email'),
                'subject' => 'Confirmation de votre compte',
                'content' => View::getHtml('email/confirmation_email', [
                    'email' => $this->request->post('email'),
                    'link_confirm' => UrlBuilder::makeAbsoluteUrl('Auth', 'passwordUpdateView', [
                        'ref' => $token_reference->get(),
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            Database::commit();
            $this->sendSuccess('Un email de confirmation a été envoyé');
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /user
    public function userView()
    {
        if (!$this->request->get('id')) {
            throw new NotFoundException('Cet utilisateur n\'existe pas');
        }

        $is_current_user = $this->request->get('id') == $this->session->getUserId();

        # Custom permission check for user case
        if (!$this->hasPermission(Constants::PERM_READ_USER) && !$is_current_user) {
            if (Request::isPost()) $this->sendError(Constants::ERROR_FORBIDDEN);

            throw new ForbiddenAccessException(Constants::ERROR_FORBIDDEN);
        }

        $user = $this->repository->user->find((int)$this->request->get('id'));
        if (!$user) {
            throw new NotFoundException('Cet utilisateur n\'est pas trouvé');
        }
        $this->setContentTitle($is_current_user ? 'Mon profil' : $user['username']);
        $this->setCSRFToken();
        $view_data = [
            'roles' => $this->repository->role->findAll(),
            'user' => $user,
            'hold_confirmation' => $user['status'] == Constants::STATUS_INACTIVE,
            'is_current_user' => $this->session->getUserId() == $user['id'],
            'is_admin' => $this->session->isAdmin() || $this->session->isSuperAdmin(),
            'role_name' => !$this->session->isAdmin() && !$this->session->isSuperAdmin() ? $this->repository->role->find($this->session->getRole())['name'] : null,
            'url_form' => $user['status'] == Constants::STATUS_INACTIVE ? UrlBuilder::makeUrl('User', 'reconfirmationAction') : UrlBuilder::makeUrl('User', 'userAction'),
            'url_delete' => UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]),
            'can_update' => $is_current_user || $this->hasPermission(Constants::PERM_UPDATE_USER),
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_USER),
        ];
        $this->render('user_detail', $view_data);
    }

    # /user-save
    public function userAction()
    {
        $this->validateCSRF();
        $form_data = $this->request->allPost();

        # Custom permission check for user case
        if (!$this->hasPermission(Constants::PERM_UPDATE_USER) && $form_data['user_id'] != $this->session->getUserId()) {
            if (Request::isPost()) $this->sendError(Constants::ERROR_FORBIDDEN);

            throw new ForbiddenAccessException(Constants::ERROR_FORBIDDEN);
        }

        # Check for duplicate
        if ($found = $this->repository->user->findByUsernameOrEmail($form_data['username'], $form_data['email'], (int)$form_data['user_id'])) {
            if ($found['email'] === $form_data['email']) {
                $this->sendError('Cette adresse email est déjà pris');
            } else {
                $this->sendError('Ce nom d\'utilisateur est déjà pris');
            }
        }

        try {
            $validator = new Validator(FormRegistry::getUserDetail());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $update_fields = [
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'updated_at' => Formatter::getDateTime()
            ];
            if (isset($form_data['password'])) {
                $update_fields['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);
            }
            if (isset($form_data['role']) && ($this->session->isAdmin() || $this->session->isSuperAdmin())) {
                $update_fields['role'] = $form_data['role'];
            }

            $this->repository->user->update($form_data['user_id'], $update_fields);

            $this->sendSuccess(Constants::SUCCESS_SAVED, [
                'url_next' => UrlBuilder::makeUrl('User', 'listView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /delete-user
    public function deleteAction()
    {
        $this->validateCSRF();
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }

        # Quick check if this user is the last super admin
        if ($this->repository->user->find((int)$this->request->get('id'))['role'] == Constants::ROLE_SUPER_ADMIN
            && $this->repository->user->countSuperAdmin() === 1
        ) {
            $this->sendError('Impossible de supprimer l\'administrateur de l\'application');
        }

        $this->repository->user->remove((int)$this->request->get('id'));

        $this->sendSuccess('Utilisateur supprimé', [
            'url_next' => $this->request->get('id') == $this->session->getUserId()
                ? UrlBuilder::makeUrl('Auth', 'logoutAction')
                : UrlBuilder::makeUrl('User', 'listView'),
            'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
        ]);
    }

    # /role
    public function roleView()
    {
        $this->setCSRFToken();

        $default_role = $this->request->get('id') ?? $this->session->getRole();
        $permissions = $this->repository->permission->findAll();
        $role_permissions = $this->repository->rolePermission->findAllPermissionsByRole((int)$default_role);
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
            'url_form' => UrlBuilder::makeUrl('User', 'roleAction'),
            'tab_options' => [
                'url_tab_view' => UrlBuilder::makeUrl('User', 'roleTabView'),
                'container_id' => 'tab-content'
            ],
            'can_create' => $this->hasPermission(Constants::PERM_CREATE_ROLE),
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_ROLE),
        ];
        $this->render('role_default', $view_data);
    }

    # /role-tab
    public function roleTabView()
    {
        $role_id = (int)$this->request->get('ref');
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
                throw new NotFoundException("le role $role_id n'existe pas");
            }
        } else {
            $role_name = 'Nouveau rôle';
        }

        $view_data = [
            'referer' => $role_id,
            'role_name' => $role_name,
            'permissions' => $permissions,
            'role_permissions' => $role_permissions,
            'url_form' => UrlBuilder::makeUrl('User', 'roleAction'),
            'can_create' => $this->hasPermission(Constants::PERM_CREATE_ROLE),
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_ROLE),
        ];
        $this->renderViewOnly('role_tab_default', $view_data);
    }

    # /role-save
    public function roleAction()
    {
        $this->validateCSRF();
        $role_id = (int)$this->request->post('ref');
        $permissions = $this->request->post('permissions');

        # Double check permissions
        if ($role_id === -1 && !$this->hasPermission(Constants::PERM_CREATE_ROLE)
            || $role_id !== -1 && !$this->hasPermission(Constants::PERM_UPDATE_ROLE)
        ) {
            $this->sendError(Constants::ERROR_FORBIDDEN);
        }

        if (!$role_id) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
        if (!$this->request->post('role_name')) {
            $this->sendError('Veuillez nommer le rôle');
        }
        if (empty($permissions)) {
            $this->sendError('Veuillez ajouter au moins une permission');
        }

        if ($this->repository->role->findByName($this->request->post('role_name'), ($role_id !== -1 ? $role_id : null))) {
            $this->sendError('Ce nom est déjà pris');
        }

        try {
            Database::beginTransaction();
            # New role
            if ($role_id === -1) {
                $role_id = $this->repository->role->create(['name' => $this->request->post('role_name')]);
                $success_msg = 'Un nouveau rôle a été ajouté';
            } else {
                $this->repository->rolePermission->deleteAllByRole((int)$role_id);
                $this->repository->role->update($role_id, ['name' => $this->request->post('role_name')]);
                $success_msg = Constants::SUCCESS_SAVED;
            }

            $role_permissions = array_map(fn($perm_id) => ['role_id' => $role_id, 'permission_id' => $perm_id], $permissions);
            $this->repository->rolePermission->create($role_permissions);

            # Refresh session permissions variable
            if ($this->session->get('user_role') == $role_id) {
                $this->session->set('permissions', array_map(fn($perm_id) => (int)$perm_id, $permissions));
            }

            Database::commit();
            $this->sendSuccess($success_msg, [
                'url_next' => UrlBuilder::makeUrl('User', 'roleView', ['id' => $role_id]),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
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
