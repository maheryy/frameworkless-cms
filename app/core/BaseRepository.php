<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\QueryBuilder;
use App\Core\Utils\Seeder;

abstract class BaseRepository
{
    protected Model $model;
    protected string $table;
    protected QueryBuilder $queryBuilder;

    protected function __construct(Model $model)
    {
        $this->model = $model;
        $this->table = $this->model->getTableName();
        $this->queryBuilder = (new QueryBuilder())->from($this->table);
    }

    public function find(int $id)
    {
        $this->queryBuilder->where(Expr::eq('id', $id));

        return $this->model->fetchOne($this->queryBuilder);
    }

    public function findAll()
    {
        if ($this->model->hasStatus()) {
            $this->queryBuilder->where(Expr::neq('status', Constants::STATUS_DELETED));
        }

        return $this->model->fetchAll($this->queryBuilder);
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

    /**
     * Database seeding : Insert initial data (from Seeder::modelName) for a given model
     *
     * @return int|bool rows affected or false
     */
    public function runSeed()
    {
        $callable = [Seeder::class, $this->model->getModelName()];
        if (!is_callable($callable)) {
            throw new NotFoundException('Seed data does not exist for ' . $this->model->getModelName());
        }
//        $this->model->truncate();
        return $this->create(call_user_func($callable));
    }
}
