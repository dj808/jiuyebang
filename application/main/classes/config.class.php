<?php
	
	/**
	 * 配置相关控制器
	 * Created by Malcolm.
	 * Date: 2018/2/19  22:07
	 */
	class Config extends Zeus {
		public $mod;
		
		public function __construct() {
			$this->mod = m('config');
			false && $this->mod = new ConfigMod();
		}
		
		
		public function edit($param, $id) {
			$type = (int) $param['type'];
			if (!$type) {
				return R('请选择类型' , false);
			}
			$name = trim($param['name']);
			if (!$name) {
				return R("请输入名称", false);
			}
			$tag = trim($param['tag']);
			if (!$tag) {
				return R("请输入开发标签", false);
			}
			$sort = intval($param['sort']);
			$sort = $sort ? $sort : 99;
			$data = [
				'name'=>$name,
				'type'=>$type,
				'tag'=>$tag,
				'sort'=>$sort,
				'format'=>$param['format']
			];
			
			
			$data['config_group_id'] = (int) $param['config_group_id'];
			$rs = $this->mod->edit($data, $id);
			if (!$rs) {
				return R("添加失败", false);
			}
			return R('操作成功' , true);
			
		}
		
		
		
		/**
		 * @todo 编辑
		 * @author Malcolm (2017年9月25日)
		 */
		public function editContent($param) {
			$dataList = $param['data'];
			foreach ($dataList as $id=>$data) {
				$content = serialize($data);
				$rs = $this->mod->setFieldValue("content", $content, $id);
				if (!$rs) {
					return R("操作失败", false);
				}
			}
			return R('操作成功' , true);
		}
		
	}