<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Newsletter extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /newsletters
    public function listView()
    {
        $pages = $this->repository->post->findAllPages();
        $statuses = Constants::getPostStatuses();
        foreach ($pages as $key => $page) {
            $pages[$key]['url_detail'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $pages[$key]['url_delete'] = UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]);
            $pages[$key]['status_label'] = $statuses[$page['status']];
        }

        $view_data = [
            'pages' => $pages
        ];
        $this->render('page_list', $view_data);
    }


    # /new-newsletter
    public function createView()
    {
        $this->setCSRFToken();
        $users = $this->repository->user->findAll();
        $view_data = [
            'users' => $users,
            'current_user_id' => $this->session->getUserId(),
            'visibility_types' => Constants::getVisibilityTypes(),
            'url_form' => UrlBuilder::makeUrl('Page', 'createAction')
        ];
        $this->render('page_new', $view_data);
    }

    # /new-newsletter-save
    public function createAction()
    {
        $this->validateCSRF();
        try {

            Database::commit();
            $this->sendSuccess('Page créée', [
                'url_next' => UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page_id])
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /newsletter
    public function newsletterView()
    {
        $this->setContentTitle($page['title']);
        $this->setCSRFToken();
        $view_data = [
            'page' => $page,
            'users' => $users,
            'visibility_types' => Constants::getVisibilityTypes(),
            'url_form' => UrlBuilder::makeUrl('Page', 'pageAction', ['id' => $page['id']]),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]),
        ];
        $this->render('page_detail', $view_data);
    }

    # /newsletter-save
    public function newsletterAction()
    {
        $this->validateCSRF();
        try {
            Database::commit();
            $this->sendSuccess(Constants::SUCCESS_SAVED);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /delete-newsletter
    public function deleteAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
        $this->repository->post->remove($this->request->get('id'));
        $this->sendSuccess('Page supprimée', [
            'url_next' => UrlBuilder::makeUrl('Page', 'listView'),
            'delay_url_next' => 0,
        ]);
    }

    # /unsubscribe
    public function unsubscribeView()
    {
        if (!$this->request->get('subscriber') || !is_numeric($this->request->get('subscriber'))) {
            $error_ref = true;
        } else {
            $success = $this->repository->subscriber->unsubscribe($this->request->get('subscriber'));
        }

        $this->render('newsletter_unsubscribe', ['success' => $success ?? false, 'error' => isset($error_ref)]);
    }
}
