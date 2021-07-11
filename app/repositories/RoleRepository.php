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
}
