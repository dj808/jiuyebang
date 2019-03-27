<?php
/**
 * @todo 城市模型
 * @author 刘小祥 (2017年9月11日)
 */

class CityMod extends  CBaseMod{

    public function __construct() {
        parent::__construct("city");
    }
    
    /**
     * 基本信息
     * (non-PHPdoc)
     * @see CBaseMod::getInfo()
     */
    public function getInfo($id) {
        $info = parent::getInfo($id);
        return $info ? $info : array();
    }

   
    
    /** 
     * @todo 获取城市名称
     * @author 刘小祥 (2017年9月11日)
     */
    public function getCityName($id, $delimiter="", $isReplace=false) {
      do {
          $info = $this->getInfo($id);
          if ($isReplace){
          	$names[] = str_replace(array("省","市","维吾尔","壮族","回族","自治区"), "", $info['name']);
          } else {
          	$names[] = $info['name'];
          }
          $id = $info['parent_id'];
      } while($id>1);
      $names = array_reverse($names);
      if (strpos($names[1], $names[0])===0) {
          unset($names[0]);
      }
      return implode($delimiter, $names);
    }
	
	
	/**
	 * @todo    获取城市名称（可定制深度）
	 * @author Malcolm  (2018年03月07日)
	 */
    public function getCityNameByDepth($id,$depyh=3,$delimiter="", $isReplace=false){
    	if($depyh==3){
    		
    		return $this->getCityName($id,$delimiter,$isReplace);
	    
	    }else{
    		$info = $this->getInfo($id);
		
		    if($isReplace)
			    $name = str_replace(array("省","市","维吾尔","壮族","回族","自治区"), "", $info['name']);
		    else
			    $name = $info['name'];
		    
		    
		    if(1==$depyh)
		    	return $name;
		    
		    
		    $parentInfo = $this->getInfo($info['parent_id']);
		    if($isReplace)
			    $parentName = str_replace(array("省","市","维吾尔","壮族","回族","自治区"), "", $parentInfo['name']);
		    else
			    $parentName = $parentInfo['name'];
		
		    $cityName = [$parentName,$name];
		    if (strpos($cityName[1], $cityName[0])===0) {
			    unset($cityName[0]);
		    }
		    return implode($delimiter, $cityName);
	    }
    	
    	
    }
    
	
	/**
	 * @todo    根据名称获取ID
	 * @author Malcolm  (2018年01月05日)
	 */
    public function getIdByName($name,$level=0,$pid=0){
    	$cond[] = " name LIKE '%{$name}%'   ";
    	
    	if($level)
		    $cond[] =" level = {$level} ";
    	
    	if($pid)
		    $cond[] =" parent_id = {$pid} ";
    	
    	$cond[] = 'mark = 1';
	
	    $query = array(
		    'fields' => 'id',
		    'cond' =>$cond,
		    'order_by' => 'id DESC',
	    );
	
	    $info = $this->getOne($query);
	
	    return $info['id']?$info['id']:0;
    }
	
	
	/**
	 * @todo    根据坐标获取数据库对应ID
	 * @author Malcolm  (2018年01月05日)
	 */
	public function getAddressByGps($lng,$lat){
		$lat = trim($lat);
		$lng = trim($lng);
		
		$gps = $lng.','.$lat;
		
		import('/gaode/service.lib');
		$gaoDe = new Service();
		$rs = $gaoDe->getAddressByGps($gps);
		
		if($rs['status']!= 1)
			return false;
		
		
		$rs = $rs['regeocode'];
		
		
		$province = $this->getIdByName(str_replace('省','',trim($rs['addressComponent']['province'])),1);
		$city = $this->getIdByName(str_replace('市','',trim($rs['addressComponent']['city'])),2,$province);
		$district = $this->getIdByName(str_replace('区','',trim($rs['addressComponent']['district'])),3,$city);
		
		
		return [
			'province_id' =>$province,
			'city_id' =>$city,
			'area_id' =>$district,
		];
	}
	
	
	/**
	 * @todo    获取下级区域
	 * @author Malcolm  (2018年04月11日)
	 */
	public function getSubCity($id = 1) {
		$query = [
			'fields' => 'id,name',
			'cond' => 'mark=1 AND parent_id=' . $id,
			'order_by' => 'sort ASC',
		];
		$data = $this->getData($query);
		$list = [];
		foreach ($data as $key => $value) {
			$list[$value['id']] = $value['name'];
		}
		return $list;
	}
	
    
}