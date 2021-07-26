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
        $newsletters = $this->repository->post->findAllNewsletters();
        $view_data = [
            'newsletters' => $newsletters,
            'statuses' => Constants::getNewsletterStatuses(),
//            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_PAGE),
//            'can_read' => $this->hasPermission(Constants::PERM_READ_PAGE),
            'can_delete' => true,
            'can_read' => true,
        ];
        $this->render('newsletter_list', $view_data);
    }


    # /new-newsletter
    public function createView()
    {
        $this->setCSRFToken();
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Newsletter', 'createAction')
        ];
        $this->render('newsletter_new', $view_data);
    }

    # /new-newsletter-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            if (!Validator::isValid($this->request->post('title'))) {
                $this->sendError('Le sujet est obligatoire');
            }

            Database::beginTransaction();
            $newsletter_id = $this->repository->post->create([
                'author_id' => $this->session->getUserId(),
                'title' => $this->request->post('title'),
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_NEWSLETTER,
                'status' => Constants::STATUS_DRAFT,
            ]);

            Database::commit();
            $this->sendSuccess('Newsletter créée', [
                'url_next' => UrlBuilder::makeUrl('Newsletter', 'listView')
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /newsletter
    public function newsletterView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cette newsletter n\'existe pas');
        }

        if ((!$newsletter = $this->repository->post->find($this->request->get('id')))) {
            throw new NotFoundException('Cette newsletter n\'est pas trouvé');
        }

        $this->setContentTitle($newsletter['title']);
        $this->setCSRFToken();
        $view_data = [
            'newsletter' => $newsletter,
            'url_form' => UrlBuilder::makeUrl('Newsletter', 'newsletterAction', ['id' => $newsletter['id']]),
            'url_delete' => UrlBuilder::makeUrl('Newsletter', 'deleteAction', ['id' => $newsletter['id']]),
//            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_PAGE),
//            'can_update' => $this->hasPermission(Constants::PERM_READ_PAGE),
            'can_delete' => true,
            'can_update' => true,
        ];
        $this->render('newsletter_detail', $view_data);
    }

    # /newsletter-save
    public function newsletterAction()
    {
        $this->validateCSRF();
        try {
            if (!Validator::isValid($this->request->post('title'))) {
                $this->sendError('Le sujet est obligatoire');
            }

            Database::beginTransaction();
            $this->repository->post->update($this->request->get('id'),[
                'title' => $this->request->post('title'),
                'content' => $_POST['post_content'],
            ]);

            Database::commit();
            $this->sendSuccess(Constants::SUCCESS_SAVED, [
                'url_next' => UrlBuilder::makeUrl('Newsletter', 'newsletterView', ['id' => $this->request->get('id')])
            ]);
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
        $this->sendSuccess('Newsletter supprimée', [
            'url_next' => UrlBuilder::makeUrl('Newsletter', 'listView'),
        ]);
    }


    # /send-newsletter
    public function sendNewsletterView()
    {

    }

    # /send-newsletter-send
    public function sendNewsletterAction()
    {

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
