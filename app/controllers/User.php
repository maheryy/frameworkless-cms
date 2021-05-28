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
    }

    # /users
    public function listView()
    {
        $users = Repository::user()->findAll();

        foreach ($users as $user) {
            $user['url_detail'] = UrlBuilder::makeUrl('User', 'userView', ['id' => $user['id']]);
            $user['url_delete'] = UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]);
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
        $this->setCSRFToken();
        $this->setData([
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
            $validator = new Validator(FormRegistry::getUser());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            Repository::user()->create([
                'username' => $form_data['username'],
                'password' => password_hash($form_data['password'], PASSWORD_DEFAULT),
                'email' => $form_data['email'],
                'role' => $form_data['role'],
                'status' => Constants::STATUS_DEFAULT,
            ]);

            $this->sendSuccess('Utilisateur créé');
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
        $this->setCSRFToken();
        $this->setData([
            'user' => $user,
            'url_form' => UrlBuilder::makeUrl('User', 'userAction')
        ]);
        $this->render('user_detail');
    }

    # /user-save
    public function userAction()
    {
        $this->validateCSRF();
        try {
            $form_data = Request::allPost();
            $validator = new Validator(FormRegistry::getUser());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            Repository::user()->update($form_data['user_id'], [
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'role' => $form_data['role'],
                'updated_at' => Formatter::getDateTime()
            ]);

            $this->sendSuccess('Informations sauvegardé');
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
        }
    }
}
