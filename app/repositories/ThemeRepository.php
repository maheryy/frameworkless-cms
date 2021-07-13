<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\Theme;

class ThemeRepository extends BaseRepository
{
    public function __construct(Theme $model)
    {
        parent::__construct($model);
    }

}
