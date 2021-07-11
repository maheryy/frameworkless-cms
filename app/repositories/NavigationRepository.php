<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Models\Navigation;

class NavigationRepository extends BaseRepository
{
    public function __construct(Navigation $model)
    {
        parent::__construct($model);
    }

    public function findNavigation(int $id)
    {
        $nav_item_table = Formatter::getTableName('navigation_item');
        $this->queryBuilder
            ->select([
                "$nav_item_table.*",
                "$this->table.*",
            ])
            ->joinInner($nav_item_table, "$this->table.id = $nav_item_table.navigation_id")
            ->where(Expr::eq("$this->table.id", $id));

        return $this->model->fetchAll($this->queryBuilder);
    }
}
