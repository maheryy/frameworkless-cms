<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Session;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Page extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /pages
    public function listView()
    {
        $pages = $this->repository->post->findAllPages();
        $statuses = Page::getPostStatuses();
        foreach ($pages as $key => $page) {
            $pages[$key]['url_detail'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $pages[$key]['url_delete'] = UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]);
            $pages[$key]['status'] = $statuses[$page['status']];
        }

        $view_data = [
            'pages' => $pages
        ];
        $this->render('page_list', $view_data);
    }

    # /pages-save
    public function listAction()
    {

    }

    # /new-page
    public function createView()
    {
        $this->setCSRFToken();
        $view_data = [
            'url_form' => UrlBuilder::makeUrl('Page', 'createAction')
        ];
        $this->render('page_new', $view_data);
    }

    # /new-page-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $form_data = $this->request->allPost();
            //$validator = new Validator(FormRegistry::getUserNew());
            //if (!$validator->validate($form_data)) {
            //    $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            //}
            $page_id = $this->repository->post->create([
                'author_id' => $this->session->getUserId(),
                'title' => 'Test',
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_PAGE,
                'status' => Constants::STATUS_DRAFT
            ]);

            $this->sendSuccess('Page créée', [
                'url_next' => UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page_id])
            ]);
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /page
    public function pageView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cette page n\'existe pas');
        }
        $page = $this->repository->post->find($this->request->get('id'));
        if (!$page) {
            throw new NotFoundException('Cette page n\'est pas trouvé');
        }

        $this->setContentTitle($page['title']);
        $this->setCSRFToken();
        $view_data = [
            'page' => $page,
            'url_form' => UrlBuilder::makeUrl('Page', 'pageAction'),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]),
        ];
        $this->render('page_detail', $view_data);
    }

    # /page-save
    public function pageAction()
    {
        $this->validateCSRF();
        try {

            $form_data = $this->request->allPost();
            //$validator = new Validator(FormRegistry::getUserDetail());
            //if (!$validator->validate($form_data)) {
            //    $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            //}
            //
            $update_fields = [
                'content' => $_POST['post_content'],
                'title' => 'Updated',
                'updated_at' => Formatter::getDateTime()
            ];

            $this->repository->post->update($form_data['page_id'], $update_fields);

            $this->sendSuccess('Informations sauvegardées');
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }

    # /delete-page
    public function deleteAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }
        $this->repository->post->remove($this->request->get('id'));
        $this->sendSuccess('Page supprimée', [
            'url_next' => UrlBuilder::makeUrl('Page', 'listView'),
            'delay_url_next' => 0,
        ]);
    }


    public static function getPostStatuses()
    {
        return [
            Constants::STATUS_DRAFT => 'Brouillon',
            Constants::STATUS_PUBLISHED => 'Publié',
        ];
    }
}
