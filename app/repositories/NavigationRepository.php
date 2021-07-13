<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Models\Navigation;

class NavigationRepository extends BaseRepository
{
    public function __construct(Navigation $model)
    {
        parent::__construct($model);
    }

    public function findAll()
    {
        $this->queryBuilder
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->orderAsc('type')
            ->orderDesc('status');

        return $this->model->fetchAll($this->queryBuilder);

    }
    public function setAllInactive(int $type)
    {
        return $this->model->updateQuery(['status' => Constants::STATUS_INACTIVE], [Expr::eq('type', $type)]);
    }

    public function remove(int $id)
    {
        return $this->model->deleteQuery([Expr::eq('id', $id)]);
    }
}
