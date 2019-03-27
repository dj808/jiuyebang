<?php

/**
 * 常用基类模型
 * @author shengwx
 * @date 2015-8-17
 */
class CBackendApp extends BackendApp
{

    protected $userId, $userInfo, $req, $mod;
    public function __construct()
    {
        parent::__construct();
        $_GET['debug'] && m()->setDebugMode();
        $this->appJs();
        $this->setUrl();
        $this->req = $_REQUEST;
    }

    public function pageInit($mod = '')
    {
        if (!$mod && $this->mod) {
            $this->pageMod = $this->mod;
        }
        if ($mod) {
            if (is_object($mod)) {
                $this->pageMod = $mod;
            } else {
                $this->pageMod = m($mod);
            }
        }
        $this->fieldList = $this->pageMod->getFieldInfoList();
        $this->cond = array();
    }

    public function setKeywords($fields, $wdField = 'wd')
    {
        $wd = trim($this->req[$wdField]);
        $cond = array();
        foreach ($fields as $field) {
            if ($this->fieldList[$field] == "int") {
                $cond[] = "{$field}='{$wd}'";
            } else {
                $cond[] = "{$field} LIKE '%{$wd}%'";
            }
        }
        $cond = implode(" OR ", $cond);
        $this->cond[] = "({$cond})";
    }

    public function addCond($param)
    {
        if (is_string($param)) {
            $this->cond[] = $param;
            return true;
        }
        if (is_array($param)) {
            foreach ($param as $p) {
                if (!isset($this->fieldList[$p])) {
                    continue;
                }

                $type = $this->fieldList[$p]['type'];
                if ($type == "int") {
                    $value = intval($this->req[$p]);
                } else {
                    $value = trim($this->req[$p]);
                }
                if ($value) {
                    $this->cond[$p] = $this->req[$p];
                }
            }
            return true;
        }
        return false;
    }

    public function pageData($func = "getInfo", $orderBy = "id DESC", $fields = "id")
    {
        $query['cond'] = $this->cond;
        $query['fields'] = $fields;
        $query['order_by'] = $orderBy;

        $data = $this->pageMod->pageData($query);
        $data['list'] = (array) $data['list'];
        $field = $fields = "*" ? "id" : $fields;
        foreach ($data['list'] as &$row) {
            if (is_string($func)) {
                $row = $this->pageMod->$func($row[$field]);
                continue;
            }
            $row = call_user_func_array($func, array($row[$field]));
        }
        unset($row);
        $this->assign("data", $data);
        return $data;
    }

    /**
     * 搜索公用方法
     * @author 陈晨
     * @param string $keywords 搜索关键词
     * @param array $param 搜索参数
     * @return array
     * @date 16/10/10
     */
    public function search($param)
    {
        $keywords = trim($_GET['wd']);
        if ($keywords) {
            if (is_numeric($keywords)) {
                $cond[] = "id = {$keywords}";
            } else {
                $cond[] = "{$param} like '%{$keywords}%'";
            }
            $this->assign("keywords", $keywords);
            return $cond;
        } else {
            return array();
        }
    }

    /**
     * 分页公共方法
     * @author 陈晨
     * @param array $cond 条件
     * @param object $mod mod
     * @return array
     * @date 16/10/12
     */
    /* public function publicPage($cond,$mod)
    {
    $cond[] = 'mark = 1';
    $query = array(
    'pri'        =>'id',
    'cond'        =>$cond,
    'order_by'    =>sortOrder('id', 'desc')
    );
    $data = $mod->pageData($query);
    $data['list'] = $data['list'] ? $data['list'] : array();
    foreach ( $data['list'] as &$row) {
    $row = $mod->getInfo($row['id']);
    }
    unset($row);
    return $data;
    }  */

    /**
     * 每个翻页都需要加载的
     * @author 陈晨
     * @param $data 分页数据
     * @return array
     * @date 16/7/6
     */
    public function publicPage($data, $flag)
    {
        $this->assign('list', $data['list']);
        $this->assign('ph', $data['ph']);
        $this->assign('total', $data['total']);
        $this->assign('pages', $data['pages']);
        $this->assign('flag', $flag);
    }

    /**
     * 设置页面URL
     *
     * @author    shengwx
     * @param    void
     * @return    void
     */
    private function setUrl()
    {
        $app = APP;
        $act = ACT;
        $url = "?app={$app}&act={$act}";
        $sUrl = "?app={$app}&act=index";
        $editUrl = "?app={$app}&act=edit";
        $id = isset($_GET['id']) ? (int) $_GET['id'] : '';
        if ($id) {
            $editUrl .= "&id={$id}";
        }
        $this->assign('url', $url);
        $this->assign('sUrl', $sUrl);
        $this->assign('editUrl', $editUrl);
    }

