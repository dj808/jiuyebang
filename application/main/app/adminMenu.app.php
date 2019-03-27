<?php
	
	class AdminMenuApp extends BackendApp
	{
		public function index()
		{
			$this->display();
		}
		
		/**
		 * 列表
		 */
		public function ajax_list()
		{
			$search = I('');
			// 选中menu
			$menu_id_arr = [];
			if (!empty($search['role_id'])) {
				$admin_role_menu_mod = &m('adminRoleMenu');
				$query = [
					'fields' => 'menu_id',
					'cond' => 'mark=1 AND role_id=' . $search['role_id'],
				];
				$menu_id_data = $admin_role_menu_mod->getData($query);
				foreach ($menu_id_data as $key => $value) {
					$menu_id_arr[] = $value['menu_id'];
				}
			}
			// 所有menu
			$admin_menu_mod = &m('adminMenu');
			$query = [
				'cond' => 'mark=1',
				'order_by' => 'sort ASC',
			];
			$menu = $admin_menu_mod->getData($query);
			$menu_tree = [];
			foreach ($menu as $key => $value) {
				if (0 == $value['pid']) {
					$menu_tree[$value['id']] = $value;
				} else {
					$menu_tree[$value['pid']]['child'][] = $value;
				}
			}
			// 数据加工
			$level = 0;
			$pre_pid = 0;
			$list = [];

			foreach ($menu_tree as $top_memu) {
				$top_memu['add_time'] = $top_memu['add_time'] ? date('Y-m-d H:i:s', $top_memu['add_time']) : '';
				$top_memu['name_prefix'] = '';
				$top_memu['url'] = $top_memu['controller'] ? ('/' . $top_memu['controller'] . '/' . ($top_memu['action'] ? $top_memu['action'] : 'index')) : '';
				$top_memu['LAY_CHECKED'] = in_array($top_memu['id'], $menu_id_arr) ? true : false;
				$list[] = $top_memu;
				if (isset($top_memu['child'])) {
					foreach ($top_memu['child'] as $child_menu) {
						$child_menu['add_time'] = $child_menu['add_time'] ? date('Y-m-d H:i:s', $child_menu['add_time']) : '';
						$child_menu['name_prefix'] = '|—— ';
						$child_menu['url'] = $child_menu['controller'] ? ('/' . $child_menu['controller'] . '/' . ($child_menu['action'] ? $child_menu['action'] : 'index')) : '';
						$child_menu['LAY_CHECKED'] = in_array($child_menu['id'], $menu_id_arr) ? true : false;
						$list[] = $child_menu;
					}
				}
			}
			$res = [
				'code' => 0,
				'status' => true,
				'msg' => '',
				'count' => count($list),
				'data' => $list,
			];
			$this->ajaxReturn($res);
		}
		
		/**
		 * 添加/修改
		 */
		public function edit()
		{
			$params = I('');
			$mod = &m('adminMenu');
			if (IS_POST) {
				$data = [
					'pid' => $params['pid'],
					'name' => $params['name'],
					'class' => $params['class'],
					'controller' => $params['controller'],
					'action' => $params['action'],
				];
				if ($params['id']) {
					$res = $mod->edit($data, $params['id']);
				} else {
					$res = $mod->edit($data);
				}
				if (!$res) {
					$this->ajaxReturn(R('9993'));
				}
				$this->ajaxReturn(R('9901', true));
			}
			$action_list = [];
			$info = [];
			if ($params['id']) {
				$info = $mod->getInfo($params['id']);
				if ($info['controller']) {
					$action_list = $this->get_action($info['controller']);
				};
			}
			$query = [
				'cond' => 'mark=1 AND pid=0',
				'order_by' => 'sort ASC',
			];
			$top_menu = $mod->getData($query);
			$controller_list = $this->get_controller();
			$this->assign('top_menu', $top_menu);
			$this->assign('controller_list', $controller_list);
			$this->assign('action_list', $action_list);
			$this->assign('info', $info);
			$this->display();
		}
	}
