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

    /* --------- Main actions ---------- */

    /**
     * Fetch all fields by specific $conditions.
     * Only equivalences are supported.
     * - ex: ['id' => 2, 'name' => 'bob] : WHERE id = 2 AND name = 'bob'
     * 
     * @param array $conditions 
     * @param int $type_fetch FETCH_ALL|FETCH_ONE
     * 
     * @return array
     */
    public function getBy(array $conditions, int $type_fetch = Database::FETCH_ALL)
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

    /**
     * Insert or update query.
     * Update query is triggered if a model has an id. Insert query otherwise
     * 
     * @return array affected rows and last inserted id
     */
    public function save()
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

    /**
     * Set all the properties of a given model (usually from the database)
     * 
     * @param array $data
     * 
     * @return void
     */
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

    /* --------- Utilities ---------- */

    /**
     * Get all the result from a prepared query
     * 
     * @param QueryBuilder $qb
     * @param bool $debug if true, the query (statement, params) is dumped before executing the query
     * 
     * @return array 1D array
     */
    protected function fetchAll(QueryBuilder $qb, bool $debug = false)
    {
        if ($debug) var_dump($qb->debug());

        return $this->fetch($qb->buildQuery(), $qb->getParams());
    }

    /**
     * Get the first result from a prepared query
     * 
     * @param QueryBuilder $qb
     * @param bool $debug if true, the query (statement, params) is dumped before executing the query
     * 
     * @return array 2D array
     */
    protected function fetchOne(QueryBuilder $qb, bool $debug = false)
    {
        if ($debug) var_dump($qb->debug());

        return $this->fetch($qb->buildQuery(), $qb->getParams(), Database::FETCH_ONE);
    }

    /**
     * Fetch a prepared statement
     * 
     * @param string $sql
     * @param array $params
     * @param int $type FETCH_ALL|FETCH_ONE
     * 
     * @return array
     */
    private function fetch(string $sql, array $params, int $type = Database::FETCH_ALL)
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

    /**
     * Execute a prepared statement
     * 
     * @param string $sql
     * @param array $params
     * 
     * @return PDOStatement
     */
    private function execute(string $sql, array $params)
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);

        return $st;
    }

    /**
     * @param string $class
     * 
     * @return string
     */
    private function getModelTableName(string $class)
    {
        $class_name = explode("\\", $class);
        $class_name = $class_name[count($class_name) - 1];

        return DB_PREFIX . '_' . mb_strtolower($class_name);
    }

    /**
     * Check if a model has an id
     * 
     * @return bool
     */
    private function hasId()
    {
        return !is_null($this->getId());
    }

    /**
     * Return all the model properties => values
     * 
     * @return array
     */
    private function getModelData()
    {
        $res = [];
        foreach ($this->columns as $column) {
            $res[$column] = $this->{$this->toGetter($column)}();
        }

        return $res;
    }

    /**
     * Convert a table column name to a model's getter
     * 
     * @param string $column
     * 
     * @return string
     */
    private function toGetter(string $column)
    {
        return !empty($column) ? 'get' . ucfirst($column) : '';
    }

    /**
     * Convert a table column name to a model's setter
     * 
     * @param string $column
     * 
     * @return string
     */
    private function toSetter(string $column)
    {
        return !empty($column) ? 'set' . ucfirst($column) : '';
    }
}
