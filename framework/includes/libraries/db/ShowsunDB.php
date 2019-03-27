<?php
class ShowsunDB
{

    /**驱动配置实例*/
    private $incInstance;

    /** pdo实例*/
    private $pdoInstance;

    /**驱动名称*/
    private $name;

    /**错误信息*/
    private $errorInfo;

    /**影响行数*/
    private $affectedRows;

    /**调试器句柄*/
    private $debugHandle;

    /**调试器开关*/
    private $debug;

    public function __construct($dsn)
    {
        $this->pdoInstance = $this->getPdoInstance($dsn);
        $className = $this->loadClass($this->name);
        $this->incInstance = new $className();
        false && $this->incInstance = new DBI();
    }

    /**
     * @todo 获取单条数据
     * @author 刘小祥 (2017年11月10日)
     */
    public function getOne($query)
    {
        $sqlData = array();
        if (!isset($query['limit'])) {
            $query['limit'] = 1;
        }
        if (!isset($query['fields'])) {
            $query['fields'] = "*";
        }
        $query['limit'] = 1;
        $sql = $this->buildSql("select", $query, $sqlData);
        return $this->getOneBySql($sql, $sqlData);
    }

    /**
     * @todo 获取多条数据
     * @author 刘小祥 (2017年11月10日)
     */
    public function getData($query)
    {
        $sqlData = array();
        if (!isset($query['fields'])) {
            $query['fields'] = "*";
        }
        $sql = $this->buildSql("select", $query, $sqlData);
        return $this->getDataBySql($sql, $sqlData);
    }

    /**
     * @todo 增加
     * @author 刘小祥 (2017年11月10日)
     */
    public function doInsert($query)
    {
        $sqlData = array();
        $sql = $this->buildSql("insert", $query, $sqlData);
        $statement = $this->pdoExecute($sql, $sqlData);
        if ($this->errorInfo[0] > 0) {
            return false;
        }
        return true;
    }

    /**
     * @todo 修改
     * @author 刘小祥 (2017年11月10日)
     */
    public function doUpdate($query)
    {
        $sqlData = array();
        $sql = $this->buildSql("update", $query, $sqlData);
        $statement = $this->pdoExecute($sql, $sqlData);
        if ($this->errorInfo[0] > 0) {
            return false;
        }
        return true;
    }

    /**
     * @todo 删除
     * @author 刘小祥 (2017年11月10日)
     */
    public function doDelete($query)
    {
        $sqlData = array();
        $sql = $this->buildSql("delete", $query, $sqlData);
        $statement = $this->pdoExecute($sql, $sqlData);
        if ($this->errorInfo[0] > 0) {
            return false;
        }
        return true;
    }

    /**
     * @todo 执行sql
     * @author 刘小祥 (2017年11月10日)
     */
    public function execute($sql, $sqlData = array())
    {
        $this->pdoExecute($sql, $sqlData);
        if ($this->errorInfo[0] > 0) {
            return false;
        }
        return true;
    }

