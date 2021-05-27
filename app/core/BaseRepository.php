<?php

namespace App\Core;

use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\QueryBuilder;

abstract class BaseRepository
{
    protected Model $model;
    protected string $table;

    protected function __construct(Model $model)
    {
        $this->model = $model;
        $this->table = $this->model->getTableName();
    }

    public function find(int $id)
    {
        $qb = (new QueryBuilder())
            ->from($this->table)
            ->where(Expr::eq('id', $id));

        return $this->model->fetchOne($qb);
    }

    public function findAll()
    {
        $qb = (new QueryBuilder())->from($this->table);

        if ($this->model->hasStatus()) {
            $qb->where(Expr::neq('status', Constants::STATUS_DELETED));
        }

        return $this->model->fetchAll($qb);
    }

    public function create(array $data)
    {
        return $this->model->insertQuery($data);
    }

    public function update(int $id, array $fields)
    {
        return $this->model->updateQuery($fields, [Expr::eq('id', $id)]);
    }

    public function remove(int $id)
    {
        return $this->model->hasStatus()
            ? $this->model->updateQuery(['status' => Constants::STATUS_DELETED], [Expr::eq('id', $id)])
            : $this->model->deleteQuery([Expr::eq('id', $id)]);
    }
}
