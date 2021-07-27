<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\HttpNotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Request;
use App\Core\Utils\UrlBuilder;

class Review extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!$this->getValue('review_active')) {
            if (Request::isPost()) $this->sendError('Le service est indisponible, veuillez réessayer');

            throw new HttpNotFoundException('Page not found');
        }
    }

    # /reviews
    public function listView()
    {
        $reviews = $this->repository->review->findAll();
        $this->setCSRFToken();
        $view_data = [
            'reviews' => $reviews,
            'review_statuses' => Constants::getReviewStatuses(),
            'url_action' => UrlBuilder::makeUrl('Review', 'reviewAction'),
            'can_manage' => $this->hasPermission(Constants::PERM_MANAGE_REVIEW),
            'can_delete' => $this->hasPermission(Constants::PERM_DELETE_REVIEW)
        ];
        $this->render('review_list', $view_data);
    }

    # /review-action
    public function reviewAction()
    {
        $this->validateCSRF();
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }

        # Double check permissions
        if ($this->request->get('action') === 'delete' && !$this->hasPermission(Constants::PERM_DELETE_REVIEW)
            || $this->request->get('action') !== 'delete' && !$this->hasPermission(Constants::PERM_MANAGE_REVIEW)
        ) {
            $this->sendError(Constants::ERROR_FORBIDDEN);
        }

        $status = null;
        switch ($this->request->get('action')) {
            case 'hold':
                $status = Constants::REVIEW_PENDING;
                break;
            case 'reject':
                $status = Constants::REVIEW_INVALID;
                break;
            case 'approve':
                $status = Constants::REVIEW_VALID;
                break;
            case 'delete':
                $status = Constants::STATUS_DELETED;
                break;
        }

        $this->repository->review->update((int)$this->request->get('id'), ['status' => $status]);
        $this->sendSuccess('', [
            'url_next' => UrlBuilder::makeUrl('Review', 'listView'),
        ]);
    }
}
