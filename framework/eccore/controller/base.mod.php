<?php
/**
 * 模型操作基类
 * @date 2016-03-01
 * 请勿擅自修改，如有需要发邮件说明
 */
if (!defined('IN_ECM')) {
    die('Forbidden');
}

class BaseMod {
    /**最后一个影响到的缓存key*/
    public $lastAffectedKey;
    public $table , $modName , $gvar;

    /**数据库对象*/
    public $db;

    /**缓存对象*/
    public $cache;

    /** 主键 **/
    protected $pk;

    public function __construct($table = '') {
        $this->initCache();
        if ($table) {
            if (strpos($table , '.')) {
                $this->table = $table;
            }
            else {
                $this->table = DB_PREFIX . $table;
            }
        }

        $this->pk = "id";
    }

    /**
     * @todo   关联查询
     * @author 刘小祥 (2017年11月15日)
     */
    public function with() {

    }

    /**
     * 获取信息
     * @date 2011-5-22
     * @param 记录编号 int $id
     * @param 查询字段 string $field
     * @return array
     */
    public function getRow($id = 0 , $field = '*') {
        $query = [
            'cond'   => [$this->pk => $id , 'mark' => 1] ,
            'fields' => "*" ,
        ];
        $this->initQuery($query);
        return $this->getOne($query);
    }

    /**
     *根据字段查询信息(与mark一起查询)
     * @author 刘小祥
     * @date   2015-8-15
     * @return array
     */
    public function getRowByField($field , $value , $id = 0 , $fields = '*') {
        $cond = [
            $field => $value ,
            'mark' => 1 ,
        ];
        if ($id) {
            $cond[] = "{$this->pk} != {$id}";
        }
        $query = array('fields' => $fields , 'cond' => $cond);
        $this->initQuery($query);
        $row = $this->getOne($query);
        return $row;
    }

    /**
     *根据多个字段查询信息(与mark一起查询)
     * @author 刘小祥
     * @date   2015-8-15
     * @return array
     */
    public function getRowByAttr($data , $fields = "*" , $id = false) {
        $cond = $data;
        if ($id) {
            $cond[] = "{$this->pk} != {$id}";
        }
        $cond[] = "mark=1";
        $query = array('fields' => $fields , 'cond' => $cond);
        $this->initQuery($query);
        $row = $this->getOne($query);
        return $row;
    }

    /**
     * 设置字段值
     * @author 刘小祥 (2017年3月3日)
     */
    public function setFieldValue($field , $value , $id) {
        return $this->edit(array($field => $value) , $id);
    }

    /**
     * 物理删除记录
     * @param 记录编号 int $id
     * @return bool
     */
    public function doDrop($id = 0) {
        $query = [
            'cond' => [$this->pk => $id] ,
        ];
        $this->initQuery($query);
        return $this->db->doDelete($query);
    }

    /**
     * 逻辑删除记录
     * @param int  表主键 且必须是表主键的形式才可以执行该操作
     * @param int $is_sql
     * @return int 影响更新的条数 如果为0则更新失败
     */
    public function doMark($id) {
        $query = [
            'cond' => [$this->pk => $id] ,
            'set'  => ['mark' => 0] ,
        ];
        $this->initQuery($query);
        return $this->db->doUpdate($query);
    }

    /**
     * @todo   更新表
     * @author 刘小祥 (2017年11月14日)
     */
    public function doUpdate($query) {
        $this->initQuery($query);
        return $this->db->doUpdate($query);
    }

    /**
     * @todo   根据查询规则获取单条记录
     * @author 刘小祥 (2017年11月14日)
     */
    public function getOne($query) {
        $this->initQuery($query);
        return $this->db->getOne($query);
    }

    /**
     * @todo   获取记录总和
     * @author 刘小祥 (2017年11月14日)
     */
    public function getSum($cond , $select , $table = false) {
        $query = [
            'fields' => "SUM({$select}) AS total" ,
            'cond'   => $cond ,
        ];
        if ($table) {
            $query['table'] = DB_PREFIX . $table;
        }
        $this->initQuery($query);
        $info = $this->db->getOne($query);
        return (int)$info['total'];
    }

    /**
     * @todo   获取记录总数
     * @author 刘小祥 (2017年11月14日)
     */
    public function getCount($cond , $table = false , $isSql = false) {
        $query = [
            'fields' => "COUNT(*) AS total" ,
            'cond'   => $cond ,
        ];
        if ($table) {
            $query['table'] = DB_PREFIX . $table;
        }
        $this->initQuery($query);
        $info = $this->db->getOne($query);
        return (int)$info['total'];
    }

