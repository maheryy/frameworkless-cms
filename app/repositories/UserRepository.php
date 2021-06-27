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
