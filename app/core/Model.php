<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Utils\QueryBuilder;

abstract class Model
{
    private $db;

    private $table_name;
    private $columns;
    private $model_class_name;

    protected function __construct()
    {
        $this->db = Database::getInstance();
        $this->model_class_name = get_called_class();
        $this->table_name = $this->getModelTableName($this->model_class_name);
        $this->columns = array_keys(array_diff_key(
            get_class_vars($this->model_class_name),
            get_class_vars(get_class())
        ));
    }

    /* Main actions */

    public function get(): array
    {
        $by_id = $this->hasId() ? ['id' => $this->getId()] : [];
        return $this->getBy($by_id, Database::FETCH_ONE);
    }

    public function getBy(array $conditions, int $type_fetch = Database::FETCH_ALL): array
    {
        $qb = (new QueryBuilder())->from($this->table_name);
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $qb->where($value);
            } else {
                $qb->where(QueryBuilder::eq($key, $value));
            }
        }

        if ($type_fetch === Database::FETCH_ONE)
            return $this->fetchOne($qb);

        return $this->fetchAll($qb);
    }

    public function save(): array
    {
        $data = $this->getModelData();

        # Insert query
        if (!$this->hasId()) {
            $sql = 'INSERT INTO ' . $this->table_name
                . ' (' . implode(',', $this->columns) . ') 
                    VALUES (:' . implode(', :', $this->columns) . ')';
        }
        # Update query
        else {
            $data['id'] = $this->getId();
            $update = [];
            foreach ($data as $column => $value) {
                $update[] = $column . " = :" . $column;
            }
            $sql = 'UPDATE ' . $this->table_name
                . ' SET '
                . implode(', ', $update)
                . ' WHERE id = :id';
        }

        $res = $this->execute($sql, $data);
        if (!$res)
            return false;

        return [
            'affected_rows' => $res->rowCount(),
            'inserted_id' => !$this->hasId() ? $this->db->lastInsertId() : null
        ];
    }

    public function populate(array $data)
    {
        foreach ($data as $column => $value) {
            if (method_exists($this->model_class_name, $this->toSetter($column))) {
                $this->{$this->toSetter($column)}($value);
            } else {
                throw new \Exception('La méthode : ' . $this->toSetter($column) . ' de la classe ' . get_called_class() . ' n\' existe pas');
            }
        }
    }

    // protected function insert() : array
    // {
    //     return [];
    // }

    // protected function update() : array
    // {
    //     return [];
    // }

    // public function delete(int $id) : bool
    // {
    //     return true;
    // }

    /* Utilities */

    protected function fetchAll(QueryBuilder $qb, bool $debug = false): array
    {
        if ($debug) var_dump($qb->debug());

        return $this->fetch($qb->buildQuery(), $qb->getParams());
    }

    protected function fetchOne(QueryBuilder $qb, bool $debug = false): array
    {
        if ($debug) var_dump($qb->debug());

        return $this->fetch($qb->buildQuery(), $qb->getParams(), Database::FETCH_ONE);
    }

    private function fetch(string $sql, array $params, int $type = Database::FETCH_ALL): array
    {
        $st = $this->execute($sql, $params);
        $results = false;

        switch ($type) {
            case Database::FETCH_ONE:
                $results = $st->fetch();
                break;
            case Database::FETCH_ALL:
                $results = $st->fetchAll();
                break;
        }

        return $results;
    }

    private function execute(string $sql, array $params): \PDOStatement
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);

        return $st;
    }

    private function getModelTableName(string $class): string
    {
        $class_name = explode("\\", $class);
        $class_name = $class_name[count($class_name) - 1];

        return DB_PREFIX . '_' . mb_strtolower($class_name);
    }

    private function hasId()
    {
        return !is_null($this->getId());
    }

    private function getModelData(): array
    {
        $res = [];
        foreach ($this->columns as $column) {
            $res[$column] = $this->{$this->toGetter($column)}();
        }

        return $res;
    }

    private function toGetter(string $column): string
    {
        return !empty($column) ? 'get' . ucfirst($column) : '';
    }

    private function toSetter(string $column): string
    {
        return !empty($column) ? 'set' . ucfirst($column) : '';
    }
}
