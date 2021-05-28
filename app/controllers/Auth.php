<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Repository;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Mailer;
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
        parent::__construct($options);
    }

    # /login
    public function loginView()
    {
        if (Session::isLoggedIn()) {
            $this->router->redirect(UrlBuilder::makeUrl('Home', 'defaultView'));
        }

        if (Request::get('timeout')) {
            $this->setParam('active_error', 'Déconnecté pour inactivité');
        }

        $redirect = Request::url('redirect');
        $url_form_params = $redirect ? ['redirect' => Formatter::encodeUrlQuery($redirect)] : [];
        $this->setData([
            'url_form' => UrlBuilder::makeUrl('Auth', 'loginAction', $url_form_params),
            'url_forgotten_password' => UrlBuilder::makeUrl('Auth', 'passwordRecoveryView'),
        ]);

        $this->render('login');
    }

    # /recover-password
    public function passwordRecoveryView()
    {
        $this->setParam('url_form_action', UrlBuilder::makeUrl('Auth', 'passwordRecoveryAction'));
        $this->setData([
            'url_form_action' => UrlBuilder::makeUrl('Auth', 'passwordRecoveryAction'),
            'url_back' => UrlBuilder::makeUrl('Auth', 'loginView'),
        ]);
        $this->render('password_recovery');
    }

    # /recover-password-send
    public function passwordRecoveryAction()
    {
        try {
            # Quick validation
            $email = Request::post('email');

            $validator = new Validator(FormRegistry::getPasswordRecovery());
            if (!$validator->validate(['email' => $email])) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            # Verify user by email field
            $user = Repository::user()->findByEmail($email);
            if (!$user) {
                $this->sendError('Aucun compte n\'est associé à cette adresse email ');
            }

            # Create token
            $token_reference = (new Token())->generate(8)->encode();
            $token = (new Token())->generate();

            $validation_token_repository = Repository::validationToken();
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
        $token_reference = Request::get('ref');
        $token = Request::get('token');
        $validation_token = Repository::validationToken()->findByReference($token_reference);

        if (!$validation_token) {
            $view_data = ['is_token_valid' => false];
        } elseif (Formatter::getTimestampFromDateTime($validation_token['expires_at']) < Formatter::getTimestamp()) {
            $view_data = [
                'is_token_valid' => true,
                'has_expired' => true
            ];
        } else {
            $view_data = [
                'url_form_action' => UrlBuilder::makeUrl('Auth', 'passwordResetAction'),
                'is_token_valid' => true,
                'has_expired' => false,
                'reference' => $token_reference,
                'token' => $token,
            ];
        }
        $view_data['url_back'] = UrlBuilder::makeUrl('Auth', 'loginView');
        $this->setData($view_data);
        $this->render('password_reset');
    }

    # /reset-password-send
    public function passwordResetAction()
    {
        try {
            # Validate fields
            $data = [
                'password' => Request::post('password'),
                'password_confirm' => Request::post('password_confirm'),
            ];

            $validator = new Validator(FormRegistry::getPasswordReset());
            if (!$validator->validate($data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }
            $validation_token_repository = Repository::validationToken();

            # Verify token
            $validation_token = $validation_token_repository->findByReference(Request::post('reference'));
            $token = (new Token(Request::post('token')))->decode();
            if (!$validation_token || !$token->equals($validation_token['token'])) {
                $this->sendError("Une erreur est survenue");
            }

            # Update user password
            Repository::user()->updatePassword($validation_token['user_id'], password_hash($data['password'], PASSWORD_DEFAULT));

            # Delete token
            $validation_token_repository->remove($validation_token['id']);

            $this->sendSuccess("Votre mot de passe a été réinitialisé", [
                'url_next' => UrlBuilder::makeUrl('Auth', 'loginView'),
            ]);
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu pendant le traitement", [$e->getMessage()]);
        }
    }

    # /login-send
    public function loginAction()
    {
        $data = [
            'login' => Request::post('login'),
            'password' => Request::post('password'),
        ];
        $validator = new Validator();
        if (!$validator->validateRequiredOnly($data)) {
            $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
        }

        $user = Repository::user()->findByLogin($data['login']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->sendError('Nom d\'utilisateur ou mot de passe incorrect');
        }
        $this->createSessionData($user);
        $this->sendSuccess('Bien joué ! Tu es connecté', [
            'url_next' => Request::url('redirect') ?? UrlBuilder::makeUrl('Home', 'defaultView'),
        ]);
    }

    # /logout
    public function logoutAction()
    {
        if (Request::getCookie(session_name())) {
            Request::deleteCookie(session_name());
            Session::stop();
        }

        $url_params = Request::get('timeout') ? ['redirect' => Formatter::encodeUrlQuery(Request::url('redirect')), 'timeout' => 1] : [];
        $this->router->redirect(UrlBuilder::makeUrl('Auth', 'loginView', $url_params));
    }

    # /auth/test
    public function test()
    {
        $user_repo = Repository::user();
        $token_repo = Repository::validationToken();

        var_dump('create', $user_repo->create([
            'username' => "nfgbvx",
            'email' => 'testr',
            'password' => 'sfeadzdzas',
            'role' => Constants::ROLE_ADMIN,
        ]));
        var_dump('all', $user_repo->findAll());
        var_dump('upd', $user_repo->updatePassword(2, "abracadbra"));

    }

    private function createSessionData(array $user_data)
    {
        Session::load([
            'user_id' => $user_data['id'],
            'user_role' => $user_data['role'],
            'is_admin' => $user_data['role'] == 3,
            'csrf_token' => (new Token())->generate()->getEncoded()
        ]);
    }
}
