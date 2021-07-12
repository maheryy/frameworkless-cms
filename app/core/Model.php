<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Exceptions\NotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\QueryBuilder;
use App\Core\Utils\Expr;

abstract class Model
{
    private \PDO $db;

    private string $table_name;
    private string $model_class_name;
    private array $columns;

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

    public function getTableName()
    {
        return $this->table_name;
    }

    public function getModelName()
    {
        $model_class = explode("\\", $this->model_class_name);
        return end($model_class);
    }

    public function getColumns()
    {
        return $this->columns;
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
        } # Update query
        else {
            $update = $this->updateQuery($data, [Expr::eq('id', $this->getId())]);
            $res = $update ? ['affected_rows' => $update] : false;
        }
        return $res;
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
            $setter = Formatter::propertyToSetter($column);
            if (method_exists($this->model_class_name, $setter)) {
                $this->{$setter}($value);
            } else {
                throw new NotFoundException('La méthode : ' . $setter . ' de la classe ' . get_called_class() . ' n\' existe pas');
            }
        }
    }


    /**
     * Get all the result from a prepared query
     *
     * @param QueryBuilder $qb
     * @param bool $debug if true, the query (statement, params) is dumped before executing the query
     *
     * @return array 2D array
     */
    public function fetchAll(QueryBuilder $qb, bool $debug = false)
    {
        if ($debug) {
            echo '<pre>';
            print_r($qb->debug());
            echo '</pre>';
        }

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
    public function fetchOne(QueryBuilder $qb, bool $debug = false)
    {
        if ($debug) {
            echo '<pre>';
            print_r($qb->debug());
            echo '</pre>';
        }

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
    public function insertQuery(array $data)
    {
        if(empty($data)) return false;

        # Check if there is multiple insertion
        if (is_int(array_key_first($data))) {
            $fields = array_keys(array_values($data)[0]);
            $sql_params = [];
            $sql = 'INSERT INTO ' . $this->table_name
                . '(' . implode(',', $fields) . ') VALUES';

            foreach ($data as $key => $value) {
                $fields = array_map(fn($v) => $v . '_' . $key, array_keys($value));
                $sql .= "\n(:" . implode(', :', $fields) . "),";
                $sql_params = array_merge($sql_params, array_combine($fields, array_values($value)));
            }
            $sql = rtrim($sql, ',');
        } else {
            $sql_params = $data;
            $fields = array_keys($data);
            $sql = 'INSERT INTO ' . $this->table_name
                . '(' . implode(',', $fields) . ') '
                . 'VALUES (:' . implode(', :', $fields) . ')';
        }

        $res = $this->execute($sql, $sql_params);
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
    public function updateQuery(array $fields, array $conditions = [])
    {
        $update_fields = $clauses = [];
        $params = $fields;

        if(empty($fields)) return false;

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
            $sql .= ' WHERE '
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
    public function deleteQuery(array $conditions = [])
    {
        $clauses = [];
        $params = [];

        foreach ($conditions as $condition) {
            $clauses[] = $condition['expr'];
            $params = array_merge($params, $condition['bind']);
        }

        $sql = 'DELETE FROM ' . $this->table_name;

        if (!empty($clauses)) {
            $sql .= ' WHERE '
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
        $qb = (new QueryBuilder())->from($this->table_name)->where(Expr::eq('id', $this->getId()));
        $data = $this->fetchOne($qb);
        if (empty($data)) {
            throw new NotFoundException('l\'id ' . $this->getId() . ' n\'existe pas');
        }

        unset($data['id']);
        $this->populate($data);
    }

    /**
     * Truncate table and reset autoincrement
     */
    public function truncate()
    {
        $this->deleteQuery();
        $this->resetAutoIncrement();
    }

    /**
     * Reset autoincrement value
     */
    public function resetAutoIncrement()
    {
        $this->execute("ALTER TABLE {$this->table_name} AUTO_INCREMENT = 1");
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
        $class_exploded = explode("\\", $class);
        return Formatter::getTableName(Formatter::camelToSnakeCase($class_exploded[array_key_last($class_exploded)]));
    }

    /**
     * Check if a model has an id
     *
     * @return bool
     */
    public function hasId()
    {
        return !is_null($this->getId());
    }

    /**
     * Check if a model has status column
     *
     * @return bool
     */
    public function hasStatus()
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
            $res[$column] = $this->{Formatter::propertyToGetter($column)}();
            if ($code === Database::SAVE_IGNORE_NULL && is_null($res[$column])) {
                unset($res[$column]);
            }
        }

        return $res;
    }
}
