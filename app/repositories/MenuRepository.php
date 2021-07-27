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
            ->orderAsc('type');

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findMenuLinks()
    {
        $this->queryBuilder
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->where(Expr::eq('type', Constants::MENU_LINKS));

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findMenuSocials()
    {
        $this->queryBuilder
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->where(Expr::eq('type', Constants::MENU_SOCIALS));

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findByTitle(string $title, ?int $ignore_id = null)
    {
        $this->queryBuilder
            ->where(Expr::like('title', $title));
        if ($ignore_id) {
            $this->queryBuilder->where(Expr::neq('id', $ignore_id));
        }
        return $this->model->fetchOne($this->queryBuilder);
    }

    public function remove(int $id)
    {
        return $this->model->deleteQuery([Expr::eq('id', $id)]);
    }
}