    /**
     * @todo 根据SQL获取单条记录
     * @author 刘小祥 (2017年11月10日)
     */
    public function getOneBySql($sql, $data = array())
    {
        $statement = $this->pdoExecute($sql, $data);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @todo 根据SQL获取多条记录
     * @author 刘小祥 (2017年11月10日)
     */
    public function getDataBySql($sql, $data = array())
    {
        $statement = $this->pdoExecute($sql, $data);
        //var_dump($statement->errorInfo());
        /*log::jsonInfo($statement->debugDumpParams());*/
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @todo 开启事务
     * @author 刘小祥 (2017年11月14日)
     */
    public function transStart()
    {
        $this->pdoInstance->beginTransaction();
    }

    /**
     * @todo 提交事务
     * @author 刘小祥 (2017年11月14日)
     */
    public function transCommit()
    {
        $this->pdoInstance->commit();
    }

    /**
     * @todo 回滚事务
     * @author 刘小祥 (2017年11月14日)
     */
    public function transBack()
    {
        $this->pdoInstance->rollBack();
    }

    /**
     * @todo 获取最后插入的 ID
     * @author 刘小祥 (2017年11月10日)
     */
    public function getLastInsertId()
    {
        return $this->pdoIncInvoke("getLastInsertId");
    }

    /**
     * @todo 获取影响行数
     * @author 刘小祥 (2017年11月13日)
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @todo 开启/关闭调试
     * @author 刘小祥 (2017年11月14日)
     */
    public function setDebugMode($debug = true)
    {
        $this->debug = $debug;
    }

    /**
     * @todo 设置错误处理函数
     * @author 刘小祥 (2017年11月14日)
     */
    public function setDebugHandle($debugHandle)
    {
        $this->debugHandle = $debugHandle;
    }

    /**
     * @todo 生成SQL
     * @author 刘小祥 (2017年11月10日)
     */
    private function buildSql($opt, $query, &$sqlData = null)
    {
        $sqlData = array();
        if (isset($query['set']) && is_array($query['set'])) {
            foreach ($query['set'] as $row) {
                $sqlData[] = $row;
            }
        }
        if (isset($query['cond']) && is_array($query['cond'])) {
            foreach ($query['cond'] as $key => $row) {
                if (is_numeric($key)) {
                    continue;
                }

                $row = is_array($row) ? $row[1] : $row;
                $sqlData[] = $row;
            }
        }
        $optMethod = "build" . ucfirst($opt) . "Sql";
        return $this->incInvoke($optMethod, $query);
    }

    /**
     * @todo pdo
     * @author 刘小祥 (2017年11月9日)
     */
    private function getPdoInstance($dsn)
    {
        $config = parse_url($dsn);
        $config['path'] = str_replace('/', '', $config['path']);
        $this->name = $config['scheme'];
        $pdoDsn = sprintf("%s:host=%s;dbname=%s", $config['scheme'], $config['host'], $config['path']);
        return new PDO($pdoDsn, $config['user'], $config['pass']);
    }

    private function loadClass($name)
    {
        include "dbi.php";
        include $name . ".inc.php";
        return ucfirst($name) . "Inc";
    }

    /**
     * @todo 调用方法
     * @author 刘小祥 (2017年11月10日)
     */
    private function incInvoke($method)
    {
        $args = func_get_args();
        unset($args[0]);
        return call_user_func_array(array($this->incInstance, $method), $args);
    }

    /**
     * @todo 调用inc 的专有 pdo方法
     * @author 刘小祥 (2017年11月10日)
     */
    private function pdoIncInvoke($method)
    {
        $args = func_get_args();
        unset($args[0]);
        $methodName = $this->incInstance->pdoMethods[$method];
        return call_user_func_array(array($this->pdoInstance, $methodName), $args);
    }

    /**
     * @todo pdo execute
     * @author 刘小祥 (2017年11月10日)
     */
    private function pdoExecute($sql, $sqlData)
    {
        $statement = $this->pdoInstance->prepare($sql);
        $statement->execute($sqlData);
        $this->affectedRows = $statement->rowCount();
        $this->errorInfo = $statement->errorInfo();
        if (($this->errorInfo[0] > 0 || $this->debug) && $this->debugHandle) {
            foreach ($sqlData as &$row) {
                $row = str_replace("'", "\'", $row);
            }
            unset($row);
            $sql = str_replace("?", "'%s'", $sql);
            $param = array_merge(array($sql), $sqlData);
            $sql = call_user_func_array("sprintf", $param);


	        if (defined('IS_API')) {
		        log::jsonInfo($sql);
	        }
	        
            $info = array($sql);
            $info = array_merge($info, $this->errorInfo);

            if($_REQUEST['debug']==1)
                printc($sql);
            
            //call_user_func_array($this->debugHandle, $info);
            if ($this->errorInfo[0] > 0) {
                return new PDOStatement();
            }
        }
        return $statement;
    }

}
