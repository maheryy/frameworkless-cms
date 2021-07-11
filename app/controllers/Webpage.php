<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\UrlBuilder;
use App\Core\Utils\Validator;

class Webpage extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /new-page-save
    public function createAction()
    {
        $this->validateCSRF();
        try {
            $validator = new Validator();
            if (!$validator->validateRequiredOnly(['title' => $this->request->post('title')])) {
                $this->sendError('Le titre est obligatoire');
            }

            Database::beginTransaction();
            $page_id = $this->repository->post->create([
                'author_id' => $this->session->getUserId(),
                'title' => $this->request->post('title'),
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_PAGE,
                'status' => $this->request->post('action_publish') ? Constants::STATUS_PUBLISHED : Constants::STATUS_DRAFT,
                'published_at' => $this->request->post('action_publish') ? Formatter::getDateTime(null, Formatter::DATE_TIME_FORMAT) : null
            ]);
            $this->repository->pageExtra->create([
                'post_id' => $page_id,
                'slug' => $this->request->post('slug') ?? Formatter::slugify($this->request->post('title')),
                'visibility' => $this->request->post('visibility'),
                'allow_comments' => $this->request->post('allow_comments') ? 1 : 0,
                'meta_title' => $this->request->post('meta_title') ?? $this->request->post('title'),
                'meta_description' => $this->request->post('meta_description'),
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
            ]);

            Database::commit();
            $this->sendSuccess('Page créée', [
                'url_next' => UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page_id])
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenu", [$e->getMessage()]);
        }
    }

    # /page-save
    public function pageAction()
    {
        $this->validateCSRF();
        try {
            $validator = new Validator();
            if (!$validator->validateRequiredOnly(['title' => $this->request->post('title')])) {
                $this->sendError('Le titre est obligatoire');
            }
            $post_fields = [
                'title' => $this->request->post('title'),
                'author_id' => $this->request->post('author'),
                'content' => $_POST['post_content'],
                'updated_at' => Formatter::getDateTime()
            ];

            if ($this->request->post('action_publish')) {
                $post_fields['status'] = Constants::STATUS_PUBLISHED;
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            if ($this->request->post('published_at')) {
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            $page_fields = [
                'slug' => $this->request->post('slug'),
                'meta_title' => $this->request->post('meta_title'),
                'meta_description' => $this->request->post('meta_description'),
                'allow_comments' => $this->request->post('allow_comments') ? 1 : 0,
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
                'visibility' => $this->request->post('visibility'),
            ];

            Database::beginTransaction();
            $this->repository->post->update($this->request->get('id'), $post_fields);
            $this->repository->pageExtra->update($this->request->get('id'), $page_fields);
            Database::commit();

            $this->sendSuccess('Informations sauvegardées');
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError("Une erreur est survenue", [$e->getMessage()]);
        }
    }


    # /themes
    public function themeListView()
    {

    }

    # /import-theme
    public function importThemeView()
    {

    }

    # /import-theme-save
    public function importThemeAction()
    {

    }

    # /navigations
    public function navigationListView()
    {
        $navs = $this->repository->navigation->findAll();
        foreach ($navs as $key => $nav) {
            $pages[$key]['url_detail'] = UrlBuilder::makeUrl('Webpage', 'navigationView', ['id' => $nav['id']]);
            $pages[$key]['url_delete'] = UrlBuilder::makeUrl('Webpage', 'deleteNavigationAction', ['id' => $nav['id']]);
        }

        $view_data = [
            'navigations' => $navs,
            'new_navigation_link' => UrlBuilder::makeUrl('Webpage', 'createNavigationView')
        ];
        $this->render('nav_list', $view_data);
    }

    # /new-navigation
    public function createNavigationView()
    {
        $this->setCSRFToken();
        $view_data = [
            'pages' => $this->repository->post->findPublishedPages(),
            'nav_types' => Constants::getNavigationTypes(),
            'url_form' => UrlBuilder::makeUrl('Webpage', 'createNavigationAction')
        ];
        $this->render('nav_new', $view_data);
    }

    # /new-navigation-save
    public function createNavigationAction()
    {

    }

    # /navigation
    public function navigationView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cette navigation n\'existe pas');
        }
        $nav = $this->repository->navigation->findNavigation($this->request->get('id'));
        if (!$nav) {
            throw new NotFoundException('Cette navigation n\'est pas trouvé');
        }

        $this->setContentTitle($nav['title']);
        $this->setCSRFToken();
        $view_data = [
            'navigation' => $nav,
            'url_form' => UrlBuilder::makeUrl('Webpage', 'navigationAction', ['id' => $nav['id']]),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteNavigationAction', ['id' => $nav['id']]),
        ];
        $this->render('nav_detail', $view_data);
    }

    # /navigation-save
    public function navigationAction()
    {

    }

    # /delete-navigation
    public function deleteNavigationAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError('Une erreur est survenue');
        }
        $this->repository->navigation->remove($this->request->get('id'));
        $this->sendSuccess('Page supprimée', [
            'url_next' => UrlBuilder::makeUrl('Webpage', 'navigationListView'),
            'delay_url_next' => 0,
        ]);
    }
}
