<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Mailer;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Settings extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /settings
    public function settingsView()
    {
        $this->setCSRFToken();
        $view_data = [
            'active_tab' => 1,
            'settings' => $this->settings,
            'roles' => $this->repository->role->findAll(),
            'smtp' => [
                'host' => SMTP_HOST,
                'port' => SMTP_PORT,
                'user' => SMTP_USERNAME,
            ],
            'url_form_general' => UrlBuilder::makeUrl('Settings', 'settingsGeneralAction'),
            'url_form_mail' => UrlBuilder::makeUrl('Settings', 'settingsMailAction'),
            'url_form_page' => UrlBuilder::makeUrl('Settings', 'settingsPageAction'),
        ];
        $this->render('settings', $view_data);
    }

    # /settings-general-save
    public function settingsGeneralAction()
    {
        $this->validateCSRF();
        try {
            $form_data = $this->request->allPost();
            $form_data['public_signup'] = isset($form_data['public_signup']) ? 1 : 0;
            $validator = new Validator(FormRegistry::getSettingsGeneral());
            if (!$validator->validate($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }
            Database::beginTransaction();
            $this->repository->settings->updateSettings($form_data);
            Database::commit();
            $this->sendSuccess('Informations sauvegardés');
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError('Une erreur est survenue', [$e->getMessage()]);
        }
    }

    # /settings-mail-save
    public function settingsMailAction()
    {
        $this->validateCSRF();
        try {
            $form_data = $this->request->allPost();
            $validator = new Validator();
            if (!$validator->validateRequiredOnly($form_data)) {
                $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            }
            $is_valid_config = Mailer::connect(
                $form_data['smtp_host'],
                $form_data['smtp_port'],
                $form_data['smtp_user'],
                $form_data['smtp_password'],
            );

            if (!$is_valid_config) {
                $this->sendError('Impossible de se connecter au serveur SMTP');
            }
            if ($this->request->post('try_connection')) {
                $this->sendSuccess('Connexion réussie');
            }

            Installer::generateConfig([
                'db_host' => DB_HOST,
                'db_name' => DB_NAME,
                'db_user' => DB_USER,
                'db_password' => DB_PWD,
                'db_prefix' => DB_PREFIX,
                'app_debug' => APP_DEBUG,
                'app_dev' => APP_DEV,
                'smtp_host' => $form_data['smtp_host'],
                'smtp_port' => $form_data['smtp_port'],
                'smtp_user' => $form_data['smtp_user'],
                'smtp_password' => $form_data['smtp_password'],
            ]);
            $this->sendSuccess('Informations sauvegardées');
        } catch (\Exception $e) {
            $this->sendError('Une erreur est survenue', [$e->getMessage()]);
        }
    }

    /*
    # /settings-page-save
    public function settingsPageAction()
    {
        $this->validateCSRF();
        try {
            Database::beginTransaction();


            $this->sendSuccess('Informations sauvegardés');
            Database::commit();
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError('Une erreur est survenue', [$e->getMessage()]);
        }
    }
    */
}
