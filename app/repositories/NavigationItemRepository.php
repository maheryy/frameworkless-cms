<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Models\NavigationItem;

class NavigationItemRepository extends BaseRepository
{
    public function __construct(NavigationItem $model)
    {
        parent::__construct($model);
    }

    private function findItems(array $options = [])
    {
        $nav_table = Formatter::getTableName('navigation');
        $page_table = Formatter::getTableName('post');
        $page_detail_table = Formatter::getTableName('page_extra');

        $this->queryBuilder
            ->select([
                "$this->table.label",
                "$page_table" => ['page_id' => 'id', 'page_title' => 'title'],
                'page_link' => "$page_detail_table.slug",
                "$nav_table" => ['nav_title' => 'title', 'nav_type' => 'type', 'nav_active' => 'status'],
            ])
            ->joinInner($nav_table, "$this->table.navigation_id = $nav_table.id")
            ->joinInner($page_table, "$this->table.post_id = $page_table.id")
            ->joinInner($page_detail_table, "$page_table.id = $page_detail_table.post_id")
            ->where(Expr::eq("$page_table.status", Constants::STATUS_PUBLISHED));

        if (!empty($options['nav_id'])) {
            $this->queryBuilder->where(Expr::eq("$this->table.navigation_id", $options['nav_id']));
        }
        if (!empty($options['nav_active'])) {
            $this->queryBuilder->where(Expr::eq("$nav_table.status", Constants::STATUS_ACTIVE));
        }

        return $this->model->fetchAll($this->queryBuilder);
    }

    public function findNavigationItems(int $navigation_id)
    {
        return $this->findItems(['nav_id' => $navigation_id]);
    }

    public function findActiveNavigationItems()
    {
        return $this->findItems(['nav_active' => true]);
    }

    public function deleteAllByNavigation(int $navigation_id)
    {
        return $this->model->deleteQuery([Expr::eq('navigation_id', $navigation_id)]);
    }
}
