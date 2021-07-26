<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Models\Review;

class ReviewRepository extends BaseRepository
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function findAll(int $limit = null)
    {
        $this->queryBuilder
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->orderAsc('status');

        if($limit) {
            $this->queryBuilder->limit($limit);
        }

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findAllApproved(int $limit = null)
    {
        $this->queryBuilder
            ->where(Expr::eq('status', Constants::REVIEW_VALID))
            ->orderDesc('date');

        if($limit) {
            $this->queryBuilder->limit($limit);
        }

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findAllPending(int $limit = null)
    {
        $this->queryBuilder
            ->where(Expr::eq('status', Constants::REVIEW_PENDING))
            ->orderDesc('date');

        if($limit) {
            $this->queryBuilder->limit($limit);
        }

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findReviewByEmailAndDate(string $email, string $date)
    {
        $this->queryBuilder
            ->where(Expr::like('email', $email))
            ->where(Expr::eq('date', $date));

        return $this->model->fetchOne($this->queryBuilder);
    }
}
