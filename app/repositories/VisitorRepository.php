<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Models\Visitor;

class VisitorRepository extends BaseRepository
{
    public function __construct(Visitor $model)
    {
        parent::__construct($model);
    }


    public function findUniqueVisitor(string $ip, string $uri, string $date)
    {
        $this->queryBuilder
            ->select(['COUNT(*)'])
            ->where(Expr::like('ip', $ip))
            ->where(Expr::like('uri', $uri))
            ->where(Expr::eq('date', $date));

        return (int)$this->model->fetchOne($this->queryBuilder)['COUNT(*)'];
    }

    public function countTotalUniqueVisitors()
    {
        $this->queryBuilder
            ->select(['ip, date'])
            ->group('ip', 'date');

        return count($this->model->fetchAll($this->queryBuilder));
    }
}
