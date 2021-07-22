<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Router;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Core\Utils\Token;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;

class Auth extends Controller
{

    public function __construct(array $options = [])
    {
//        parent::__construct($options);
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->session = new Session();
        $this->setTemplate('default');
    }

    # /login
    public function loginView()
    {
        if ($this->session->isLoggedIn()) {
            $this->router->redirect(UrlBuilder::makeUrl('Home', 'dashboardView'));
        }

        if ($this->request->get('timeout')) {
            $this->setParam('active_error', 'Déconnecté pour inactivité');
        }

        $redirect = $this->request->url('redirect');
        $url_form_params = $redirect ? ['redirect' => Formatter::encodeUrlQuery($redirect)] : [];
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Auth', 'loginAction', $url_form_params),
            'url_forgotten_password' => UrlBuilder::makeUrl('Auth', 'passwordRecoveryView'),
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
            'url_next' => $this->request->url('redirect') ?? UrlBuilder::makeUrl('Home', 'dashboardView'),
        ]);
    }

    # /logout
    public function logoutAction()
    {
        if ($this->request->getCookie(session_name())) {
            $this->request->deleteCookie(session_name());
            $this->session->stop();
        }

        $url_params = $this->request->get('timeout') ? ['redirect' => Formatter::encodeUrlQuery($this->request->url('redirect')), 'timeout' => 1] : [];
        $this->router->redirect(UrlBuilder::makeUrl('Auth', 'loginView', $url_params));
    }

    # /recover-password
    public function passwordRecoveryView()
    {
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Auth', 'passwordRecoveryAction'),
            'url_back' => UrlBuilder::makeUrl('Auth', 'loginView'),
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
                    'link_reset_password' => UrlBuilder::makeAbsoluteUrl('Auth', 'passwordResetView', [
                        'ref' => $token_reference->get(),
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
                'url_form' => UrlBuilder::makeUrl('Auth', 'passwordResetAction'),
                'is_token_valid' => true,
                'has_expired' => false,
                'reference' => $token_reference,
                'token' => $token,
            ];
        }
        $view_data['url_back'] = UrlBuilder::makeUrl('Auth', 'loginView');
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
                'url_next' => UrlBuilder::makeUrl('Auth', 'loginView'),
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu pendant le traitement", [$e->getMessage()]);
        }
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
        $permissions = array_map(fn($el) => (int)$el['id'], $this->repository->rolePermission->findAllPermissionsByRole((int)$user_data['role']));
        $this->session->setData([
            'user_id' => $user_data['id'],
            'user_role' => $user_data['role'],
            'is_admin' => $user_data['role'] == Constants::ROLE_ADMIN,
            'permissions' => $permissions,
            'csrf_token' => (new Token())->generate()->getEncoded()
        ]);
    }
}
