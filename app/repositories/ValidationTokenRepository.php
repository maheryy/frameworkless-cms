<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Core\Utils\QueryBuilder;
use App\Models\ValidationToken;

class ValidationTokenRepository extends BaseRepository
{
    public function __construct(ValidationToken $model)
    {
        parent::__construct($model);
    }

    public function findByReference(string $reference)
    {
        $qb = (new QueryBuilder())
            ->from($this->table)
            ->where(Expr::like('reference', $reference));

        return $this->model->fetchOne($qb);
    }

    public function removeTokenByUser(int $user_id, int $type_token)
    {
        return $this->model->deleteQuery([
            Expr::eq('type', $type_token),
            Expr::eq('user_id', $user_id)
        ]);
    }

}