    /**
     * APP对应JS
     *
     * @author    shengwx
     * @param    int $pn
     * @return    int
     */
    protected function appJs($app = array())
    {
        if (!$app) {
            if (file_exists(ROOT_PATH . '/public/main/assets/app/' . APP . '.js')) {
                $app[] = APP;
            }
        }
        $this->assign('appJs', $app);
    }

    /**
     * 修改排序顺序
     *
     * @author    shengwx
     * @param    void
     * @return    array
     */
    protected function ajaxCol()
    {
        $id = (int) $_REQUEST['id'];
        $sort = (int) $_REQUEST['value'];
        if ($id <= 0) {
            return message('请指定要修改排序的记录！', false);
        }
        $info = $this->mod->getInfo($id);
        if (!$info) {
            return message('指定的记录不存在，请核实后再试！', false);
        }
        if ($sort < 0) {
            return message('排序顺序不能小于0！', false);
        }
        $data['sort_order'] = $sort;
        $rowId = $this->mod->edit($data, $id);
        if (!$rowId) {
            return message('操作失败，请重试', false);
        }
        return message();
    }

    /**
     * 批量删除
     *
     * @author    shengwx
     * @param    string $ids
     * @return    array
     */
    private function batchDrop($ids)
    {
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $key => $id) {
            $this->mod->drop($id);
        }
        $count = count($id_arr);
        return message("成功删除{$count}条记录");
    }

    /**
     * 批量重置缓存
     *
     * @author    shengwx
     * @param    object $mod
     * @return    array
     */
    protected function batchCacheReset($mod = false)
    {
        if (!IS_POST) {
            return;
        }
        $post = $_POST;
        $ids = trim($post['ids']);
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $key => $id) {
            $mod = $mod ? $mod : $this->mod;
            $mod->_cacheReset($id);
        }
        $count = count($id_arr);
        echo json_encode(message("成功重置{$count}条记录", true));
        return;
    }

    /**
     * 共用查询字段
     *
     * @author    shengwx
     * @param     array $cond
     * @param    string $fields
     * @return    void
     */
    protected function queryCond(&$cond, $fields, $type = 'text', $field_arr = array())
    {
        $field = isset($_GET[$fields]) ? (int) $_GET[$fields] : '';
        if ($field) {
            $cond[] = "{$fields}='{$field}'";
        }
        if ($type == 'select') {
            if (empty($field_arr) && isset($this->mod->$fields)) {
                $field_arr = $this->mod->$fields;
            }
            $field_option = make_option($this->mod->$fields, $field);
            $this->assign("{$fields}_option", $field_option);
        } else {
            $this->assign("{$fields}", $field);
        }
    }

    /**
     * 共用关键词查询字段
     *
     * @author    shengwx
     * @param     array $cond
     * @param    string $fields
     * @return    void
     */
    protected function keywordsCond(&$cond = array(), $fields = 'name', $pri = 'id')
    {
        $keywords = isset($_GET['wd']) ? trim($_GET['wd']) : '';
        if ($keywords) {
            if (is_numeric($keywords)) {
                $cond[] = "{$pri}={$keywords}";
            } else {
                if (strpos($fields, ',') !== false) {
                    $fields = explode(',', $fields);
                    $tmp = array();
                    foreach ($fields as $field) {
                        $tmp[] = "{$field} LIKE '%{$keywords}%'";
                    }
                    $cond[] = '(' . implode(' OR ', $tmp) . ')';
                } else {
                    $cond[] = "{$fields} LIKE '%{$keywords}%'";
                }
            }
        }
        $this->assign('keywords', $keywords);
    }

    /**
     * 共用时间查询字段
     *
     * @author    shengwx
     * @param     array $cond
     * @param    string $fields
     * @param   $is_second 是否加载秒
     * @return    void
     */
    protected function timeCond(&$cond = array(), $fields = 'add_time', $is_second = true)
    {
        $from_time = isset($_GET['from_time']) ? trim($_GET['from_time']) : '';
        $f_time = strtotime($from_time);
        $to_time = isset($_GET['to_time']) ? trim($_GET['to_time']) : '';
        $t_time = strtotime($to_time);
        if ($from_time && !$to_time) {
            $cond[] = "{$fields} >= {$f_time}";
            $this->assign('from_time', $from_time);
        }
        if (!$from_time && $to_time) {
            $cond[] = "{$fields} <= {$t_time}";
            $this->assign('to_time', $to_time);
        }
        if ($from_time && $to_time) {
            $cond[] = "({$fields} BETWEEN {$f_time} AND {$t_time})";
            $this->assign('from_time', $from_time);
            $this->assign('to_time', $to_time);
        }
        $this->assign('is_second', $is_second);
        $calendar = $this->fetch('public/calendar.html');

        $this->assign('calendar', $calendar);
    }

    /**
     * 获取列表
     *
     * @author    shengwx
     * @param    string $cond
     * @param    string $order_by
     * @param    int $is_sql
     * @return    void
     */
    protected function getList($cond = 'mark=1', $order_by = 'id DESC', $is_page = 1, $is_sql = 0)
    {
        $query = array(
            'pri' => 'id',
            'fields' => 'id',
            'cond' => $cond,
            'order_by' => $order_by,
        );
        //单页条数
        $pn = $this->getPageNumber();
        if ($is_page) {
            $result = $this->mod->pageData($query, array('per_page' => $pn, 'is_sql' => $is_sql));
            $array = $result['list'];
            $this->assign('ph', $result['ph']);
            $this->assign('pn', $pn);
        } else {
            $array = $this->mod->getData($query);
        }
        $list = array();
        if ($array) {
            foreach ($array as $id => $val) {
                // $this->mod->_cacheReset($id);
                $info = $this->mod->getInfo($id);
                $list[$id] = $info;
            }
        }
        $this->assign('list', $list);
        return $list;
    }

    /**
     * 获取每页显示记录数
     *
     * @author    shengwx
     * @param    int $pn
     * @return    int
     */
    protected function getPageNumber($pn = 20)
    {
        $pn = isset($_GET['pn']) && $_GET['pn'] ? (int) $_GET['pn'] : $pn;
        if ($pn <= 0 || $pn > 400) {
            $pn = 20;
        }
        return $pn;
    }

    /**
     * 选择每页显示记录数
     *
     * @author    shengwx
     * @param    int $pn
     * @return    string
     */
    protected function pageNumber($pn = 20)
    {
        $str = "<option value='{$pn}'>每页显示{$pn}条</option>";
        $array = array(50, 80, 100, 150, 200, 250, 400);
        $pn = isset($_GET['pn']) && $_GET['pn'] ? (int) $_GET['pn'] : $pn;
        foreach ($array as $key => $val) {
            $sel = ($pn == $val) ? ' selected' : '';
            $str .= "<option value='{$val}'{$sel}>{$val}条</option>";
        }
        return "<select name='pn' class='form-control local-pselect'>{$str}</select>";
    }

    /**
     * 数组写入文件
     *
     * @author  shengwx <shengweixing@aiyishu.com> (2016-05-06)
     * @param  [type] $array 传入的数组
     * @param  string $name  文件名
     * @return [type]        [description]
     */
    protected function writeArray($array, $name)
    {
        //处理数组
        $array = var_export($array, true);
        $array = "<?php return {$array} ?>";
        $filepath = ROOT_PATH . '/temp/';
        //创建目录
        if (!is_dir($filepath)) {
            ecm_mkdir($filepath);
        }
        $filename = $filepath . "{$name}.php";
        $flag = file_put_contents($filename, $array);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * 更新文件缓存
     *
     * @author shengwx
     * @date 2016-5-10
     */
    protected function updateFileCache()
    {
        if (IS_POST) {
            $fileName = trim($_POST['fileName']);
            if (!$fileName) {
                $this->jsonReturn(message('确认要写入的文件名', false));
            }

            $query = array(
                'cond' => 'mark=1',
                'pri' => 'id',
                'order_by' => 'id desc',
            );
            $list = $this->mod->getData($query);
            if ($list) {
                foreach ($list as &$val) {
                    $val = $this->mod->getInfo($val['id']);
                }
                unset($val);
                $rs = $this->writeArray($list, $fileName);
                if ($rs) {
                    $this->jsonReturn(message());
                } else {
                    $this->jsonReturn(message('写入文件失败', false));
                }
            } else {
                $this->jsonReturn('更新失败,未获取到数据', false);
            }
        }
    }

    /**
     * 后台错误页面
     *
     * @author shengwx
     * @date(2016-10-16)
     */
    public function errorPage($msg)
    {
        if (IS_POST) {
            $this->jsonReturn($msg, false);
        }
        $this->assign('msg', $msg);

        $this->display('public/error.html');
        die;
    }
    
    
    public function __destruct() {
        parent::__destruct();
        if (m()->isTransStarted()) {
            m()->transBack();
        }
    }
}