    /**
     * @todo   数据
     * @author 刘小祥 (2017年11月14日)
     */
    public function doInsert($data) {
        $query = ['set' => $data];
        $this->initQuery($query);
        $this->db->doInsert($query);
        return $this->db->getLastInsertId();
    }

    /**
     * @todo   编辑信息
     * @author 刘小祥 (2017年11月14日)
     */
    public function doEdit($id , $data) {
        $query['cond'] = [
            $this->pk => $id ,
        ];
        $query['set'] = $data;
        $this->initQuery($query);
        return $this->doUpdate($query);
    }

    /**
     * @todo   获取多条数据
     * @author 刘小祥 (2017年11月14日)
     */
    public function getData($query) {
        $this->initQuery($query);
        $callback = null;
        if (isset($query['fields']) && is_array($query['fields'])) {
            $callback = $query['fields'][0];
            $query['fields'] = isset($query['fields'][1]) ? $query['fields'][1] : $this->pk;
        }
        $data = $this->db->getData($query);
        if (isset($query['pri'])) {
            $newData = array();
            foreach ($data as $row) {
                $newData[$row[$query['pri']]] = $row;
            }
            $data = $newData;
        }
        if ($callback) {
            foreach ($data as &$row) {
                if (strpos($callback , '^')) {
                    $newCallback = explode('^' , $callback);
                    $row = $this->$newCallback[0]($row[$query['fields']] , $newCallback[1]);
                }
                else {
                    $row = $this->$callback($row[$query['fields']]);
                }
            }
            unset($row);
        }
        return $data ? $data : [];
    }

    /**
     * @todo   根据SQL获取单条数据
     * @author 刘小祥 (2017年11月14日)
     */
    public function getOneBySql($sql) {
        if (log::hasMode(log::MODE_DB)) {
            log::jsonInfo($sql);
        }
        $this->initDb();
        return $this->db->getOneBySql($sql);
    }

    /**
     * @todo   根据SQL获取多条记录
     * @author 刘小祥 (2017年11月14日)
     */
    public function getDataBySql($sql , $master = false) {
        if (log::hasMode(log::MODE_DB)) {
            log::jsonInfo($sql);
        }
        $this->initDb();
        return $this->db->getDataBySql($sql);
    }

    /**
     * 根据筛选条件获取数据ID
     * @param array  string $cond - 筛选条件
     * @return array
     */
    public function getIds($cond , $table = '' , $key = 'id') {
        $table = $table ? $table : $this->table;
        if (!strpos($table , '.')) {
            $table = DB_PREFIX . str_replace(DB_PREFIX , '' , $table);
        }

        $query = array(
            'fields' => $key ,
            'table'  => $table ,
            'cond'   => $cond ,
        );
        $info = $this->getData($query);
        $rs = array();
        if ($info) {
            foreach ($info as $row) {
                $rs[] = $row[$key];
            }
        }
        return $rs;
    }

    /**
     * 获取数据表字段列表
     * @param string $table - 表名
     * @return array
     */
    public function getFields($table = "") {
        if (!$table) {
            $table = $this->table;
        }

        $table = DB_PREFIX . str_replace(DB_PREFIX , '' , $table);
        $sql = "DESC {$table}";
        $res = $this->getDataBySql($sql , false);
        if (!$res) {
            return array();
        }

        $result = array();
        foreach ($res as $row) {
            $result[] = $row['Field'];
        }
        return $result;
    }

    /**
     * @todo    分页操作
     * @author  Malcolm  (2018年02月19日)
     */
    public function pageData($query , $mod , $fields = 'getShortInfo' , $num = 0) {
        $data = Zeus::pageData($query , $mod , $fields , $num);
        return $data;
    }

    /**
     * 带分页的查询（配合layui.table数据规则）
     */
    public function findWithPager($params , $pageIndex = 1 , $pageSize = 10) {
        // 查询条件
        $query = [
            'cond'     => isset($params['cond']) ? $params['cond'] : '*' ,
            'order_by' => isset($params['order_by']) ? $params['order_by'] : 'id' ,
            'limit'    => (($pageIndex - 1) * $pageSize) . ',' . $pageSize
        ];

        $count = $this->getCount($query['cond'] , false);
        $list = $this->getData($query);

        $res = [
            'code'   => 0 ,
            'status' => true ,
            'msg'    => '' ,
            'count'  => $count ,
            'data'   => $list
        ];
        return $res;
    }

    //==================================缓存处理===============================================

    /**
     * 获取函数缓存Key
     * @author 刘小祥
     * @param string funcName 调用方法名称(不带前缀'_cache')
     * */
    public function getFuncKey($funcName) {
        //拼接key
        $argList = func_get_args();
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        $key = implode("_" , $argList);
        return $key;
    }

