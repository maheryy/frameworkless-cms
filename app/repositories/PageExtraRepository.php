<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\PageExtra;

class PageExtraRepository extends BaseRepository
{
    public function __construct(PageExtra $model)
    {
        parent::__construct($model);
    }

    public function update(int $id, array $fields)
    {
        return $this->model->updateQuery($fields, [Expr::eq('post_id', $id)]);
    }
}
