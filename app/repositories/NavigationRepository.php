<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\Navigation;

class NavigationRepository extends BaseRepository
{
    public function __construct(Navigation $model)
    {
        parent::__construct($model);
    }

}
