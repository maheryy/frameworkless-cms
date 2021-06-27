<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\NavigationItem;

class NavigationItemRepository extends BaseRepository
{
    public function __construct(NavigationItem $model)
    {
        parent::__construct($model);
    }

}
