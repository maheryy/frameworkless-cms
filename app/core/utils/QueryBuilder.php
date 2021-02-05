<?php

namespace App\Core\Utils;

class QueryBuilder {
    const JOIN_FULL = 'FULL JOIN';
    const JOIN_INNER = 'INNER JOIN';
    const JOIN_LEFT = 'LEFT JOIN';
    const JOIN_RIGHT = 'RIGHT JOIN';
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    private $fields = [];
    private $from;
    private $joins = [];
    private $wheres = [];
    private $group;
    private $orders = [];
    private $params = [];
    private $limit;
    
    public function __construct(){}

    public function __toString() : string
    {
        return $this->buildQuery();
    }
    
    public function getParams() : array
    {
        return $this->params;
    }

    public function buildQuery() : string 
    {
        $select = "SELECT\n". (!empty($this->fields) ? implode(', ', $this->fields) : '*');
        $from = "\nFROM\n". $this->from;
        $join = !empty($this->joins) ? "\n". implode("\n", $this->joins): '';
        $where = !empty($this->wheres) ? "\nWHERE\n". implode("\nAND ", $this->wheres) : '';
        $group = !empty($this->group) ? "\nGROUP BY ". $this->group : '';
        $order = !empty($this->orders) ? "\nORDER BY ". implode(", ", $this->orders) : '';
        $limit = !empty($this->limit) ? "\nLIMIT ". $this->limit : '';

        return "$select $from $join $where $group $order $limit\n";
    }

    public function buildQueryWithParams() : string
    {
        $query = $this->buildQuery();
        foreach ($this->params as $key => $value) {
            $query = preg_replace("/(:$key)\b/", $value, $query);
        }

        return $query;
    }

    public function cleanUnusedParams() : self
    {
        $query = $this->buildQuery();
        $unused_params = [];
        foreach($this->params as $key => $value) {
            if( !preg_match("/(:$key)\b/", $query) ) {
                $unused_params[] = $key;
            }
        }
        foreach($unused_params as $param) {
            unset($this->params[$param]);
        }

        return $this;
    }
    
    public function select(array $fields) : self
    {
        foreach($fields as $key => $field) {
            if( is_array($field) ) {
                foreach ($field as $alias => $t_field) {
                    if( !empty($t_field) ) {
                        $this->fields[] = is_string($alias) ? "$key.$t_field as $alias" : "$key.$t_field";
                    }
                }
            } else {
                if( !empty($field) ) {
                    $this->fields[] = is_string($key) ? "$field as $key" : $field;
                }
            }
        }

        return $this;
    }

    public function from(string $table) : self
    {
        $this->from = $table;
        return $this;
    }

    public function join(string $type, string $table, string ...$conditions) : self
    {
        if( !empty($conditions) ) {
            $group_conditions = '';
            foreach ($conditions as $key => $condition) {
                if( is_array($condition) ) {
                    $group_conditions .= array_key_exists($key+1, $conditions) 
                    ? $condition['expr'] .' AND '
                    : $condition['expr'];
                    $this->params = array_merge($this->params, $condition['bind']);
                } else {
                    $group_conditions .= array_key_exists($key+1, $conditions) 
                    ? $condition .' AND '
                    : $condition;
                }
            }            
            $this->joins[] = "$type $table ON $group_conditions";
        } else {
            $this->joins[] = "$type $table";
        }

        return $this;
    }

    public function where(array ...$conditions) : self
    {
        $group_conditions = '';
        foreach ($conditions as $key => $condition) {
            $group_conditions .= array_key_exists($key+1, $conditions) 
                                ? $condition['expr'] .' OR '
                                : $condition['expr'];
            $this->params = array_merge($this->params, $condition['bind']);
        }

        $this->wheres[] = '(' .$group_conditions . ')';
        return $this;
    }

    public function order(string $direction, string ...$fields) : self
    {
        $this->orders[] = !empty($fields) ? implode(', ', $fields) .' '. $direction : null;
        return $this;
    }

    public function group(string ...$fields) : self
    {
        $this->group = !empty($fields) ? implode(', ', $fields) : null;
        return $this;
    }

    public function limit(int $limit, int $from = 0) : self
    {
        $this->limit = !empty($from) ? "$from, $limit" : $limit;
        return $this;
    }

    public function joinLeft(string $table, string ...$conditions) : self
    {
        return $this->join(self::JOIN_LEFT, $table, ...$conditions);
    }
    public function joinRight(string $table, string ...$conditions) : self
    {
        return $this->join(self::JOIN_RIGHT, $table, ...$conditions);
    }
    public function joinInner(string $table, string ...$conditions) : self
    {
        return $this->join(self::JOIN_INNER, $table, ...$conditions);
    }
    public function orderAsc(string ...$fields) : self
    {
        return $this->order(self::ORDER_ASC, ...$fields);
    }
    public function orderDesc(string ...$fields) : self
    {
        return $this->order(self::ORDER_DESC,...$fields);
    }
    
    public function debug() : array
    {
        return [
            'sql' => $this->buildQuery(),
            'params' => $this->getParams(), 
            'sql_filled'=> $this->buildQueryWithParams(),
        ];
    }


    # WHERE id = 5
    public static function eq(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier = :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id <> 5
    public static function neq(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier <> :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id < 5
    public static function lt(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier < :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id <= 5
    public static function lte(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier <= :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id > 5
    public static function gt(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier > :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id >= 5
    public static function gte(string $identifier, $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier >= :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id >= 1 AND id <= 5
    public static function between(string $identifier, $value1, $value2) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "($identifier >= :{$placeholder}_1 AND $identifier <= :{$placeholder}_2)",
            'bind' => ["{$placeholder}_1" => $value1, "{$placeholder}_2" => $value2]
        ];
    }
    # WHERE name LIKE 'bob'
    public static function like(string $identifier, string $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier LIKE :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE name NOT LIKE 'bob'
    public static function notLike(string $identifier, string $value) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier NOT LIKE :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }
    # WHERE id IN (1,2,3,4)
    public static function in(string $identifier, array $values) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        $params = [];
        foreach($values as $index => $value) {
            $params[$placeholder .'_'. $index] = $value;
        }
        return [
            'expr' => $identifier .' IN (:'. implode(', :', array_keys($params)) .')',
            'bind' => $params
        ];
    }
    # WHERE id NOT IN (1,2,3,4)
    public static function notIn(string $identifier, array $values) : array
    {
        $placeholder = self::makePlaceholder($identifier);
        $params = [];
        foreach($values as $index => $value) {
            $params[$placeholder .'_'. $index] = $value;
        }
        return [
            'expr' => $identifier .' NOT IN (:'. implode(', :', array_keys($params)) .')',
            'bind' => $params
        ];
    }
    # WHERE content IS NULL
    public static function isNull(string $identifier) : array
    {
        return [
            'expr' => "$identifier IS NULL",
            'bind' => []
        ];
    }
    # WHERE content IS NOT NULL
    public static function isNotNull(string $identifier) : array
    {
        return [
            'expr' => "$identifier IS NOT NULL",
            'bind' => []
        ];
    }
    
    public static function makePlaceholder(string $identifier) : string
    {
        return str_replace('.', '_', $identifier);
    }

   
}