    /**
     * 获取函数缓存
     * @author 刘小祥
     * @param string funcName 调用方法名称(带前缀'_cache')
     * @param mixed ... 其他参数列表
     * @return mixed
     */
	public function getFuncCache($funcName){
		//拼接key
		$argList = func_get_args();
		if ($this->table)  array_unshift($argList, $this->table);
		$key = implode("_", $argList);
		//从缓存中获取
		$data = $this->getCache($key);
		if( !$data ){
			array_shift($argList);
			if ($this->table) array_shift($argList);
			$act = "_cache".ucfirst($funcName);
			$data = call_user_func_array(array($this, $act), $argList);
			$this->setCache($key, $data);
		}
		return $data ;
	}

    /**
     * 获取函数缓存扩展
     * @author 刘小祥
     * @param string $key      set
     * @param string $funcName 调用方法名称(不带前缀'_cache')
     * @param int    $time     缓存时长
     * @param mixed ... 其他参数列表
     * @return mixed
     */
    public function getFuncCacheEx($funcName , $time = 0) {
        $argList = func_get_args();
        unset($argList[1]);
        $argList = array_values($argList);
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        $key = implode("_" , $argList);
        $data = $this->getCache($key);
        if (!$data) {
            array_shift($argList);
            if ($this->table) {
                array_shift($argList);
            }

            $data = call_user_func_array(array($this , $funcName) , $argList);
            $r = $this->setCache($key , $data , $time);
        }
        return $data;
    }

    /**
     * 重置函数缓存
     * @author 刘小祥
     * @param string $funcName 调用方法名称(带前缀'_cache')
     * @param mixed ... 其他参数列表
     * @return mixed
     */
    public function resetFuncCache($funcName) {
        //拼接key
        $argList = func_get_args();
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        $key = implode("_" , $argList);
        //延迟1s缓存
        $this->deleteCache($key , 0);
        /** 设置缓存
         * array_shift($argList);
         * if ($this->table) array_shift($argList);
         * $act = "_cache".ucfirst($funcName);
         * $data = call_user_func_array(array($this, $act), $argList);
         * $this->setCache($key, $data);**/
        return true;
    }

    /**
     * 修改函数的缓存
     * @param string $funcName 方法名称
     * @param  ....函数其他参数列表
     * @param mixed  $data     修改后的数据
     * @author 刘小祥 (2016年9月20日)
     */
    public function setFuncCache($funcName) {
        //拼接key
        $argList = func_get_args();
        //压入表名
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        //取出数据
        $data = array_pop($argList);
        $key = implode("_" , $argList);
        return $this->setCache($key , $data);
    }

    /**
     * 重置函数缓存扩展
     * @author 刘小祥
     * @param string $funcName 调用方法名称(不带前缀'_cache')
     * @param int    $time     缓存时长
     * @param mixed ... 其他参数列表
     * @return mixed
     */
    public function resetFuncCacheEx($funcName , $time = 0) {
        //拼接key
        $argList = func_get_args();
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        $key = implode("_" , $argList);
        //删除缓存
        $this->deleteCache($key , 0);
        /** 设置缓存
         * array_shift($argList);
         * if ($this->table) array_shift($argList);
         * $data = call_user_func_array(array($this, $funcName), $argList);
         * $this->setCache($key, $data, $time); **/
        return true;
    }

    /**
     *删除函数缓存
     * @author 刘小祥
     * @param string $funcName 方法名称
     * @return void
     */
    public function deleteFuncCache($funcName) {
        $argList = func_get_args();
        if ($this->table) {
            array_unshift($argList , $this->table);
        }

        $key = implode("_" , $argList);
        $this->deleteCache($key);
    }

    /**
     * 添加缓存前缀
     * @param string $key    缓存的key
     * @param bool   $master 是否读取主缓存
     * @return mixed
     */
    public function getCache($key , $master = true) {
        $key = $this->getKey($key);
        $this->initCache();
        return $this->cache->get($key);
    }

    /**
     * 设置缓存
     * @param string $key
     * @param array  $value
     * @return bool
     */
    public function setCache($key , $value , $ttl = 0) {
        if (isset($GLOBALS['trans']) && $GLOBALS['trans'] === true) {
            $GLOBALS['trans_keys'][] = $key;
        }
        $key = $this->getKey($key);
        if (!$value) {
            return false;
        }

        $this->initCache();
        return $this->cache->set($key , $value , $ttl);
    }

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function deleteCache($key , $delay = 0) {
        $key = $this->getKey($key);
        $this->initCache();
        return $this->cache->delete($key , $delay);
    }

