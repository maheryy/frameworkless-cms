<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Core\Utils\QueryBuilder;
use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function find(int $id)
    {
        $this->queryBuilder
            ->where(Expr::eq('id', $id))
            ->where(Expr::neq('status', Constants::STATUS_DELETED));

        return $this->model->fetchOne($this->queryBuilder);
    }

    public function findAll()
    {
        $role_table = Formatter::getTableName('role');
        $this->queryBuilder
            ->select(["$this->table.*", 'role_name' => "$role_table.name"])
            ->joinInner($role_table, "$this->table.role = $role_table.id")
            ->where(Expr::neq('status', Constants::STATUS_DELETED));

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findByLogin(string $login)
    {
        $this->queryBuilder
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->where(
                Expr::like('username', $login),
                Expr::like('email', $login)
            );

        return $this->model->fetchOne($this->queryBuilder);
    }


    public function findByUsernameOrEmail(string $username, string $email, int $ignore_id)
    {
        $this->queryBuilder
            ->where(Expr::neq('id', $ignore_id))
            ->where(Expr::neq('status', Constants::STATUS_DELETED))
            ->where(
                Expr::like('username', $username),
                Expr::like('email', $email)
            );

        return $this->model->fetchOne($this->queryBuilder);
    }
}
