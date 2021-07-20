<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Models\Menu;

class MenuRepository extends BaseRepository
{
    public function __construct(Menu $model)
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

    public function remove(int $id)
    {
        return $this->model->deleteQuery([Expr::eq('id', $id)]);
    }
}
