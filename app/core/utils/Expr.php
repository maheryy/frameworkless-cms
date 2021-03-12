<?php

namespace App\Core\Utils;

/**
 * Static class with all possible expressions in sql queries.
 * 
 * Every function returns an expression with custom placeholder associated with his binded value
 * 
 * ex : Expr::eq('id', 2) will get "id = :id_234" with array('id_234' => 2)
 * 
 */
class Expr
{

    /**
     * Determine if $var a variable is an expression from this class
     * 
     * @param mixed $var
     * @return bool
     */
    public static function isExpr($var)
    {
        return is_array($var) && isset($var['expr']) && isset($var['bind']);
    }

    /**
     * WHERE id = :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function eq(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier = :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id <> :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function neq(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier <> :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id < :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function lt(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier < :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id <= :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function lte(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier <= :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id > :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function gt(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier > :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id >= :id | ['id' => 5]
     * 
     * @param string $identifier
     * @param mixed $value
     * 
     * @return array
     */
    public static function gte(string $identifier, $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier >= :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id >= :id_1 AND id <= :id_2 | ['id_1' => 1, 'id_2' => 5]
     * 
     * @param string $identifier
     * @param mixed $value1
     * @param mixed $value2
     * 
     * @return array
     */
    public static function between(string $identifier, $value1, $value2)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "($identifier >= :{$placeholder}_1 AND $identifier <= :{$placeholder}_2)",
            'bind' => ["{$placeholder}_1" => $value1, "{$placeholder}_2" => $value2]
        ];
    }

    /**
     * WHERE name LIKE :name | ['name' => 'bob']
     * 
     * @param string $identifier
     * @param string $value
     * 
     * @return array
     */
    public static function like(string $identifier, string $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier LIKE :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE name NOT LIKE :name | ['name' => 'bob']
     * 
     * @param string $identifier
     * @param string $value
     * 
     * @return array
     */
    public static function notLike(string $identifier, string $value)
    {
        $placeholder = self::makePlaceholder($identifier);
        return [
            'expr' => "$identifier NOT LIKE :$placeholder",
            'bind' => [$placeholder => $value]
        ];
    }

    /**
     * WHERE id IN (:id_1, :id_2, :id_3) | ['id_1' => 1, 'id_2' => 2, 'id_3' => 3]
     * 
     * @param string $identifier
     * @param array $values
     * 
     * @return array
     */
    public static function in(string $identifier, array $values)
    {
        $placeholder = self::makePlaceholder($identifier);
        $params = [];
        foreach ($values as $index => $value) {
            $params[$placeholder . '_' . $index] = $value;
        }
        return [
            'expr' => $identifier . ' IN (:' . implode(', :', array_keys($params)) . ')',
            'bind' => $params
        ];
    }

    /**
     * WHERE id NOT IN (:id_1, :id_2, :id_3) | ['id_1' => 1, 'id_2' => 2, 'id_3' => 3]
     * 
     * @param string $identifier
     * @param array $values
     * 
     * @return array
     */
    public static function notIn(string $identifier, array $values)
    {
        $placeholder = self::makePlaceholder($identifier);
        $params = [];
        foreach ($values as $index => $value) {
            $params[$placeholder . '_' . $index] = $value;
        }
        return [
            'expr' => $identifier . ' NOT IN (:' . implode(', :', array_keys($params)) . ')',
            'bind' => $params
        ];
    }

    /**
     * WHERE content IS NULL
     * 
     * @param string $identifier
     * 
     * @return array
     */
    public static function isNull(string $identifier)
    {
        return [
            'expr' => "$identifier IS NULL",
            'bind' => []
        ];
    }

    /**
     * WHERE content IS NOT NULL
     * 
     * @param string $identifier
     * 
     * @return array
     */
    public static function isNotNull(string $identifier)
    {
        return [
            'expr' => "$identifier IS NOT NULL",
            'bind' => []
        ];
    }

    /**
     * Generate placeholder for prepared statements
     * 
     * @param string $identifier
     * 
     * @return string
     */
    public static function makePlaceholder(string $identifier)
    {
        return str_replace('.', '_', $identifier) . rand(0, 999);
    }
}
