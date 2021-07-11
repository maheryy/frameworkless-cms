<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
