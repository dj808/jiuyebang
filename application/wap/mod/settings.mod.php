<?php
	
	/**
	 * Created by Malcolm.
	 * Date: 2017/12/8  20:44
	 */
	class SettingsMod extends CBaseMod {
		public function __construct() {
			parent::__construct("settings");
		}
		
		
		public function getSettings($id) {
			$info  = $this->getInfo($id);
			$style_list = unserialize($info['content']);
			$style_list = $style_list ? $style_list : array();
			foreach ($style_list as $row) {
				$list[$row['id']] = $row['name'];
			}
			return $list;
			
		}
		
	}