<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Router;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\ConstantManager;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
use App\Core\Utils\Seeder;
use App\Core\Utils\Token;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;
use Exception;

class Installer extends Controller
{

    public function __construct(array $options = [])
    {
        //parent::__construct($options);
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->setTemplate('back_office');

        # Redirect to app reset when everything is setup
        //if (Database::isReady()) {
        //    $this->installerResetView();
        //}

    }

    public function installerResetView()
    {

        throw new Exception('Already installed');
        $this->render('installer_reset');
    }

    # /installer-register
    public function installerRegisterView()
    {
        if (!Database::isReady()) {
            $this->router->redirect(UrlBuilder::makeUrl('Installer', 'installerDatabaseView'));
        }

        $this->setParam('url_form', UrlBuilder::makeUrl('Installer', 'registerAction'));
        $this->render('installer_register');
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
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $form_data['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);

            Database::beginTransaction();
            $this->loadSeeders();
            $this->repository->settings->updateSettings([
                Constants::STG_TITLE => $form_data['website_title'],
                Constants::STG_EMAIL_ADMIN => $form_data['email'],
                Constants::STG_EMAIL_CONTACT => $form_data['email_contact'],
            ]);

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
                    'link_confirm' => UrlBuilder::makeAbsoluteUrl('User', 'confirmAccountView', [
                        'ref' => $token_reference->get(),
                        'token' => $token->getEncoded()
                    ]),
                ]),
            ]);

            if (!$mail['success']) {
                $this->sendError($mail['message']);
            }

            Database::commit();
            $this->sendSuccess('Installation terminé', [
                'url_next' => UrlBuilder::makeUrl('User', 'loginView')
            ]);
        } catch (Exception $e) {
            Database::rollback();
            $this->sendError('Une erreur est survenue :' . $e->getMessage());
        }
    }

    # /installer-db
    public function installerDatabaseView()
    {
        $view_data = [
            'opts_try_connection' => ['add_data' => ['try_connection' => 1]],
            'url_form' => UrlBuilder::makeUrl('Installer', 'loadDatabaseAction')
        ];

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

        $this->render('installer_db', $view_data);
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
            $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
        }

        try {
            $pdo = Database::connect($data['db_host'], $data['db_name'], $data['db_user'], $data['db_password']);

            if (!Mailer::connect($data['smtp_host'], 587, $data['smtp_user'], $data['smtp_password'])) {
                $this->sendError('Impossible de se connecter au serveur SMTP');
            }
            if ($this->request->post('try_connection')) {
                $this->sendSuccess('Connexion réussie !');
            }

            try {
                $sql = preg_replace("/{PREFIX[0-9]*}/", $data['db_prefix'], file_get_contents(PATH_SQL_DUMP));
                $pdo->prepare($sql)->execute();

                $data['app_debug'] = 1;
                $data['app_dev'] = 1;

                self::generateConfig($data);
                $this->sendSuccess('La base de donnéees est prête', [
                    'url_next' => UrlBuilder::makeUrl('Installer', 'installerRegisterView')
                ]);
            } catch (Exception $e) {
                $this->sendError('Une erreur est survenue durant le traitement :' . $e->getMessage());
            }
        } catch (Exception $e) {
            $this->sendError('Veuillez vérifier les informations de connexion à la base de donnée');
        }

    }

    public function loadSeeders()
    {
        $seeders = Seeder::getAvailableSeeders();
        foreach ($seeders as $seeder) {
            $this->repository->{$seeder}->runSeed();
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
}
