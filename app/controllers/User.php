<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Formatter;
use App\Core\Utils\Repository;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Request;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Vendor\PHPMailer\PHPMailer\Src\Exception;

class User extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->setLayoutParams();
    }

    # /users
    public function listView()
    {
        $this->setContentTitle('Liste des utilisateurs');
        $users = Repository::user()->findAll();
        $roles = self::getRoles();

        foreach ($users as $key => $user) {
            $users[$key]['url_detail'] = UrlBuilder::makeUrl('User', 'userView', ['id' => $user['id']]);
            $users[$key]['url_delete'] = UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]);
            $users[$key]['role'] = $roles[$user['role']];
        }

        $this->setData([
            'users' => $users
        ]);
        $this->render('user_list');
    }

    # /users-save
    public function listAction()
    {

    }

    # /new-user
    public function createView()
    {
        $this->setContentTitle('Ajouter un utilisateur');
        $this->setCSRFToken();
        $this->setData([
            'roles' => self::getRoles(),
            'url_form' => UrlBuilder::makeUrl('User', 'createAction')
        ]);
        $this->render('user_new');
    }

    # /new-user-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $form_data = Request::allPost();
            $validator = new Validator(FormRegistry::getUserNew());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $user_id = Repository::user()->create([
                'username' => $form_data['username'],
                'password' => password_hash($form_data['password'], PASSWORD_DEFAULT),
                'email' => $form_data['email'],
                'role' => $form_data['role'],
                'status' => Constants::STATUS_DEFAULT,
            ]);

            $this->sendSuccess('Utilisateur créé', [
                'url_next' => UrlBuilder::makeUrl('User', 'userView', ['id' => $user_id])
            ]);
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /user
    public function userView()
    {
        $user_id = Request::get('id');
        if (!$user_id) {
            throw new Exception('Cet utilisateur n\'existe pas');
        }

        $user = Repository::user()->find($user_id);
        if (!$user) {
            throw new Exception('Cet utilisateur n\'existe pas');
        }

        $this->setContentTitle($user['username']);
        $this->setCSRFToken();
        $this->setData([
            'roles' => self::getRoles(),
            'user' => $user,
            'url_form' => UrlBuilder::makeUrl('User', 'userAction'),
            'url_delete' => UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]),
        ]);
        $this->render('user_detail');
    }

    # /user-save
    public function userAction()
    {
        $this->validateCSRF();
        try {
            $form_data = Request::allPost();
            $validator = new Validator(FormRegistry::getUserDetail());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $update_fields = [
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'role' => $form_data['role'],
                'updated_at' => Formatter::getDateTime()
            ];
            if(isset($form_data['password'])) {
                $update_fields['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);
            }

            Repository::user()->update($form_data['user_id'], $update_fields);

            $this->sendSuccess('Informations sauvegardé', [
                'url_next' => UrlBuilder::makeUrl('User', 'listView'),
            ]);
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /delete-user
    public function deleteAction()
    {
        $user_id = Request::get('id');
        if ($user_id) {
            Repository::user()->remove($user_id);
            $this->sendSuccess('Utilisateur supprimé', [
                'url_next' => UrlBuilder::makeUrl('User', 'listView')
            ]);
        }
    }

    public static function getRoles()
    {
        return [
            Constants::ROLE_DEFAULT => 'Normal',
            Constants::ROLE_EDITOR => 'Editeur',
            Constants::ROLE_ADMIN => 'Administrateur',
            Constants::ROLE_SUPER_ADMIN => 'Super Administrateur',
        ];
    }
}
