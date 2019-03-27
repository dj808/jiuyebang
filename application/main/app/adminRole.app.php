<?php
	
	class AdminRoleApp extends BackendApp
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
			
			$admin_role_mod = &m('adminRole');
			$query = [
				'cond' => 'mark=1',
				// 'order_by' => 'sort ASC',
			];
			
			if($this->uid !=1)
				$query['cond'] =  'id <> 1 AND mark=1';
			
			$data = $admin_role_mod->findWithPager($query, I('page', 1), I('limit', 10));
			
			foreach ($data['data'] as $key => &$value) {
				$value['add_time'] = $value['add_time'] ? date('Y-m-d H:i:s', $value['add_time']) : '';
			}
			$this->ajaxReturn($data);
		}
		
		/**
		 * 添加/修改
		 */
		public function edit()
		{
			$params = I('');
			$mod = &m('adminRole');
			if (IS_POST) {
				$data = [
					'name' => $params['name'],
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
			if ($params['id']) {
				$info = $mod->getInfo($params['id']);
				$this->assign('info', $info);
			}
			$this->display();
		}
		
		/**
		 * 菜单
		 */
		public function layer_menu()
		{
			$mod = &m('adminRoleMenu');
			if (IS_POST) {
				$menu_id_new = explode(',', I('menu_ids'));
				$query = [
					'fields' => 'menu_id',
					'cond' => 'mark=1 AND role_id=' . I('role_id'),
					// 'order_by' => 'sort ASC',
				];
				$menu_id_old_data = $mod->getData($query);
				$menu_id_old = [];
				foreach ($menu_id_old_data as $key => $value) {
					$menu_id_old[] = $value['menu_id'];
				}
				// 新去除的菜单
				$del_data = array_diff($menu_id_old, $menu_id_new);
				foreach ($del_data as $item) {
					$data = [
						'upd_user' => $this->uid,
						'upd_time' => time(),
						'mark' => -1,
					];
					$query = [
						'cond' => 'mark=1 AND role_id=' . I('role_id') . ' AND menu_id=' . $item,
						'set' => $data,
					];
					$res = $mod->doUpdate($query);
					if (!$res) {
						$this->ajaxReturn(R('9993'));
					}
				}
				// 新增加的菜单
				$add_data = array_diff($menu_id_new, $menu_id_old);
				foreach ($add_data as $item) {
					$data = [
						'role_id' => I('role_id'),
						'menu_id' => $item,
						'add_user' => $this->uid,
						'add_time' => time(),
						'upd_user' => $this->uid,
						'upd_time' => time(),
					];
					$res = $mod->edit($data);
					if (!$res) {
						$this->ajaxReturn(R('9993'));
					}
				}
				$this->ajaxReturn(R('9900', true));
			}
			$this->assign('role_id', I('role_id'));
			$this->display();
		}
		
		/**
		 * 权限
		 */
		public function layer_access()
		{
			
			$mod = $this->admin_role_access_mod;
			$query = [
				'cond' => 'mark=1 AND role_id=' . I('role_id'),
			];
			$access_old_data = $mod->getData($query);
			$access_old = [];
			foreach ($access_old_data as $key => $value) {
				$access_old[] = $value['controller'] . '/' . $value['action'];
			}
			if (IS_POST) {
				$access_new = $_REQUEST['access'];
				
				
				// 新去除的权限
				$del_data = array_diff($access_old, $access_new);
				foreach ($del_data as $item) {
					list($controller, $action) = explode('/', $item);
					$data = [
						'upd_user' => $this->uid,
						'upd_time' => time(),
						'mark' => -1,
					];
					$query = [
						'cond' => 'mark=1 AND role_id=' . I('role_id') . ' AND controller=\'' . $controller . '\' AND action=\'' . $action . '\'',
						'set' => $data,
					];
					$res = $mod->doUpdate($query);
					if (!$res) {
						$this->ajaxReturn(R('9993'));
					}
				}
				// 新增加的权限
				$add_data = array_diff($access_new, $access_old);
				foreach ($add_data as $item) {
					list($controller, $action) = explode('/', $item);
					$data = [
						'role_id' => I('role_id'),
						'controller' => $controller,
						'action' => $action,
						'add_user' => $this->uid,
						'add_time' => time(),
						'upd_user' => $this->uid,
						'upd_time' => time(),
					];
					$res = $mod->edit($data);
					if (!$res) {
						$this->ajaxReturn(R('9993'));
					}
				}
				$this->ajaxReturn(R('9900', true));
			}
			$controller = $this->get_controller();
			$data = [];
			foreach ($controller as $item) {
				$data[$item] = $this->get_action($item);
			}
			$this->assign('data', $data);
			$this->assign('access_list', $access_old);
			$this->assign('role_id', I('role_id'));
			$this->display();
		}
	}
