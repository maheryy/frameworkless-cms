<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ForbiddenAccessException;
use App\Core\Exceptions\NotFoundException;
use Exception;

/**
 * ErrorController
 * 
 */
class Error extends Controller
{
    private $error_template;
    private $debug;

    public function __construct()
    {
        parent::__construct();
        $this->initSession();
        $this->error_template = 'error_default';
        $this->debug = defined('APP_DEBUG') ? APP_DEBUG : false;
        $this->setLayoutParams();
    }

    public function displayErrorDefault(Exception $error)
    {
        $this->setParam('error_title', 'default');
        $this->setParam('error_message', $error->getMessage());
        $this->render('error_default', $this->error_template);
    }

    public function displayErrorNotFound(NotFoundException $error)
    {
        $this->setParam('error_title', 'not found');
        if ($this->debug) {
            $this->setParam('error_message', $error->getMessageDetails());
        } else {
            $this->setParam('error_message', $error);
        }
        $this->render('error_default', $this->error_template);
    }

    public function displayErrorNoAccess(ForbiddenAccessException $error)
    {
        $this->setParam('error_title', 'default');
        if ($this->debug) {
            $this->setParam('error_message', $error->getMessageDetails());
        } else {
            $this->setParam('error_message', $error);
        }
        $this->render('error_default', $this->error_template);
    }

    public function displayError404()
    {
        $this->render('error_404', $this->error_template);   
    }
}
