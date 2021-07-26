<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\ForbiddenAccessException;
use App\Core\Model;
use App\Core\Router;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\ConstantManager;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
use App\Core\Utils\Seeder;
use App\Core\Utils\Session;
use App\Core\Utils\Token;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;
use Exception;

class Installer extends Controller
{

    public function __construct(array $options = [])
    {
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->setTemplate('default');

        # Redirect to app reset when everything is setup
        if (!Request::isPost() && Database::isReady()) {
            $this->installerResetView();
        }

    }


    public function installerResetView()
    {
        $session = new Session(true);
        $session->init();
        if (!$session->isLoggedIn() || !$session->isSuperAdmin()) {
            throw new ForbiddenAccessException(Constants::ERROR_FORBIDDEN);
        }

        $this->setParam('csrf_token', $session->getCSRFToken());
        $this->setParam('url_drop', UrlBuilder::makeUrl('Installer', 'dropAllAction'));
        $this->setParam('url_back', UrlBuilder::makeUrl('Home', 'dashboardView'));

        $this->render('installer_reset');
    }

    # /installer
    public function installerView()
    {
        $step = $this->request->get('step') ?? 1;

        if ($step == 2) {
            $view = 'installer_register';
            if (!ConstantManager::isConfigLoaded()) {
                $this->router->redirect(UrlBuilder::makeUrl('Installer', 'installerView', ['step' => 1]));
            }
            $this->setParam('url_form', UrlBuilder::makeUrl('Installer', 'registerAction'));
        } else {
            $view = 'installer_db';
            if (ConstantManager::isConfigLoaded()) {
                $this->setParam('config', [
                    'db_host' => DB_HOST,
                    'db_name' => DB_NAME,
                    'db_user' => DB_USER,
                    'db_password' => DB_PWD,
                    'db_prefix' => DB_PREFIX,
                    'smtp_host' => SMTP_HOST,
                    'smtp_user' => SMTP_USERNAME,
                    'smtp_password' => SMTP_PASSWORD,
                ]);
            }

            $this->setParam('opts_try_connection', ['add_data' => ['try_connection' => 1]]);
            $this->setParam('url_form', UrlBuilder::makeUrl('Installer', 'loadDatabaseAction'));
        }

        $this->render($view);
    }

    # /installer-register-save
    public function registerAction()
    {
        try {
            $form_data = [
                'website_title' => $this->request->post('website_title'),
                'username' => $this->request->post('username'),
                'password' => $this->request->post('password'),
                'password_confirm' => $this->request->post('password_confirm'),
                'email' => $this->request->post('email'),
                'email_contact' => $this->request->post('email_contact'),
            ];

            $validator = new Validator(FormRegistry::getInstallerRegistration());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs invalides', $validator->getErrors());
            }

            $form_data['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);

            Database::beginTransaction();

            # Create tables
            $sql = preg_replace("/{PREFIX[0-9]*}/", DB_PREFIX, file_get_contents(PATH_SQL_DUMP));
            Database::execute($sql);

            # Load seeders
            $this->loadSeeders();
            # Set main settings
            $this->repository->settings->updateSettings([
                Constants::STG_TITLE => $form_data['website_title'],
                Constants::STG_EMAIL_ADMIN => $form_data['email'],
                Constants::STG_EMAIL_CONTACT => $form_data['email_contact'],
            ]);

            # Create user
            $user_id = $this->repository->user->create([
                'username' => $form_data['username'],
                'email' => $form_data['email'],
                'password' => $form_data['password'],
                'status' => Constants::STATUS_INACTIVE,
                'role' => Constants::ROLE_SUPER_ADMIN
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
            $this->sendSuccess('Installation terminée !', [
                'url_next' => UrlBuilder::makeUrl('Auth', 'loginView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /installer-db-save
    public function loadDatabaseAction()
    {
        $data = [
            'db_name' => $this->request->post('db_name'),
            'db_user' => $this->request->post('db_user'),
            'db_password' => $this->request->post('db_password'),
            'db_host' => $this->request->post('db_host'),
            'db_prefix' => $this->request->post('db_prefix'),
            'smtp_host' => $this->request->post('smtp_host'),
            'smtp_user' => $this->request->post('smtp_user'),
            'smtp_password' => $this->request->post('smtp_password'),
        ];

        $validator = new Validator();
        if (!$validator->validateRequiredOnly($data)) {
            $this->sendError('Veuillez vérifier les champs invalides', $validator->getErrors());
        }

        try {
            Database::connect($data['db_host'], $data['db_name'], $data['db_user'], $data['db_password']);

            if (!Mailer::connect($data['smtp_host'], 587, $data['smtp_user'], $data['smtp_password'])) {
                $this->sendError('Impossible de se connecter au serveur SMTP');
            }
            if ($this->request->post('try_connection')) {
                $this->sendSuccess('Connexion réussie !');
            }

            $data['app_debug'] = 1;
            $data['app_dev'] = 1;

            self::generateConfig($data);
            $this->sendSuccess('La base de donnéees est prête', [
                'url_next' => UrlBuilder::makeUrl('Installer', 'installerView', ['step' => 2]),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
            ]);
        } catch (Exception $e) {
            $this->sendError('Veuillez vérifier les informations de connexion à la base de données');
        }

    }

    public static function generateConfig(array $data)
    {
        $content = <<<CONF
DB_HOST={$data['db_host']}
DB_NAME={$data['db_name']}
DB_USER={$data['db_user']}
DB_PWD={$data['db_password']}
DB_PREFIX={$data['db_prefix']}

APP_DEBUG={$data['app_debug']}
APP_DEV={$data['app_dev']}

SMTP_HOST={$data['smtp_host']}
SMTP_PORT=587
SMTP_USERNAME={$data['smtp_user']}
SMTP_PASSWORD={$data['smtp_password']}
CONF;

        file_put_contents(ConstantManager::$env_path, $content, LOCK_EX);
    }

    # /seed
    public function loadSeeders()
    {
        $seeders = Seeder::getAvailableSeeders();
        foreach ($seeders as $seeder) {
            $this->repository->{$seeder}->runSeed();
        }
    }

    # /installer-drop-all
    public function dropAllAction()
    {
        $this->session = new Session();
        if (!$this->session->isLoggedIn() || !$this->session->isSuperAdmin()) $this->sendError(Constants::ERROR_FORBIDDEN);
        $this->validateCSRF();

        $tables = Model::getAllTables();
        try {
            foreach ($tables as $table) {
                Database::execute('DROP TABLE IF EXISTS ' . Formatter::getTableName($table));
            }

            if ($this->request->getCookie(session_name())) {
                $this->request->deleteCookie(session_name());
                $this->session->stop();
            }

            $this->sendSuccess('Base de données réinitialisé !', [
                'url_next' => UrlBuilder::makeUrl('Installer', 'installerView'),
                'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION,
            ]);
        } catch (\Exception $e) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
    }
}
