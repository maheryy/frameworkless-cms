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
    private bool $debug;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->debug = defined('APP_DEBUG') ? APP_DEBUG : false;
    }

    public function displayErrorDefault(Exception $error)
    {
        $this->setParam('error_title', 'Error');
        $this->setParam('error_message', $error->getMessage());
        $this->render('error_default');
    }

    public function displayErrorNotFound(NotFoundException $error)
    {
        $this->setParam('error_title', 'Not found');
        if ($this->debug) {
            $this->setParam('error_message', $error->getMessageDetails());
        } else {
            $this->setParam('error_message', $error);
        }
        $this->render('error_default');
    }

    public function displayErrorNoAccess(ForbiddenAccessException $error)
    {
        $this->setParam('error_title', 'No access');
        if ($this->debug) {
            $this->setParam('error_message', $error->getMessageDetails());
        } else {
            $this->setParam('error_message', $error);
        }
        $this->render('error_default');
    }

    public function displayError404()
    {
        $this->render('error_404');
    }
}
