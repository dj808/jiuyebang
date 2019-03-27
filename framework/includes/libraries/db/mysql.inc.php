<?php

/**
 * @todo mysql驱动类
 * @author 刘小祥 (2017年11月14日)
 */
class MysqlInc extends DBI
{

    public $pdoMethods;

    public function __construct()
    {
        $this->pdoMethods = array(
            "getLastInsertId" => "lastInsertId",
        );
    }

    public function buildInsertSql($query)
    {
        $fields = $values = array();
        foreach ($query['set'] as $field => $row) {
            $fields[] = "`{$field}`";
            $values[] = "?";
        }
        $fields = implode(",", $fields);
        $values = implode(",", $values);
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $query['table'], $fields, $values);
        return $sql;
    }

    public function buildDeleteSql($query)
    {
        $cond = array();
        if (isset($query['cond']) && is_array($query['cond'])) {
            foreach ($query['cond'] as $field => $row) {
                $opt = is_array($row) ? $row[0] : "=";
                $cond[] = is_numeric($field) ? $row : "`{$field}` {$opt} ?";
            }
            $cond = implode(" AND ", $cond);
        }
        if (isset($query['cond']) && is_string($query['cond'])) {
            $cond = $query['cond'];
        }
        $cond = $cond ? "WHERE " . $cond : "";
        $sql = sprintf("DELETE FROM %s %s", $query['table'], $cond);
        return $sql;
    }

    public function buildUpdateSql($query)
    {
        $set = array();
        $cond = array();
        foreach ($query['set'] as $field => $row) {
            $set[] = "`{$field}` = ?";
        }
        if (isset($query['cond']) && is_array($query['cond'])) {
            foreach ($query['cond'] as $field => $row) {
                $opt = is_array($row) ? $row[0] : "=";
                $cond[] = is_numeric($field) ? $row : "`{$field}` {$opt} ?";
            }
            $cond = implode(" AND ", $cond);
        }
        if (isset($query['cond']) && is_string($query['cond'])) {
            $cond = $query['cond'];
        }
        $cond = $cond ? "WHERE " . $cond : "";
        $set = implode(",", $set);
        $sql = sprintf("UPDATE %s set %s %s", $query['table'], $set, $cond);
        return $sql;
    }

    public function buildSelectSql($query)
    {
        $cond = array();
        if (isset($query['cond']) && is_array($query['cond'])) {
            foreach ($query['cond'] as $field => $row) {
                $opt = is_array($row) ? $row[0] : "=";
                $cond[] = is_numeric($field) ? $row : "`{$field}` {$opt}  ?";
            }
            $cond = implode(" AND ", $cond);
        }
        if (isset($query['cond']) && is_string($query['cond'])) {
            $cond = $query['cond'];
        }
        $cond = $cond ? "WHERE " . $cond : "";
        $fields = empty($query['fields']) ? "*" : $query['fields'];
        $groupBy = empty($query['group_by']) ? "" : "GROUP BY " . $query['group_by'];
        $orderBy = empty($query['order_by']) ? "" : "ORDER BY " . $query['order_by'];
        $limit = empty($query['limit']) ? "" : "limit " . $query['limit'];
        $sql = sprintf("SELECT %s FROM %s %s %s %s %s", $fields, $query['table'], $cond, $groupBy, $orderBy, $limit);
        /* log::jsonInfo('################');
        log::jsonInfo($sql);
        log::jsonInfo('################');*/
        return $sql;
    }

}
