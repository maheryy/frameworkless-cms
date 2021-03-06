<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\HttpNotFoundException;
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
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->session = new Session();
        $this->setTemplate('default');

        # Database check before taking any actions
        if (!Database::isReady()) {
            if (Request::isPost()) $this->sendError("Une installation est nécessaire : " . UrlBuilder::makeUrl('Installer', 'installerView'));

            $this->router->redirect(UrlBuilder::makeUrl('Installer', 'installerView'));
        }

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
            'url_register' => $this->getValue('public_signup') ? UrlBuilder::makeUrl('Auth', 'registerView') : null,
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
            $this->sendError('Veuillez vérifier les champs invalides', $validator->getErrors());
        }

        $user = $this->repository->user->findByLogin($data['login']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->sendError('Nom d\'utilisateur ou mot de passe incorrect');
        }

        if ($user['status'] == Constants::STATUS_INACTIVE) {
            $this->sendError('Votre adresse email doit être vérifiée');
        }

        $this->createSessionData($user);
        $this->sendSuccess('Vous êtes connecté', [
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
                    'link_reset_password' => UrlBuilder::makeAbsoluteUrl('Auth', 'passwordUpdateView', [
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
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /update-password
    public function passwordUpdateView()
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
                'url_form' => UrlBuilder::makeUrl('Auth', 'passwordUpdateAction'),
                'is_token_valid' => true,
                'has_expired' => false,
                'title' => $validation_token['type'] == Constants::TOKEN_EMAIL_CONFIRM ? 'Création de votre mot de passe' : 'Réinitialisation du mot de passe',
                'reference' => $token_reference,
                'token' => $token,
            ];
        }
        $view_data['url_back'] = UrlBuilder::makeUrl('Auth', 'loginView');
        $this->render('password_reset', $view_data);
    }

    # /update-password-send
    public function passwordUpdateAction()
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
                $this->sendError(Constants::ERROR_UNKNOWN);
            }

            Database::beginTransaction();
            # Update user password
            $update_data = [
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'updated_at' => Formatter::getDateTime()
            ];
            # Update status to active for confirmation case
            if ($validation_token['type'] == Constants::TOKEN_EMAIL_CONFIRM) {
                $update_data['status'] = Constants::STATUS_ACTIVE;
            }
            $this->repository->user->update($validation_token['user_id'], $update_data);

            # Delete token
            $validation_token_repository->remove($validation_token['id']);


            Database::commit();
            $this->sendSuccess(Constants::SUCCESS_SAVED, [
                'url_next' => UrlBuilder::makeUrl('Auth', 'loginView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
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

        $view_data['url_login'] = UrlBuilder::makeUrl('Auth', 'loginView');
        $this->render('account_confirm', $view_data);
    }

    # /register
    public function registerView()
    {
        if (!$this->getValue('public_signup')) {
            throw new HttpNotFoundException('Page not found');
        }

        if ($this->session->isLoggedIn()) {
            $this->router->redirect(UrlBuilder::makeUrl('Home', 'dashboardView'));
        }

        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Auth', 'registerAction'),
        ];

        $this->render('register', $view_data);
    }

    # /register-send
    public function registerAction()
    {
        if (!$this->getValue('public_signup')) {
            $this->sendError('Bien tenté');
        }
        try {
            $form_data = [
                'username' => $this->request->post('username'),
                'email' => $this->request->post('email'),
                'password' => $this->request->post('password'),
                'password_confirm' => $this->request->post('password_confirm'),
            ];

            # Check for duplicate
            if ($found = $this->repository->user->findByUsernameOrEmail($form_data['username'], $form_data['email'], 0)) {
                if ($found['email'] === $form_data['email']) {
                    $this->sendError('Cette adresse email est déjà pris');
                } else {
                    $this->sendError('Ce nom d\'utilisateur est déjà pris');
                }
            }

            $validator = new Validator(FormRegistry::getUserRegistration());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs invalides', $validator->getErrors());
            }

            Database::beginTransaction();

            # Create user
            $user_id = $this->repository->user->create([
                'username' => $form_data['username'],
                'password' => password_hash($form_data['password'], PASSWORD_DEFAULT),
                'email' => $form_data['email'],
                'role' => $this->getValue(Constants::STG_ROLE),
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
                    'link_confirm' => UrlBuilder::makeAbsoluteUrl('Auth', 'confirmAccountView', [
                        'ref' => $token_reference->get(),
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            Database::commit();
            $this->sendSuccess('Vous êtes bien inscrit ! Veuillez confirmer votre compte', [
                'url_next' => UrlBuilder::makeUrl('Auth', 'loginView'),
                'url_next_delay' => 2
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
    }

    private function createSessionData(array $user_data)
    {
        $permissions = array_map(fn($el) => (int)$el['id'], $this->repository->rolePermission->findAllPermissionsByRole((int)$user_data['role']));
        $this->session->setData([
            'user_id' => $user_data['id'],
            'user_role' => $user_data['role'],
            'permissions' => $permissions,
            'csrf_token' => (new Token())->generate()->getEncoded()
        ]);
    }
}
