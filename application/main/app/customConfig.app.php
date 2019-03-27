<?php
/**
 * @todo 基础配置 管理
 * @author Malcolm (2017年9月25日)
 */
class CustomConfigApp extends BackendApp {
    
    public $mod,$ic;
    public function __construct() {
        parent::__construct();
    
        $this->mod = m('config');
        false && $this->configMod = new ConfigMod();
    
        $this->ic = ic('config');
        false && $this->configIc = new Config();
    }
    
    public function index() {
        if (IS_POST) {
            $result = $this->ic->editContent($_POST);
            $this->ajaxReturn($result);
        }

        $configGroupMod = m("configGroup");
        $tabList = $configGroupMod->getData([
            'cond'=>"mark=1",
            'order_by'=>"sort ASC"
        ]);

        $configGroupId = $_REQUEST['config_group_id'];
        if (!$configGroupId) {
            $configGroupId = $tabList[0]['id'];
        } 
        $list = $this->mod->getData([
            'cond'=>"config_group_id={$configGroupId}  AND mark = 1",
            'order_by'=>"sort ASC"
        ]);
        foreach ($list as &$row) {
            $row = $this->mod->getInfo($row['id']);
            if ($row['type']==4) { 
                $format = $row['format'];
                list($firstFormat, $secondFormat)=explode("=>", $format);
                preg_match("/([a-z]+)(\[(.+?)\])?/", $firstFormat, $firstMatch);
                $firstType = $firstMatch[1];
                $firstKeys = $firstMatch[3]; 
                preg_match("/([a-z]+)(\[(.+?)\])?/", $secondFormat, $secondMatch);
                $itemList = $row['content']; 
                $secondType = $secondMatch[1];
                $secondKeys = $secondMatch[3];
                $data = [];
                if ($firstType=="id") {
                    $maxId = $itemList ? count($itemList) : 1;
                    $row['auto'] = true;
                    $firstKeys = range(1, $maxId);
                    
                } else {
                    $firstKeys = str_replace(" ", "", $firstKeys);
                    $firstKeys = explode(",", $firstKeys);
                } 
                $secondKeys = $secondKeys ? explode(",", $secondKeys) : [];
                foreach ($firstKeys as $k=>$firstKey) {
                    $firstName = $firstKey; 
                    $firstField = $firstKey; 
                    $tips = "";
                    if (strpos($firstKey, "^")) {
                        list($firstField, $fieldType, $firstName, $tips) = explode("^", $firstKey);
                    } 
                    $firstData =  [
                        'name'=>$firstName,
                        'id'=>$firstField,
                    ];
                    if (!$secondKeys) {
                        $firstKey = $firstField;
                        $firstData['list'][0] = [ 
                            'name'=>"",
                            'input'=>"[{$row['id']}][".$firstKey."]",
                            "input_tpl"=>"[{$row['id']}][AUTO_ID]",
                            'content'=>$itemList[$firstKey],
                            'tips'=>$tips
                        ];
                    } else {
                        foreach ($secondKeys as $secondKey) {
                            list($secondField, $fieldType, $secondName) = explode("^", $secondKey);
                            $firstData['list'][] = [
                                'name'=>$secondName,
                                'input'=>"[{$row['id']}][".$firstKey."][".$secondField."]",
                                "input_tpl"=>"[{$row['id']}][AUTO_ID][".$secondField."]",
                                'content'=>$itemList[$firstKey][$secondField]
                            ];
                        }
                    }
                    $row['list'][] = $firstData;
                }
                
            } 
        }
         //"index=>assoc[bank_name^text^银行名称, account_name^text^户主, bank_no^text^卡号]";
       
        
        unset($row);
        $this->assign("tabList", $tabList);
        $this->assign("list", $list);
        $this->assign("configGroupId", $configGroupId);
        $this->assign("req", $_REQUEST); 
        $this->display();
    }
    
    
    
}