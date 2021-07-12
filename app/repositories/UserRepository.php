<?php

namespace App\Repositories;

use App\Core\BaseRepository;
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

    public function findAll()
    {
        $role_table = Formatter::getTableName('role');
        $this->queryBuilder
            ->select(["$this->table.*", 'role_name' => "$role_table.name"])
            ->joinInner($role_table, "$this->table.role = $role_table.id");

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findByLogin(string $login)
    {
        $this->queryBuilder
            ->where(
                Expr::like('username', $login),
                Expr::like('email', $login)
            );

        return $this->model->fetchOne($this->queryBuilder);
    }

    public function findByEmail(string $email)
    {
        $this->queryBuilder->where(Expr::like('email', $email));
        return $this->model->fetchOne($this->queryBuilder);
    }

    public function updatePassword(int $id, string $password)
    {
        return $this->model->updateQuery(
            ['password' => $password, 'updated_at' => Formatter::getDateTime()],
            [Expr::eq('id', $id)]
        );
    }
}
