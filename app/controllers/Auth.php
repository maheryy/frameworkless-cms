<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Router;
use App\Core\Utils\Expr;
use App\Core\Utils\Mailer;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;
use App\Models\User;

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

        $url_form_params = Request::url('redirect') ? ['redirect' => urlencode(Request::url('redirect'))] : [];
        $this->setData([
            'url_form' => UrlBuilder::makeUrl('Auth','loginAction', $url_form_params),
//            'url_forgotten_password' => UrlBuilder::makeUrl('Auth', 'passwordRecoveryView'),
            'url_forgotten_password' => '#'
        ]);

        $this->render('login', 'login');
    }

    # /auth/password-recovery
    public function passwordRecoveryView()
    {

    }

    # /auth/login
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

        $user = (new User())->getBy([Expr::like('username', $data['login'])], Database::FETCH_ONE);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->sendError('Nom d\'utilisateur ou mot de passe incorrect');
            return;
        }

        $this->createSessionData($user);
        $this->sendSuccess('Bien joué ! Tu es connecté', [
            'url_next' => isset($_GET['redirect']) ? urldecode($_GET['redirect']) : UrlBuilder::makeUrl('Home', 'defaultView'),
        ]);
    }

    # /auth/register
    public function registerAction()
    {
        $fake_post = [
            'login' => 'admin',
            'username' => 'admin',
            'password' => 'adm',
            'email' => 'admin@admin.com',
        ];

        [
            'login' => $login,
            'username' => $username,
            'password' => $password,
            'email' => $email,
        ] = $fake_post;

        $password = password_hash($password, PASSWORD_DEFAULT);

        $user = new User();
        $user->setPassword($password);
        $user->setLogin($login);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRole(3);

        $user->save(Database::SAVE_IGNORE_NULL);
    }

    # /auth/logout
    public function logoutAction()
    {
        if (Request::getCookie(session_name())) {
            Request::deleteCookie(session_name());
            Session::stop();
        }

        $url_params = Request::get('timeout') ? ['redirect' => urlencode(Request::url('redirect')), 'timeout' => 1] : [];
        $this->router->redirect(UrlBuilder::makeUrl('Auth', 'loginView', $url_params));
    }

    # /auth/test
    public function test()
    {
        $mail = Mailer::send([
            'to' => 1,
            'subject' => 'Testing SMTP',
            'content' => View::getHtml('email/test_email', ['var1' => "HELLO", 'var2' => "WORLD"]),
        ]);

        echo $mail['message'];
    }

    private function createSessionData(array $user_data)
    {
        Session::load([
            'user_id' => $user_data['id'],
            'user_role' => $user_data['role'],
            'is_admin' => $user_data['role'] == 3,
        ]);
    }
}
