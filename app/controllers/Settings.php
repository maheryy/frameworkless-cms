<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

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

        ];
        $this->render('settings', $view_data);
    }

    # /settings-save
    public function settingsAction()
    {
        $this->validateCSRF();
        try {
//            Database::beginTransaction();


            $this->sendSuccess('Informations sauvegardés');
//            Database::commit();
        } catch (\Exception $e) {
//            Database::rollback();
            $this->sendError('Une erreur est survenue', [$e->getMessage()]);
        }
    }

}
