<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Models\MenuItem;

class MenuItemRepository extends BaseRepository
{
    public function __construct(MenuItem $model)
    {
        parent::__construct($model);
    }



    public function findMenuItems(int $menu_id)
    {
        $menu_table = Formatter::getTableName('menu');
        $page_table = Formatter::getTableName('post');
        $page_detail_table = Formatter::getTableName('page_extra');

        $this->queryBuilder
            ->select([
                "$this->table.label, $this->table.icon, $this->table.url",
                "$page_table" => ['page_id' => 'id', 'page_title' => 'title'],
                'page_link' => "$page_detail_table.slug",
                "$menu_table" => ['menu_title' => 'title', 'menu_type' => 'type', 'menu_status' => 'status'],
            ])
            ->joinInner($menu_table, "$this->table.menu_id = $menu_table.id")
            ->joinLeft($page_table, "$this->table.post_id = $page_table.id", Expr::eq("$page_table.status", Constants::STATUS_PUBLISHED))
            ->joinLeft($page_detail_table, "$page_table.id = $page_detail_table.post_id")
            ->where(Expr::eq("$this->table.menu_id", $menu_id));


        return $this->model->fetchAll($this->queryBuilder);
    }


    public function deleteAllByMenu(int $menu_id)
    {
        return $this->model->deleteQuery([Expr::eq('menu_id', $menu_id)]);
    }
}
