<?php

namespace App\Core\Utils;

class QueryBuilder
{
    const JOIN_FULL = 'FULL JOIN';
    const JOIN_INNER = 'INNER JOIN';
    const JOIN_LEFT = 'LEFT JOIN';
    const JOIN_RIGHT = 'RIGHT JOIN';
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private string $from;
    private string $group;
    private string $limit;
    private array $fields = [];
    private array $joins = [];
    private array $wheres = [];
    private array $orders = [];
    private array $params = [];


    public function __toString()
    {
        return $this->buildQuery();
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return the final statement with placeholders
     * 
     * @return string
     */
    public function buildQuery()
    {
        $select = "SELECT\n" . (!empty($this->fields) ? implode(', ', $this->fields) : '*');
        $from = "\nFROM\n" . $this->from;
        $join = !empty($this->joins) ? "\n" . implode("\n", $this->joins) : '';
        $where = !empty($this->wheres) ? "\nWHERE\n" . implode("\nAND ", $this->wheres) : '';
        $group = !empty($this->group) ? "\nGROUP BY " . $this->group : '';
        $order = !empty($this->orders) ? "\nORDER BY " . implode(", ", $this->orders) : '';
        $limit = !empty($this->limit) ? "\nLIMIT " . $this->limit : '';

        return "$select $from $join $where $group $order $limit\n";
    }

    /**
     * Return the final statement with values
     * 
     * @return string
     */
    public function buildQueryWithParams()
    {
        $query = $this->buildQuery();
        foreach ($this->params as $key => $value) {
            $query = preg_replace("/(:$key)\b/", $value, $query);
        }

        return $query;
    }

    /**
     * Remove all params that doesn't exist in the final statement
     * 
     * @return QueryBuilder
     */
    public function cleanUnusedParams()
    {
        $query = $this->buildQuery();
        $unused_params = [];
        foreach ($this->params as $key => $value) {
            if (!preg_match("/(:$key)\b/", $query)) {
                $unused_params[] = $key;
            }
        }
        foreach ($unused_params as $param) {
            unset($this->params[$param]);
        }

        return $this;
    }

    /**
     * Specify all fields. 
     * Same field name with different table name are supported
     * 
     * ex : 
     * - ['id', 'name'] : SELECT id, name FROM ..
     * - [ 'table1' => ['id', 'name'], 'table2' => ['id_table_2' => 'id', 'name_table_2' => 'name'] ] 
     * -> SELECT table1.id, table1.name, table2.id AS id_table_2, table2.name AS name_table_2 FROM ...
     * 
     * @param array $fields
     * 
     * @return QueryBuilder
     */
    public function select(array $fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                foreach ($field as $alias => $t_field) {
                    if (!empty($t_field)) {
                        $this->fields[] = is_string($alias) ? "$key.$t_field as $alias" : "$key.$t_field";
                    }
                }
            } else {
                if (!empty($field)) {
                    $this->fields[] = is_string($key) ? "$field as $key" : $field;
                }
            }
        }

        return $this;
    }

    /**
     * Specify the main table in the statement
     * 
     * @param string $table
     * 
     * @return QueryBuilder
     */
    public function from(string $table)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Specify join clauses
     * 
     * @param string $type JOIN_FULL|JOIN_INNER|JOIN_LEFT|JOIN_RIGHT
     * @param string $table
     * @param string|array ...$conditions ex : Expr::eq('id', 5) | "table1.id = table2.id"
     * 
     * @return QueryBuilder
     */
    private function join(string $type, string $table, ...$conditions)
    {
        if (!empty($conditions)) {
            $group_conditions = '';
            foreach ($conditions as $key => $condition) {
                if (Expr::isExpr($condition)) {
                    $group_conditions .= array_key_exists($key + 1, $conditions)
                        ? $condition['expr'] . ' AND '
                        : $condition['expr'];
                    $this->params = array_merge($this->params, $condition['bind']);
                } else {
                    $group_conditions .= array_key_exists($key + 1, $conditions)
                        ? $condition . ' AND '
                        : $condition;
                }
            }
            $this->joins[] = "$type $table ON $group_conditions";
        } else {
            $this->joins[] = "$type $table";
        }

        return $this;
    }

    /**
     * Specify all the clauses
     * 
     * ex: 
     * - Expr::eq('id', 4) : WHERE id = :id
     * - [Expr::eq('id', 4), Expr::like('name', 'test')] : WHERE id = :id OR name LIKE :name
     * 
     * @param array ...$conditions
     * 
     * @return QueryBuilder
     */
    public function where(array ...$conditions)
    {
        $group_conditions = '';
        foreach ($conditions as $key => $condition) {
            $group_conditions .= array_key_exists($key + 1, $conditions)
                ? $condition['expr'] . ' OR '
                : $condition['expr'];
            $this->params = array_merge($this->params, $condition['bind']);
        }

        $this->wheres[] = '(' . $group_conditions . ')';
        return $this;
    }

    /**
     * Specify all the fields that should be ordered
     * 
     * 
     * @param string $direction ORDER_ASC|ORDER_DESC
     * @param string ...$fields
     * 
     * @return QueryBuilder
     */
    private function order(string $direction, string ...$fields)
    {
        $this->orders[] = !empty($fields) ? implode(', ', $fields) . ' ' . $direction : null;
        return $this;
    }

    /**
     * Specify all the fields that should be grouped
     * 
     * @param string ...$fields
     * 
     * @return QueryBuilder
     */
    public function group(string ...$fields)
    {
        $this->group = !empty($fields) ? implode(', ', $fields) : null;
        return $this;
    }

    /**
     * Specify the limit
     * 
     * @param int $limit
     * @param int $from optional
     * 
     * @return QueryBuilder
     */
    public function limit(int $limit, int $from = 0)
    {
        $this->limit = !empty($from) ? "$from, $limit" : $limit;
        return $this;
    }

    public function joinLeft(string $table, ...$conditions)
    {
        return $this->join(self::JOIN_LEFT, $table, ...$conditions);
    }
    public function joinRight(string $table, ...$conditions)
    {
        return $this->join(self::JOIN_RIGHT, $table, ...$conditions);
    }
    public function joinInner(string $table, ...$conditions)
    {
        return $this->join(self::JOIN_INNER, $table, ...$conditions);
    }
    public function orderAsc(string ...$fields)
    {
        return $this->order(self::ORDER_ASC, ...$fields);
    }
    public function orderDesc(string ...$fields)
    {
        return $this->order(self::ORDER_DESC, ...$fields);
    }

    /**
     * Return :
     * - the final statement with placeholders
     * - the final statement with values binded
     * - bind parameters associated with value
     * 
     * @return array
     */
    public function debug()
    {
        return [
            'sql' => $this->buildQuery(),
            'params' => $this->getParams(),
            'sql_filled' => $this->buildQueryWithParams(),
        ];
    }
}
