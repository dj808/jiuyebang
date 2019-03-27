<?php
/**
 * @todo 数据库驱动方法接口(类)
 * @author 刘小祥 (2017年11月14日)
 */
abstract class DBI
{

    /**定义INSERT语句生成规则*/
    abstract public function buildInsertSql($query);

    /**定义DELETE语句生成规则*/
    abstract public function buildDeleteSql($query);

    /**定义UPDATE语句生成规则*/
    abstract public function buildUpdateSql($query);

    /**定义查询语句生成规则*/
    abstract public function buildSelectSql($query);

}
