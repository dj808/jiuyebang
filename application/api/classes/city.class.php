<?php 
/**
 * 城市管理 - 逻辑类
 * @author 刘小祥
 * @date 2016年3月8日
 */ 
class City extends Zeus {
    public $mod;
    public function __construct() {
        $this->mod = &m("city");
        false && $this->mod = new CityMod();
    }
    
   public function edit($post) {
      $id = (int) $post['id'];
      // $existId = $this->mod->dataExist($param, $id);


      $data = $this->filterData($post);

      $existId = $this->mod->getRowByField("name", $data['name'], $id);

      if ($existId) return message("该城市名称已存在", false);
      
      $cityId = $this->mod->edit($data, $id);
      if (!$cityId) return message("操作失败", false);
      return message();
   }
   
   /**
    * 编辑开发城市
    * @author 刘小祥
    * @date 2016年3月8日
    */ 
   public function editOpenCity($list) { 
       $openCityMod = &m("openCity");
       foreach ($list as $pid=>$cityIds) {
           $openCityMod->editCityIds($cityIds, $pid);
       }
       return message();
   }
   
   public function getCityList( $limit='' ) {
       $query = array();
       $cityList = array();
       
       $query['cond'] = "parent_id=1 AND mark=1";
       $query['field'] = "id";
       $query['limit'] = $limit ? $limit : '';
       $query['order_by'] = "id asc";
       $data = $this->mod->getData($query);
       $i=0;
       foreach ($data as $key => $pCity) {
           if ($pCity['id'] == 821) {
               $cityList[0]['city_id'] = $pCity['id'];
               $cityList[0]['city_name'] = $pCity['name'];
               $cityList[0]['city_list'] = $this->mod->getChildList($pCity['id']);
           } else {
               $cityList[$key+1]['city_id'] = $pCity['id'];
               $cityList[$key+1]['city_name'] = $pCity['name'];
               if($pCity['id'] == 2 || $pCity['id'] == 20 || $pCity['id'] == 802 || $pCity['id'] == 2324) {
                   $cCityInfo = $this->mod->getOne(array('cond'=> "parent_id = {$pCity['id']}"));
                   $cityList[$key+1]['city_list'] = $this->mod->getChildList($cCityInfo['id']);
               } else {
                   $cityList[$key+1]['city_list'] = $this->mod->getChildList($pCity['id']);
               }
           }
       }
       $cityList[] = kSort($cityList);
       unset($cityList['35']);
       return $cityList;
   }
}

?>