    /**
     * 拼接缓存前缀
     * @param string $key
     * @return string
     */
    protected function getKey($key) {
        $this->lastAffectedKey = $key;
        $key = str_replace(MKEY . "_" , "" , $key);
        $key = MKEY . "_" . $key;
        return $key;
    }

    /**
     * 执行sql
     * @author 刘小祥
     * @date   2016年4月9日
     * @param string $sql    执行的sql
     * @param bool   $master 是否使用主库
     * @return ADORecordSet adodb结果集
     */
    public function execute($sql , $master = true) {
        if (log::hasMode(log::MODE_DB)) {
            log::jsonInfo($sql);
        }
        $this->initDb();
        return $this->db->execute($sql);

    }

    /**
     * 获取上次执行的SQL影响的行数
     * @author 刘小祥
     * @date   2016年4月9日
     * @param bool $master 是否与使用主库
     * @param int 影响到的行数
     */
    public function affectedRows($master = true) {
        if ($master || $this->isForceDbMaster() || $this->dbSlave == false) {
            $this->initDb(true);
            return $this->masterDb->Affected_Rows();
        }
        else {
            $this->initDb(false);
            return $this->slaveDb->Affected_Rows();
        }
    }

    /**
     * 获取上一个插入的ID
     * @author 刘小祥
     * @date   2016年4月9日
     * @param bool $master 是否使用主库
     * @return int 上一个插入的ID
     */
    public function getInsertID($master = true) {
        if ($master || $this->isForceDbMaster() || $this->dbSlave == false) {
            $this->initDb(true);
            return $this->masterDb->Insert_ID();
        }
        else {
            $this->initDb(false);
            return $this->slaveDb->Insert_ID();
        }
    }

    /**
     *开始事务
     * @author 刘小祥
     * @date   2015-10-10
     * @return null
     */
    public function transStart() {
        $this->initDb();
        //事务-缓存相关处理
        $GLOBALS['trans'] = true;
        $transId = uniqid("trans_");
        $GLOBALS['trans_id'] = $transId;
        $GLOBALS['trans_keys'] = array();
        $info = debug_backtrace();
        $this->setCache($transId , $info[0]);
        $this->db->transStart();
    }

    /**
     *提交并结束事务
     * @author 刘小祥
     * @date   2015-10-10
     * @return null
     */
    public function transCommit() {
        $this->initDb();
        $this->db->transCommit();
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = array();
        $this->deleteCache($GLOBALS['trans_id']);
    }

    /**
     *回滚并结束事务
     * @author 刘小祥
     * @date   2015-10-10
     * @return null
     */
    public function transBack() {
        //回滚数据库命令
        $this->initDb();
        $this->db->transBack();
        //$this->execute("ROLLBACK");
        //回滚缓存
        foreach ($GLOBALS['trans_keys'] as $key) {
            $this->deleteCache($key);
        }
        $this->deleteCache($GLOBALS['trans_id']);
        $GLOBALS['trans'] = false;
        $GLOBALS['trans_keys'] = array();
    }

    /**
     * 判断当前是否打开事务
     * @author 刘小祥 (2016年9月19日)
     */
    public function isTransStarted() {
        return (isset($GLOBALS['trans']) && $GLOBALS['trans'] == true);
    }

    /**
     * 用于内部初始化数据库对象
     * @author 刘小祥
     * @date   2016年4月9日
     * @param bool 是否为主库
     * @param ADOConnection adodb数据库对象
     */
    private function initDb() {
        if (!$this->db) {
            $this->db = db();
        }
    }

    /**
     * 用于内部初始化缓存对象
     * @author 刘小祥
     * @date   2016年4月9日
     * @param bool 是否为主缓存
     * @param RedisServer redis对象
     */
    private function initCache() {
        $this->cache = cache_server();
    }

    /**
     * 打开/关闭调试模式
     * @author 刘小祥 (2016年4月29日)
     */
    public function setDebugMode($debug = true) {
        $this->initDb();
        $this->db->setDebugMode($debug);

    }

    private function initQuery(&$query) {
        $this->initDb();
        if (!isset($query['table'])) {
            $query['table'] = $this->table;
        }
    }
    
    /**
     * 获取结果中指定字段聚合一维数组
     * @author Zhulx (2018年7月26日)
     */
    public function getSingle($query,$field = "",$format = 'string'){
        $data = $this->getData($query);
        if($field){
            switch ($format){
                case 'string':
                    $data =  implode(',',array_column($data,$field));
                    break;   
                case 'array' :
                    $data =  array_column($data,$field);
                    break;
            }
        }
        return $data;
    }

}
