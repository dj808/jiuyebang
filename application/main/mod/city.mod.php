<?php

class CityMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('city');
    }

    /**
     * 根据ID获取省市区
     */
    public function getFullCity($id)
    {
        $data = [];
        $info = $this->getInfo($id);
        if (3 == $info['level']) {
            $data = [
                'dist_id' => $info['id'],
                'dist_name' => $info['name'],
            ];
            $pre_info = $this->getFullCity($info['parent_id']);
            $data['city_id'] = $pre_info['city_id'];
            $data['city_name'] = $pre_info['city_name'];
            $data['prov_id'] = $pre_info['prov_id'];
            $data['prov_name'] = $pre_info['prov_name'];
        } elseif (2 == $info['level']) {
            $data = [
                'dist_id' => 0,
                'dist_name' => '-',
                'city_id' => $info['id'],
                'city_name' => $info['name'],
            ];
            $pre_info = $this->getFullCity($info['parent_id']);
            $data['prov_id'] = $pre_info['prov_id'];
            $data['prov_name'] = $pre_info['prov_name'];
        } elseif (1 == $info['level']) {
            $data = [
                'dist_id' => 0,
                'dist_name' => '-',
                'city_id' => 0,
                'city_name' => '-',
                'prov_id' => $info['id'],
                'prov_name' => $info['name'],
            ];
        }
        return $data;
    }

    /**
     * 获取下级区域
     */
    public function getSubCity($id = 1)
    {
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
	
	public function getChildList($parentId, $fields = '*') {
		$query = array(
			'cond' => "parent_id={$parentId} AND mark=1",
			'fields' => $fields,
			'order_by' => 'id ASC'
		);
		return $this->getData($query);
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
	
	
	
	
 
}
