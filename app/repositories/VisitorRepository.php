<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
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

    public function findUniqueVisitorsPerDay(string $date_from)
    {
        $this->queryBuilder
            ->select(['COUNT(*) AS count, date'])
            ->from("(SELECT DISTINCT ip, date FROM $this->table) AS $this->table")
            ->where(Expr::gte('date', $date_from))
            ->group('date')
            ->orderAsc('date');

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findVisitorsPerPages(int $limit)
    {
        $page_details_table = Formatter::getTableName('page_extra');
        $page_table = Formatter::getTableName('post');

        $this->queryBuilder
            ->select([
                "COUNT(*) AS count, $this->table.uri",
                "$page_table.title AS page_title, $page_table.id AS page_id"
            ])
            ->joinInner($page_details_table, "$this->table.uri = $page_details_table.slug")
            ->joinInner($page_table, "$page_details_table.post_id = $page_table.id", Expr::eq("$page_table.status", Constants::STATUS_PUBLISHED))
            ->group("$this->table.uri, $page_table.title, $page_table.id")
            ->orderDesc("count")
            ->limit($limit);

        return $this->model->fetchAll($this->queryBuilder);
    }
}
