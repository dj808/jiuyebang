<?php
/**
 * 远程模型基类
 * @author 刘小祥
 * @date 2015-11-27
 */
class RemoteMod
{
    public $modName, $cachePath;
    public function __construct($mod = '')
    {
        $this->modName = $mod;
        $this->cachePath = ROOT_PATH . "/temp/mod/{$mod}";
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

    }

    public function getData($query)
    {
        $name = $this->modName;
        return $this->getMethodData("getData", $query);
    }

    public function getOne($query)
    {
        return $this->getMethodData("getOne", $query);
    }

    public function getInfo($id)
    {
        $cacheFile = $this->cachePath . "/{$id}.php";
        if (is_file($cacheFile)) {
            $info = include $cacheFile;
            return $info;
        }
        if ($this->rowList) {
            $data = $this->rowList;
        } else {
            $data = $this->getAll();
        }
        $info = $data[$id];
        $content = var_export($info, true);
        $content = "<?php return {$content}; ?>";
        file_put_contents($cacheFile, $content);
        return $data[$id];
    }

    public function getMethodData($method)
    {
        $args = func_get_args();
        //代有@标识符 不采用 md5命名方式
        if (strpos($method, "@")) {
            list($mdstr, $method) = explode("@", $method);
        } else {
            $mdstr = md5(serialize($args));
        }
        $cacheFile = $this->cachePath . "/{$mdstr}.php";
        if (is_file($cacheFile)) {
            $data = include $cacheFile;
            return $data;
        }
        $postData['mod'] = $this->modName;
        $postData['method'] = $method;
        unset($args[0]);
        $args = array_values($args);
        $postData['param'] = $args;
        $data = RemoteMod::getInterfaceData("getMethodData", $postData);
        if (!$data) {
            return $data;
        }
        $content = var_export($data, true);
        $content = "<?php return {$content}; ?>";
        file_put_contents($cacheFile, $content);
        return $data;
    }

    public static function getInterfaceData($act, $postData)
    {
        $url = MANAGE_URL;
        RemoteMod::addTokenData($postData, $act);
        $browser = &ic("browser");
        false && $browser = new Browser();
        $url = "{$url}api.php?app=default&act={$act}";
        $result = $browser->visitByPost($url, $postData);
        $result = json_decode($result, true);
        if ($result && $result['success']) {
            return $result['data'];
        }
        return false;

    }

    public static function postArticeData($act, $articleData)
    {
        $url = CMS_URL;
        $postData['article'] = $articleData;
        RemoteMod::addTokenData($postData, $act);
        $browser = &ic("browser");
        false && $browser = new Browser();
        $url = "{$url}api.php?app=articleApi&act={$act}";
        //$result = $browser->visitByPost($url, $postData);
        $result = "";
        $result = json_decode($result, true);
        if (!$result) {
            $result = message("接口响应异常，请稍后再试！", false);
        }
        return $result;
    }

    public static function addTokenData(&$data, $act)
    {
        $time = time();
        $data['tm'] = $time;
        $data['token'] = sha1("jk2015" . md5($time . $act));

    }
}
