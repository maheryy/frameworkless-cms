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

    # /login
    public function loginView()
    {
        if ($this->session->isLoggedIn()) {
            $this->router->redirect(UrlBuilder::makeUrl('Home', 'defaultView'));
        }

        if ($this->request->get('timeout')) {
            $this->setParam('active_error', 'Déconnecté pour inactivité');
        }

        $redirect = $this->request->url('redirect');
        $url_form_params = $redirect ? ['redirect' => Formatter::encodeUrlQuery($redirect)] : [];
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('User', 'loginAction', $url_form_params),
            'url_forgotten_password' => UrlBuilder::makeUrl('User', 'passwordRecoveryView'),
        ];

        $this->render('login', $view_data);
    }

    # /login-send
    public function loginAction()
    {
        $data = [
            'login' => $this->request->post('login'),
            'password' => $this->request->post('password'),
        ];
        $validator = new Validator();
        if (!$validator->validateRequiredOnly($data)) {
            $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
        }

        $user = $this->repository->user->findByLogin($data['login']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->sendError('Nom d\'utilisateur ou mot de passe incorrect');
        }

        if ($user['status'] == Constants::STATUS_INACTIVE) {
            $this->sendError('Votre adresse email doit être vérifiée');
        }

        $this->createSessionData($user);
        $this->sendSuccess('Bien joué ! Tu es connecté', [
            'url_next' => $this->request->url('redirect') ?? UrlBuilder::makeUrl('Home', 'defaultView'),
        ]);
    }

    # /recover-password
    public function passwordRecoveryView()
    {
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('User', 'passwordRecoveryAction'),
            'url_back' => UrlBuilder::makeUrl('User', 'loginView'),
        ];
        $this->render('password_recovery', $view_data);
    }

    # /recover-password-send
    public function passwordRecoveryAction()
    {
        try {
            # Quick validation
            $login = $this->request->post('login');

            $validator = new Validator();
            if (!$validator->validateRequiredOnly(['login' => $login])) {
                $this->sendError('Ce champ ne peut pas être vide', $validator->getErrors());
            }

            # Verify user
            $user = $this->repository->user->findByLogin($login);
            if (!$user) {
                $this->sendError('Aucun compte n\'a été trouvé');
            }

            # Create token
            $token_reference = (new Token())->generate(8)->encode();
            $token = (new Token())->generate();

            $validation_token_repository = $this->repository->validationToken;
            $validation_token_repository->removeTokenByUser($user['id'], Constants::TOKEN_RESET_PASSWORD);

            # Store the token with expiration
            $validation_token_repository->create([
                'user_id' => $user['id'],
                'type' => Constants::TOKEN_RESET_PASSWORD,
                'token' => $token->getHash(),
                'reference' => $token_reference,
                'created_at' => Formatter::getDateTime(),
                'expires_at' => Formatter::getModifiedDateTime('+ ' . Constants::RESET_PASSWORD_TIMEOUT . ' minutes'),
            ]);

            # Send confirmation email
            $mail = Mailer::send([
                'to' => $user['id'],
                'subject' => 'Réinitialisation de votre mot passe',
                'content' => View::getHtml('email/password_reset_email', [
                    'username' => $user['username'],
                    'link_reset_password' => UrlBuilder::makeAbsoluteUrl('User', 'passwordResetView', [
                        'ref' => $token_reference,
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            $this->sendSuccess('Lien de réinitialisation envoyé');
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /reset-password
    public function passwordResetView()
    {
        $token_reference = $this->request->get('ref');
        $token = $this->request->get('token');
        $validation_token = $this->repository->validationToken->findByReference($token_reference);

        if (!$validation_token) {
            $view_data = ['is_token_valid' => false];
        } elseif (Formatter::getTimestampFromDateTime($validation_token['expires_at']) < Formatter::getTimestamp()) {
            $view_data = [
                'is_token_valid' => true,
                'has_expired' => true
            ];
        } else {
            $view_data = [
                'url_form' => UrlBuilder::makeUrl('User', 'passwordResetAction'),
                'is_token_valid' => true,
                'has_expired' => false,
                'reference' => $token_reference,
                'token' => $token,
            ];
        }
        $view_data['url_back'] = UrlBuilder::makeUrl('User', 'loginView');
        $this->render('password_reset', $view_data);
    }

    # /reset-password-send
    public function passwordResetAction()
    {
        try {
            # Validate fields
            $data = [
                'password' => $this->request->post('password'),
                'password_confirm' => $this->request->post('password_confirm'),
            ];

            $validator = new Validator(FormRegistry::getPasswordReset());
            if (!$validator->validate($data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }
            $validation_token_repository = $this->repository->validationToken;

            # Verify token
            $validation_token = $validation_token_repository->findByReference($this->request->post('reference'));
            $token = (new Token($this->request->post('token')))->decode();
            if (!$validation_token || !$token->equals($validation_token['token'])) {
                $this->sendError("Une erreur est survenue");
            }

            Database::beginTransaction();
            # Update user password
            $this->repository->user->updatePassword($validation_token['user_id'], password_hash($data['password'], PASSWORD_DEFAULT));

            # Delete token
            $validation_token_repository->remove($validation_token['id']);
            Database::commit();

            $this->sendSuccess("Votre mot de passe a été réinitialisé", [
                'url_next' => UrlBuilder::makeUrl('User', 'loginView'),
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu pendant le traitement", [$e->getMessage()]);
        }
    }

    # /logout
    public function logoutAction()
    {
        if ($this->request->getCookie(session_name())) {
            $this->request->deleteCookie(session_name());
            $this->session->stop();
        }

        $url_params = $this->request->get('timeout') ? ['redirect' => Formatter::encodeUrlQuery($this->request->url('redirect')), 'timeout' => 1] : [];
        $this->router->redirect(UrlBuilder::makeUrl('User', 'loginView', $url_params));
    }

    # /users
    public function listView()
    {
        $users = $this->repository->user->findAll();
        $roles = Constants::getRoles();

        foreach ($users as $key => $user) {
            $users[$key]['url_detail'] = UrlBuilder::makeUrl('User', 'userView', ['id' => $user['id']]);
            $users[$key]['url_delete'] = UrlBuilder::makeUrl('User', 'deleteAction', ['id' => $user['id']]);
            $users[$key]['role'] = $roles[$user['role']];
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
            'roles' => Constants::getRoles(),
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
                        'ref' => $token_reference,
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
            'roles' => Constants::getRoles(),
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

    # /confirm-account
    public function confirmAccountView()
    {
        $token_reference = $this->request->get('ref');
        $validation_token = $this->repository->validationToken->findByReference($token_reference);

        $token = (new Token($this->request->get('token')))->decode();
        if (!$validation_token || !$token->equals($validation_token['token'])) {
            $view_data = ['is_token_valid' => false];
        } elseif (Formatter::getTimestampFromDateTime($validation_token['expires_at']) < Formatter::getTimestamp()) {
            $view_data = [
                'is_token_valid' => true,
                'has_expired' => true
            ];
        } else {
            $view_data = [
                'is_token_valid' => true,
                'has_expired' => false,
            ];
            $this->repository->validationToken->remove($validation_token['id']);
            $this->repository->user->update($validation_token['user_id'], ['status' => Constants::STATUS_ACTIVE]);
        }

        $view_data['url_login'] = UrlBuilder::makeUrl('User', 'loginView');
        $this->render('account_confirm', $view_data);
    }

    private function createSessionData(array $user_data)
    {
        $this->session->setData([
            'user_id' => $user_data['id'],
            'user_role' => $user_data['role'],
            'is_admin' => $user_data['role'] == Constants::ROLE_ADMIN,
            'csrf_token' => (new Token())->generate()->getEncoded()
        ]);
    }
}
