<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Constants;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Core\Utils\QueryBuilder;
use App\Models\Post;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function findPage(int $id)
    {
        $details_table = Formatter::getTableName('page_extra');

        $qb = (new QueryBuilder())
            ->select([
                "$details_table" => ['*'],
                "$this->table" => ['*'],
            ])
            ->from($this->table)
            ->joinInner($details_table, "$this->table.id = $details_table.post_id")
            ->where(Expr::eq("$this->table.id", $id))
            ->where(Expr::neq("$this->table.status", Constants::STATUS_DELETED));

        return $this->model->fetchOne($qb);

    }

    public function findPageByTitle(string $title)
    {
        $qb = (new QueryBuilder())
            ->from($this->table)
            ->where(Expr::eq('type', Constants::POST_TYPE_PAGE))
            ->where(Expr::like('title', $title));

        return $this->model->fetchOne($qb);
    }

    public function findPagesByAuthor(string $user_id)
    {
        $qb = (new QueryBuilder())
            ->from($this->table)
            ->where(Expr::eq('type', Constants::POST_TYPE_PAGE))
            ->where(Expr::eq('author_id', $user_id))
            ->where(Expr::neq('status', Constants::STATUS_DELETED));

        return $this->model->fetchAll($qb);
    }

    public function findAllPages()
    {
        $user_table = Formatter::getTableName('user');
        $details_table = Formatter::getTableName('page_extra');
        $qb = (new QueryBuilder())
            ->select([
                "$user_table" => ['author' => 'username'],
                "$details_table.*",
                "$this->table.*",
            ])
            ->from($this->table)
            ->joinInner($user_table, "$this->table.author_id = $user_table.id")
            ->joinInner($details_table, "$this->table.id = $details_table.post_id")
            ->where(Expr::eq("$this->table.type", Constants::POST_TYPE_PAGE))
            ->where(Expr::neq("$this->table.status", Constants::STATUS_DELETED));

        return $this->model->fetchAll($qb);
    }

    public function findPageBySlug(string $slug)
    {
        $details_table = Formatter::getTableName('page_extra');
        $qb = (new QueryBuilder())
            ->from($this->table)
            ->joinInner($details_table, "$this->table.id = $details_table.post_id")
            ->where(Expr::like("$details_table.slug", $slug))
            ->where(Expr::eq("$this->table.status", Constants::STATUS_PUBLISHED));

        return $this->model->fetchOne($qb);
    }

    public function updateStatus(int $id, int $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}
