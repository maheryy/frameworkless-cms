<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\ConstantManager;
use App\Core\Utils\Constants;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Token;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;
use App\Core\View;
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

        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Installer', 'registerAction')
        ];
        $this->render('installer_register', $view_data);
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
            ];

            $validator = new Validator(FormRegistry::getInstallerRegistration());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }

            $form_data['password'] = password_hash($form_data['password'], PASSWORD_DEFAULT);

            Database::beginTransaction();
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
            $this->sendSuccess('Utilisateur créé', [
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
            $view_data['config'] = [
                'db_host' => DB_HOST,
                'db_name' => DB_NAME,
                'db_user' => DB_USER,
                'db_password' => DB_PWD,
                'db_prefix' => DB_PREFIX,
            ];
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
        ];

        $validator = new Validator();
        if (!$validator->validateRequiredOnly($data)) {
            $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
        }

        try {
            $pdo = Database::connect($data['db_host'], $data['db_name'], $data['db_user'], $data['db_password']);
            if ($this->request->post('try_connection')) {
                $this->sendSuccess('Connexion réussie !');
            }

            try {
                $sql = preg_replace("/{PREFIX[0-9]*}/", $data['db_prefix'], file_get_contents(PATH_SQL_DUMP));
                $st = $pdo->prepare($sql);
                $st->execute();

                $this->generateConfig($data);
                $this->sendSuccess('Installation réussie', [
                    'url_next' => UrlBuilder::makeUrl('Installer', 'installerRegisterView')
                ]);
            } catch (Exception $e) {
                $this->sendError('Une erreur est survenue durant le traitement :' . $e->getMessage());
            }
        } catch (Exception $e) {
            $this->sendError('Veuillez vérifier les informations de connexion à la base de donnée');
        }

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
