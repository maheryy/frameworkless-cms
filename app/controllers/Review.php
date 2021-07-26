<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Utils\Constants;
use App\Core\Utils\UrlBuilder;

class Review extends Controller
{

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    # /reviews
    public function listView()
    {
        $reviews = $this->repository->review->findAll();
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
        if (!$this->request->get('id')) {
            $this->sendError(Constants::ERROR_UNKNOWN);
        }

        $status = null;
        switch ($this->request->get('action')) {
            case 'hold': $status = Constants::REVIEW_PENDING; break;
            case 'reject': $status = Constants::REVIEW_INVALID; break;
            case 'approve': $status = Constants::REVIEW_VALID; break;
            case 'delete': $status = Constants::STATUS_DELETED; break;
        }

        $this->repository->review->update($this->request->get('id'), ['status' => $status]);
        $this->sendSuccess('', [
            'url_next' => UrlBuilder::makeUrl('Review', 'listView'),
        ]);
    }
}