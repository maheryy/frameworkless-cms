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
        $this->setTemplate('default');
        $this->debug = defined('APP_DEBUG') ? APP_DEBUG : false;
    }

    public function displayErrorDefault(Exception $error)
    {
        $view_data = [
            'error_title' => 'Error',
            'error_message' => $error->getMessage()
        ];
        $this->render('error_default', $view_data);
    }

    public function displayErrorNotFound(NotFoundException $error)
    {
        $view_data = [
            'error_title' => 'Not found',
        ];
        if ($this->debug) {
            $view_data['error_message'] = $error->getMessageDetails();
        } else {
            $view_data['error_message'] = $error;
        }
        $this->render('error_default', $view_data);
    }

    public function displayErrorNoAccess(ForbiddenAccessException $error)
    {
        $view_data = [
            'error_title' => 'No access',
        ];
        if ($this->debug) {
            $view_data['error_message'] = $error->getMessageDetails();
        } else {
            $view_data['error_message'] = $error;
        }
        $this->render('error_default', $view_data);
    }

    public function displayError404()
    {
        $this->render('error_404');
    }
}
