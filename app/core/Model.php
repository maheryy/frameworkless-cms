<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\QueryBuilder;
use App\Core\Utils\Expr;

abstract class Model
{
    private \PDO $db;

    private string $table_name;
    private string $model_class_name;
    private array $columns;

    const STATUS_DELETED = -1;
    const STATUS_DEFAULT = 1;

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
     * Fetch all rows in the model table.
     * Archived rows (status = -1) are not fetched
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->hasStatus()
            ? $this->getBy([Expr::neq('status', self::STATUS_DELETED)])
            : $this->getBy();
    }


    /**
     * Fetch all fields by specific $conditions.
     * - ex: ['id' => 2, 'name' => 'bob] : WHERE id = 2 AND name = 'bob'
     * 
     * @param array $conditions 
     * @param int $type_fetch FETCH_ALL|FETCH_ONE
     * 
     * @return array
     */
    public function getBy(array $conditions = [], int $type_fetch = Database::FETCH_ALL)
    {
        $qb = (new QueryBuilder())->from($this->table_name);
        foreach ($conditions as $key => $value) {
            if (Expr::isExpr($value)) {
                $qb->where($value);
            } else {
                $qb->where(Expr::eq($key, $value));
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
     * @param int code Database::SAVE_DEFAULT | Database::SAVE_IGNORE_NULL
     * @return array affected rows and last inserted id
     */
    public function save(int $code = Database::SAVE_DEFAULT)
    {
        $data = $this->getModelData($code);

        # Insert query
        if (!$this->hasId()) {
            $insert = $this->insertQuery($data);
            $res = $insert ? ['inserted_id' => $insert] : false;
        }
        # Update query
        else {
            $update = $this->updateQuery($data, [Expr::eq('id', $this->getId())]);
            $res = $update ? ['affected_rows' => $update] : false;
        }
        return $res;
    }

    /**
     * Get model data (property => value)
     * 
     * @return array
     */
    public function getData()
    {
        $data = $this->getModelData();
        $data['id'] = $this->getId();

        return $data;
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
                throw new NotFoundException('La méthode : ' . $this->toSetter($column) . ' de la classe ' . get_called_class() . ' n\' existe pas');
            }
        }
    }


    /**
     * Delete the row from the table
     * 
     * @return void
     */
    public function deleteForever()
    {
        $this->deleteQuery([Expr::eq('id', $this->getId())]);
    }

    /**
     * Set status to -1 if "status" column exist, delete row otherwise
     * 
     * @return void
     */
    public function delete()
    {
        if ($this->hasStatus()) {
            $this->updateQuery(['status' => self::STATUS_DELETED], [Expr::eq('id', $this->getId())]);
        } else {
            $this->deleteForever();
        }
    }

    /**
     * Update one or multiple rows
     * 
     * @param array $fields
     * @param array $conditions
     * 
     * @return int|bool affected rows, false otherwise
     */
    public function update(array $fields, array $conditions = [])
    {
        return $this->updateQuery($fields, $conditions);
    }


    /* --------- Utilities ---------- */

    /**
     * Get all the result from a prepared query
     * 
     * @param QueryBuilder $qb
     * @param bool $debug if true, the query (statement, params) is dumped before executing the query
     * 
     * @return array 2D array
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
     * @return array 1D array
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
     * Insert query
     * 
     * @param array $data
     * 
     * @return int|bool last inserted id, false otherwise
     */
    private function insertQuery(array $data)
    {
        $fields = array_keys($data);
        $sql = 'INSERT INTO ' . $this->table_name
                . '(' . implode(',', $fields) . ') '
                . 'VALUES (:' . implode(', :', $fields) . ')';

        $res = $this->execute($sql, $data);
        return $res ? $this->db->lastInsertId() : $res;
    }

    /**
     * Update query with multiple conditions
     * 
     * @param array $fields
     * @param array $conditions
     * 
     * @return int|bool affected rows, false otherwise
     */
    private function updateQuery(array $fields, array $conditions = [])
    {
        $update_fields =  $clauses = [];
        $params = $fields;

        foreach ($fields as $column => $value) {
            $update_fields[] = $column . " = :" . $column;
        }

        foreach ($conditions as $condition) {
            $clauses[] = $condition['expr'];
            $params = array_merge($params, $condition['bind']);
        }

        $sql = 'UPDATE ' . $this->table_name
            . ' SET '
            . implode(', ', $update_fields);

        if (!empty($clauses)) {
            $sql .=  ' WHERE '
                . implode(' AND ', $clauses);
        }

        $res = $this->execute($sql, $params);
        return $res ? $res->rowCount() : $res;
    }

    /**
     * Delete one or more rows from the model table
     * 
     * @param array $conditions
     * 
     * @return int|bool affected rows, false otherwise
     */
    private function deleteQuery(array $conditions = [])
    {
        $clauses = [];
        $params = [];

        foreach ($conditions as $condition) {
            $clauses[] = $condition['expr'];
            $params = array_merge($params, $condition['bind']);
        }

        $sql = 'DELETE FROM ' . $this->table_name;

        if (!empty($clauses)) {
            $sql .=  ' WHERE '
                . implode(' AND ', $clauses);
        }

        $res = $this->execute($sql, $params);
        return $res ? $res->rowCount() : $res;
    }

    /**
     * Fill a model object with a given ID
     * Every model must call this function in getId method
     * 
     * @return void
     */
    protected function hydrate()
    {
        $data = $this->getBy(['id' => $this->getId()], Database::FETCH_ONE);
        if (empty($data)) {
            throw new NotFoundException("l'id " . $this->getId() . " n'existe pas");
        }

        unset($data['id']);
        $this->populate($data);
    }

    /**
     * Execute a prepared statement
     * 
     * @param string $sql
     * @param array $params
     * 
     * @return \PDOStatement
     */
    private function execute(string $sql, array $params = null)
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
     * Check if a model has status column
     * 
     * @return bool
     */
    private function hasStatus()
    {
        return in_array('status', $this->columns);
    }

    /**
     * Return all the model properties => values
     * 
     * @return array
     */
    private function getModelData(int $code = Database::SAVE_DEFAULT)
    {
        $res = [];
        foreach ($this->columns as $column) {
            $res[$column] = $this->{$this->toGetter($column)}();
            if ($code === Database::SAVE_IGNORE_NULL && is_null($res[$column])) {
                unset($res[$column]);
            }
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
