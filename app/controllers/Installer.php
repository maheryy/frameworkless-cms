<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Utils\Repository;
use App\Core\Utils\ConstantManager;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Request;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use Exception;

class Installer extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /installer-register
    public function installerRegisterView()
    {
        if (!ConstantManager::isConfigLoaded()) {
            $this->router->redirect(UrlBuilder::makeUrl('Installer', 'installerDatabaseView'));
        }

        $this->setData([
            'url_form_action' => UrlBuilder::makeUrl('Installer', 'registerAction')
        ]);
        $this->render('installer_register');
    }

    # /installer/register-save
    public function registerAction()
    {
        try {
            $form_data = [
                'website_title' => Request::post('website_title'),
                'username' => Request::post('username'),
                'password' => Request::post('password'),
                'password_confirm' => Request::post('password_confirm'),
                'email' => Request::post('email'),
            ];

            $validator = new Validator(FormRegistry::getInstallerRegistration());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $form_data['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);

            Repository::user()->create([
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'password' => $form_data['password'],
                'role' => Constants::ROLE_SUPER_ADMIN,
            ]);

            $this->sendSuccess('Success', [
                'url_next' => UrlBuilder::makeUrl('Home', 'defaultView')
            ]);
        } catch (Exception $e) {
            $this->sendError('Une erreur est survenue durant le traitement :' . $e->getMessage());
        }
    }

    # /installer-db
    public function installerDatabaseView()
    {
        $this->setData([
            'opts_try_connection' => ['add_data' => ['try_connection' => 1]],
            'url_form_action' => UrlBuilder::makeUrl('Installer', 'loadDatabaseAction')
        ]);

        if (ConstantManager::isConfigLoaded()) {
            $this->setParam('config', [
                'db_host' => DB_HOST,
                'db_name' => DB_NAME,
                'db_user' => DB_USER,
                'db_password' => DB_PWD,
                'db_prefix' => DB_PREFIX,
            ]);
        }

        $this->render('installer_db');
    }

    # /installer/load-db
    public function loadDatabaseAction()
    {
        $data = [
            'db_name' => Request::post('db_name'),
            'db_user' => Request::post('db_user'),
            'db_password' => Request::post('db_password'),
            'db_host' => Request::post('db_host'),
            'db_prefix' => Request::post('db_prefix'),
        ];

        $validator = new Validator();
        if (!$validator->validateRequiredOnly($data)) {
            $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
        }

        try {
            $pdo = Database::connect($data['db_host'], $data['db_name'], $data['db_user'], $data['db_password']);
            if (Request::post('try_connection')) {
                $this->sendSuccess('Connexion réussie !');
            }
        } catch (Exception $e) {
            $this->sendError('Veuillez vérifier les informations de connexion à la base de donnée');
        }

        try {
            $sql = str_replace('%PREFIX%', $data['db_prefix'], file_get_contents(PATH_SQL_DUMP));
            $st = $pdo->prepare($sql);
            $st->execute();

            $this->generateConfig($data);
            $this->sendSuccess('Installation réussie', [
                'url_next' => UrlBuilder::makeUrl('Installer', 'installerRegisterView')
            ]);
        } catch (Exception $e) {
            $this->sendError('Une erreur est survenue durant le traitement :' . $e->getMessage());
        }
    }

    # /installer/test
    public function testView()
    {
        $this->setData([

        ]);
//        $this->render('installer', 'installer');
    }


    public function generateConfig(array $data)
    {
        $content = <<<CONF
DB_HOST={$data['db_host']}
DB_NAME={$data['db_name']}
DB_USER={$data['db_user']}
DB_PWD={$data['db_password']}
DB_PREFIX={$data['db_prefix']}

APP_DEBUG=1
APP_DEV=1
CONF;

        file_put_contents(ConstantManager::$env_path, $content, LOCK_EX);
    }
}
