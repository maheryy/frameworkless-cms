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
        $statuses = Constants::getPageStatuses();
        foreach ($pages as $key => $page) {
            $pages[$key]['url_detail'] = UrlBuilder::makeUrl('Page', 'pageView', ['id' => $page['id']]);
            $pages[$key]['url_delete'] = UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]);
            $pages[$key]['status_label'] = $statuses[$page['status']];
        }

        $view_data = [
            'pages' => $pages,
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_PAGE),
            'can_read' => $this->hasPermission(Constants::PERM_READ_PAGE),
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
        $users = $this->repository->user->findAll();
        $view_data = [
            'users' => $users,
            'current_user_id' => $this->session->getUserId(),
            'visibility_types' => Constants::getVisibilityTypes(),
            'url_form' => UrlBuilder::makeUrl('Page', 'createAction')
        ];
        $this->render('page_new', $view_data);
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
            $slug = Formatter::slugify($this->request->post('slug') ?? $this->request->post('title'));

            # Check for duplicate
            if ($found = $this->repository->post->findPageByTitleOrSlug($this->request->post('title'), $slug, 0)) {
                $this->sendError('le ' . ($found['slug'] === $slug ? 'slug "' . $slug . '"' : 'titre "' . $this->request->post('title') . '"') . ' existe déjà');
            }

            Database::beginTransaction();
            $page_id = $this->repository->post->create([
                'author_id' => $this->request->post('author'),
                'title' => $this->request->post('title'),
                'content' => $_POST['post_content'],
                'type' => Constants::POST_TYPE_PAGE,
                'status' => $this->request->post('action_publish') ? Constants::STATUS_PUBLISHED : Constants::STATUS_DRAFT,
                'published_at' => $this->request->post('action_publish') ? Formatter::getDateTime(null, Formatter::DATE_TIME_FORMAT) : null
            ]);

            $this->repository->pageExtra->create([
                'post_id' => $page_id,
                'slug' => $slug,
                'visibility' => $this->request->post('visibility'),
                'meta_title' => $this->request->post('meta_title') ?? $this->request->post('title'),
                'meta_description' => $this->request->post('meta_description'),
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
            ]);

            Database::commit();
            $this->sendSuccess('Page créée', [
                'url_next' => UrlBuilder::makeUrl('Page', 'listView')
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /page
    public function pageView()
    {
        if (!$this->request->get('id')) {
            throw new \Exception('Cette page n\'existe pas');
        }
        $page = $this->repository->post->findPage($this->request->get('id'));
        if (!$page) {
            throw new NotFoundException('Cette page n\'est pas trouvé');
        }

        if (!empty($page['published_at'])) {
            $page['published_at'] = Formatter::getDateTime($page['published_at'], Formatter::DATE_FORMAT);
        }
        $users = $this->repository->user->findAll();
        $this->setContentTitle($page['title']);
        $this->setCSRFToken();
        $view_data = [
            'page' => $page,
            'users' => $users,
            'visibility_types' => Constants::getVisibilityTypes(),
            'url_form' => UrlBuilder::makeUrl('Page', 'pageAction', ['id' => $page['id']]),
            'url_delete' => UrlBuilder::makeUrl('Page', 'deleteAction', ['id' => $page['id']]),
            'can_update' => $this->hasPermission(Constants::PERM_UPDATE_PAGE),
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_PAGE),
            'can_publish' => $this->hasPermission(Constants::PERM_PUBLISH_PAGE),
        ];
        $this->render('page_detail', $view_data);
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

            $slug = Formatter::slugify($this->request->post('slug') ?? $this->request->post('title'));

            # Check for duplicate
            if ($found = $this->repository->post->findPageByTitleOrSlug($post_fields['title'], $slug, $this->request->get('id'))) {
                $this->sendError('le ' . ($found['slug'] === $slug ? 'slug "' . $slug . '"' : 'titre "' . $post_fields['title'] . '"') . ' existe déjà');
            }

            if ($this->request->post('action_publish')) {
                $post_fields['status'] = Constants::STATUS_PUBLISHED;
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            if ($this->request->post('published_at')) {
                $post_fields['published_at'] = Formatter::getDateTime($this->request->post('published_at'), Formatter::DATE_TIME_FORMAT);
            }

            $page_fields = [
                'slug' => $slug,
                'meta_title' => $this->request->post('meta_title'),
                'meta_description' => $this->request->post('meta_description'),
                'meta_indexable' => $this->request->post('display_search_engine') ? 1 : 0,
                'visibility' => $this->request->post('visibility'),
            ];

            Database::beginTransaction();
            $this->repository->post->update($this->request->get('id'), $post_fields);
            $this->repository->pageExtra->update($this->request->get('id'), $page_fields);
            Database::commit();

            $this->sendSuccess(Constants::SUCCESS_SAVED, [
                'url_next' => UrlBuilder::makeUrl('Page', 'listView')
            ]);
        } catch (\Exception $e) {
            Database::rollback();
            $this->sendError(Constants::ERROR_UNKNOWN, [$e->getMessage()]);
        }
    }

    # /delete-page
    public function deleteAction()
    {
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }
        $this->repository->post->remove($this->request->get('id'));
        $this->sendSuccess('Page supprimée', [
            'url_next' => UrlBuilder::makeUrl('Page', 'listView'),
            'url_next_delay' => Constants::DELAY_SUCCESS_REDIRECTION
        ]);
    }

    # /page-link-list
    public function getPageLinkList()
    {
        $pages = $this->repository->post->findPublishedPages();
        $links = [];
        foreach ($pages as $page) {
            $links[] = [
                'title' => $page['title'],
                'value' => $page['slug']
            ];
        }

        if (!empty($links)) {
            $this->sendJSON($links);
        }

        echo null;
    }
}
