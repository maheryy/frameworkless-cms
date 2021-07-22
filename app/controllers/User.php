<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Session;
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

        $view_data = [
            'users' => $users
        ];
        $this->render('user_list', $view_data);
    }

    # /users-save
    public function listAction()
    {

    }

    # /new-user
    public function createView()
    {
        $this->setCSRFToken();
        $view_data = [
            'roles' => $this->repository->role->findAll(),
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

            Database::beginTransaction();
            $user_id = $this->repository->user->create([
                'username' => $form_data['username'],
                'password' => password_hash($form_data['password'], PASSWORD_DEFAULT),
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
                    'link_confirm' => UrlBuilder::makeAbsoluteUrl('User', 'confirmAccountView', [
                        'ref' => $token_reference->get(),
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            Database::commit();
            $this->sendSuccess('Utilisateur créé', [
                'url_next' => UrlBuilder::makeUrl('User', 'userView', ['id' => $user_id])
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /user
    public function userView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cet utilisateur n\'existe pas');
        }
        $user = $this->repository->user->find($this->request->get('id'));
        if (!$user) {
            throw new NotFoundException('Cet utilisateur n\'est pas trouvé');
        }
        $this->setContentTitle($user['username']);
        $this->setCSRFToken();
        $view_data = [
            'roles' => $this->repository->role->findAll(),
            'user' => $user,
            'url_form' => UrlBuilder::makeUrl('User', 'userAction'),
            'url_delete' => UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]),
        ];
        $this->render('user_detail', $view_data);
    }

    # /user-save
    public function userAction()
    {
        $this->validateCSRF();
        try {
            $form_data = $this->request->allPost();
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
            if (isset($form_data['password'])) {
                $update_fields['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);
            }

            $this->repository->user->update($form_data['user_id'], $update_fields);

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
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }

        $this->repository->user->remove($this->request->get('id'));
        $this->sendSuccess('Utilisateur supprimé', [
            'url_next' => UrlBuilder::makeUrl('User', 'listView'),
            'delay_url_next' => 0,
        ]);

    }
}
