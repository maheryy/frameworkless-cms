<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Models\User;
use App\Core\Router;

class Home extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function defaultView()
    {
        $this->setData([
            'name' => 'nonnn',
            'test' => 'bla',
            'age' => 8
        ]);
        $this->setParam('test2', 'ouifdsfds');

        return $this->render('default');
    }

    public function loginAction()
    {

        $session_data = [
            'user_id' => 6,
            'user_role' => 2,
            'is_admin' => true,
            'lang' => 'FR',
        ];

        var_dump('session: ' . Session::isActive());
        Session::load($session_data);
        Session::set('added', 2);

        return $this->send('Connected');
    }
    public function logoutAction()
    {

        var_dump(Session::getUserId());
        var_dump(Session::get('exist'));
        Session::set('exist', 47);
        Session::set('exist2', 'tkt');
        var_dump(Session::getAll());

        var_dump('session: ' . Session::isActive());
        Session::stop();
        var_dump('session: ' . Session::isActive());

        return $this->send('Disconnected');
    }

    public function listArticleView()
    {
        // var_dump(Session::getAll());
        $this->setData([
            'form_action' => Router::getRoute('Home', 'listArticleAction'),
            'default_name' => 'default valuuuuue',
            'default_title' => 'Super title',
            'default_test' => 'not a test',
            'default_select' => 2
        ]);

        $this->setParam('select_options', [
            [
                'value' => 0,
                'label' => '1st element'
            ],
            [
                'value' => 1,
                'label' => '2nd element'
            ],
            [
                'value' => 2,
                'label' => '3rd element'
            ],
            [
                'value' => 3,
                'label' => '4ths element'
            ],
        ]);


        return $this->render('list_article');
    }

    public function listArticleAction()
    {

        // Request::setCookie('test_cookie', 4585);
        var_dump(Request::allGet());
        var_dump(Request::allPost());
        var_dump(Request::allRequest());
        var_dump(Request::get('test'));

        // var_dump(Request::allCookie());
        // Request::deleteCookie('test_cookie');
        return $this->sendJSON([
            'success' => true,
            'msg' => 'Action executed'
        ]);
    }

    public function registerView()
    {
        $data = [
            'login' => 'Bonjour',
            'password' => 'bjr^wd',
            'username' => 'Bonj',
            'email' => 'yoqqsdzs@test.com',
            'role' => '4',
            'status' => '1'
        ];

        // $test_user = new User();
        // $test_user->populate($data);
        // $test_user->save();

        // $test_user->setId(33);
        // $test_user->setUsername('AHAHA');
        // $test_user->setEmail('noooon@test.com');
        // $test_user->save();

        $this->render('default');
    }
}
