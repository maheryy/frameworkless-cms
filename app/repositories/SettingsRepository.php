<?php

namespace App\Repositories;

use App\Core\BaseRepository;
use App\Core\Utils\Expr;
use App\Core\Utils\Formatter;
use App\Models\Settings;

class SettingsRepository extends BaseRepository
{
    public function __construct(Settings $model)
    {
        parent::__construct($model);
    }

    public function findAll()
    {
        return Formatter::tableKeyValueToArray($this->model->fetchAll($this->queryBuilder));
    }

    public function findByName(string $name)
    {
        $this->queryBuilder->where(Expr::like('name', $name));
        return $this->model->fetchOne($this->queryBuilder);
    }

    public function updateSettings(array $data)
    {
        return $this->model->updateQuery($data);
    }
}
