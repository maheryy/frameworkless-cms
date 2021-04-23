<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Router;
use App\Core\Utils\Expr;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Core\Utils\UrlBuilder;
use App\Models\User;

class Auth extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    # /login
    public function loginView()
    {
        if (Session::isLoggedIn()) {
            $this->router->redirect(UrlBuilder::makeUrl('Home', 'defaultView'));
            exit;
        }

    }

    # /auth/login
    public function loginAction()
    {
        $fake_post = [
            'login' => 'charles',
            'password' => 'best_pwd',
        ];

        [
            'login' => $login,
            'password' => $password,
        ] = $fake_post;

        $user = (new User())->getBy([Expr::like('login', $login)], Database::FETCH_ONE);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->sendError('Bad info');
        }

        $this->createSessionData($user);
        $this->router->redirect(UrlBuilder::makeUrl('Home', 'defaultView'));
    }

    # /auth/register
    public function registerAction()
    {
        $fake_post = [
            'login' => 'charles',
            'username' => 'ch77',
            'password' => 'best_pwd',
            'email' => 'charles@gmail.com',
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
        $this->router->redirect(UrlBuilder::makeUrl('Auth', 'loginView'));
    }

    # /auth/test
    public function test()
    {
        var_dump(Session::getAll());
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
