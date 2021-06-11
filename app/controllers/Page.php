<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\FormRegistry;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
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
        $this->setContentTitle('Liste des pages');
        $pages = Repository::post()->findAllPages();
        $statuses = Page::getPostStatuses();
        foreach ($pages as $key => $page) {
            $pages[$key]['url_detail'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $pages[$key]['url_delete'] = UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]);
            $pages[$key]['status'] = $statuses[$page['status']];
        }

        $this->setData([
            'pages' => $pages
        ]);
        $this->render('page_list');
    }

    # /pages-save
    public function listAction()
    {

    }

    # /new-page
    public function createView()
    {
        $this->setContentTitle('Ajouter une page');
        $this->setCSRFToken();
        $this->setData([
            'url_form' => UrlBuilder::makeUrl('Page', 'createAction')
        ]);
        $this->render('page_new');
    }

    # /new-page-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $form_data = Request::allPost();
            //$validator = new Validator(FormRegistry::getUserNew());
            //if (!$validator->validate($form_data)) {
            //    $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            //}
            $page_id = Repository::post()->create([
                'author_id' => Session::getUserId(),
                'title' => 'Test',
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_PAGE,
                'visibility' => 1,
                'allow_comment' => 0,
                'status' => Constants::STATUS_DRAFT
            ]);

            $this->sendSuccess('Page créé', [
                'url_next' => UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page_id])
            ]);
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /page
    public function pageView()
    {
        $page_id = Request::get('id');
        if (!$page_id) {
            throw new \Exception('Cette page n\'existe pas');
        }

        $page = Repository::post()->find($page_id);
        if (!$page) {
            throw new \Exception('Cette page n\'existe pas');
        }

        $this->setContentTitle($page['title']);
        $this->setCSRFToken();
        $this->setData([
            'page' => $page,
            'url_form' => UrlBuilder::makeUrl('Page', 'pageAction'),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]),
        ]);
        $this->render('page_detail');
    }

    # /page-save
    public function pageAction()
    {
        $this->validateCSRF();
        try {

            $form_data = Request::allPost();
            //$validator = new Validator(FormRegistry::getUserDetail());
            //if (!$validator->validate($form_data)) {
            //    $this->sendError('Veuillez vérifier les champs', $validator->getErrors());
            //}
            //
            $update_fields = [
                'content' => $_POST['post_content'],
                'title' => 'Updated',
                'visibility' => 1,
                'allow_comment' => 0,
                'updated_at' => Formatter::getDateTime()
            ];

            Repository::post()->update($form_data['page_id'], $update_fields);

            $this->sendSuccess('Informations sauvegardés');
        } catch (\Exception $e) {
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }

    # /delete-page
    public function deleteAction()
    {
        $page_id = Request::get('id');
        if ($page_id) {
            Repository::post()->remove($page_id);
            $this->sendSuccess('Page supprimé', [
                'url_next' => UrlBuilder::makeUrl('Page', 'listView'),
                'delay_url_next' => 0,
            ]);
        }
    }


    public static function getPostStatuses()
    {
        return [
            Constants::STATUS_DRAFT => 'Brouillon',
            Constants::STATUS_PUBLISHED => 'Publié',
        ];
    }
}
