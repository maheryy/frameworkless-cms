<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Model;
use App\Core\Utils\Request;
use App\Core\Utils\Session;
use App\Models\Random;
use App\Core\Router;
use App\Core\Utils\UrlBuilder;

/**
 * TestController dedicated to test the framework
 * - view : test.view.php
 * - template : test.tpl.php
 * - model : Random
 * 
 * If a method aims to render a view, add postfix 'View' after the method name
 * Otherwise add 'Action' if you only want to send back data
 * 
 */
class Test extends Controller
{
    const ACTION_CREATE = 1;
    const ACTION_DELETE = 2;

    public function __construct()
    {
        parent::__construct();
        $this->random_states = [
            1 => 'Tranquilized',
            2 => 'Stabilized',
            3 => 'Agitated',
            4 => 'Excited'
        ];

        $this->setLayoutParams();
    }

    public function startSessionAction()
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

        return $this->send('Session started');
    }

    public function stopSessionAction()
    {

        var_dump(Session::getUserId());
        var_dump(Session::get('exist'));
        Session::set('exist', 47);
        Session::set('exist2', 'tkt');
        var_dump(Session::getAll());

        var_dump('session: ' . Session::isActive());
        Session::stop();
        var_dump('session: ' . Session::isActive());

        return $this->send('Session stopped');
    }

    public function testCreateView()
    {
        $this->setData([
            'form_action' => UrlBuilder::getUrl('Test', 'testCreateAction'),
            'states' => $this->random_states,
        ]);


        return $this->render('test_form', 'test_default');
    }


    public function testCreateAction()
    {
        $form_data = Request::allPost();

        $model = new Random();
        $model->setEmail($form_data['email']);
        $model->setPassword($form_data['password']);
        $model->setName($form_data['name']);
        $model->setState($form_data['state']);
        $model->setStatus(Model::STATUS_DEFAULT);

        $save = $model->save();

        Router::redirect(UrlBuilder::getUrl('Test', 'testEditView', ['id' => $save['inserted_id']]));
    }

    public function testEditView()
    {
        if (empty(Request::get('id'))) {
            die('url must have id');
        }

        $model = new Random();
        $model->setId(Request::get('id'));

        $random_data = $model->getData();

        $this->setData([
            'form_action' => UrlBuilder::getUrl('Test', 'testEditAction', ['id' => $random_data['id']]),
            'states' => $this->random_states,
            'random_data' => $random_data,
        ]);

        return $this->render('test_form_edit', 'test_default');
    }

    public function testEditAction()
    {
        $form_data = Request::allPost();

        if (Request::get('id')) {
            $model = new Random();
            $model->setId(Request::get('id'));
            $model->setEmail($form_data['email']);
            $model->setPassword($form_data['password']);
            $model->setName($form_data['name']);
            $model->setState($form_data['state']);
            $model->save();

            Router::redirect(UrlBuilder::getUrl('Test', 'testEditView', ['id' => Request::get('id')]));
        }
    }

    public function testListView()
    {
        $list = (new Random())->getAll();

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]['url_edit'] = UrlBuilder::getUrl('Test', 'testEditView', ['id' => $list[$i]['id']]);
            $list[$i]['url_delete'] = UrlBuilder::getUrl('Test', 'testListAction', ['id' => $list[$i]['id'], 'action' => self::ACTION_DELETE]);
            $list[$i]['state'] = $this->random_states[$list[$i]['state']];
        }

        $this->setData([
            'form_action' => UrlBuilder::getUrl('Test', 'testListAction', ['action' => self::ACTION_CREATE]),
            'states' => $this->random_states,
            'list' => $list
        ]);

        return $this->render('test_list', 'test_default');
    }

    public function testListAction()
    {
        $model = new Random();
        if (Request::get('action') === self::ACTION_CREATE) {
            $form_data = Request::allPost();

            $model->setEmail($form_data['email']);
            $model->setPassword($form_data['password']);
            $model->setName($form_data['name']);
            $model->setState($form_data['state']);
            $model->setStatus(Model::STATUS_DEFAULT);

            $model->save();
        } else {
            $model->setId(Request::get('id'));
            $model->delete();
            // $model->deleteForever();
        }

        Router::redirect(UrlBuilder::getUrl('Test', 'testListView'));
    }
}
