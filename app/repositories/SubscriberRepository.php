<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Models\Subscriber;

class SubscriberRepository extends BaseRepository
{
    public function __construct(Subscriber $model)
    {
        parent::__construct($model);
    }

    public function findAll()
    {
        $this->queryBuilder->where(Expr::neq('status', Constants::STATUS_INACTIVE));
        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findSubscriber(string $email)
    {
        $this->queryBuilder->where(Expr::like('email', $email));
        return $this->model->fetchOne($this->queryBuilder);
    }

    public function unsubscribe(int $id)
    {
        return $this->update($id, ['status' => Constants::STATUS_INACTIVE]);
    }

    public function subscribe(string $email)
    {
        return $this->create(['email' => $email, 'status' => Constants::STATUS_ACTIVE]);
    }
}
