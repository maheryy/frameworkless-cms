<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Models\RolePermission;

class RolePermissionRepository extends BaseRepository
{
    public function __construct(RolePermission $model)
    {
        parent::__construct($model);
    }

    public function update(int $id, array $fields)
    {
        return $this->model->updateQuery($fields, [Expr::eq('post_id', $id)]);
    }

    public function findAllPermissionsByRole(int $role_id)
    {
        $permission_table = Formatter::getTableName('permission');

        $this->queryBuilder
            ->select(["$permission_table.*"])
            ->joinInner($permission_table, "$this->table.permission_id = $permission_table.id")
            ->where(Expr::eq('role_id', $role_id));

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function deleteAllByRole(int $role_id)
    {
        return $this->model->deleteQuery([Expr::eq('role_id', $role_id)]);
    }
}
