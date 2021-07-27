<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\Role;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name, ?int $ignore_id = null)
    {
        $this->queryBuilder
            ->where(Expr::like('name', $name));
        if ($ignore_id) {
            $this->queryBuilder->where(Expr::neq('id', $ignore_id));
        }
        return $this->model->fetchOne($this->queryBuilder);
    }
}
