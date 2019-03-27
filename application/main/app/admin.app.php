<?php

class AdminApp extends BackendApp
{
	public function __construct() {
		parent::__construct();
		
	}
	
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

        if($this->uid != 1){
            $query = [
                'cond' => 'mark=1 AND id!=1',
                // 'order_by' => 'id DESC',
            ];
        }else{
            $query = [
                'cond' => 'mark=1',
                // 'order_by' => 'id DESC',
            ];
        }

        if (!empty($search['unick'])) {
            $query['cond'] .= ' AND unick LIKE \'%' . $search['unick'] . '%\'';
        }
        if (!empty($search['mobile'])) {
            $query['cond'] .= ' AND mobile LIKE \'%' . $search['mobile'] . '%\'';
        }
        $admin_mod = &m('admin');
        false&&$admin_mod = new AdminMod();
        $data = $admin_mod->findWithPager($query, I('page', 1), I('limit', 10));

        $admin_role_mod = &m('adminRole');
        foreach ($data['data'] as $key => &$value) {
            $value['last_login_time'] = $value['last_login_time'] ? date('Y-m-d H:i:s', $value['last_login_time']) : '';
            $role_info = $admin_role_mod->getInfo($value['role_id'] ? $value['role_id'] : 0);
            $value['role_name'] = $role_info['name'] ? $role_info['name'] : '';
        }
        $this->ajaxReturn($data);
    }

    /**
     * 添加/修改
     */
    public function edit()
    {
        $params = I('');
        $mod = &m('admin');
        if (IS_POST) {
            $data = [
                'username' => $params['username'],
                'unick' => $params['unick'],
                'mobile' => $params['mobile'],
                'role_id' => $params['role_id'],
            ];
            if ($params['id']) {
                if ($params['password']) {
                    $data['password'] = md5($params['password']);
                }
                $res = $mod->edit($data, $params['id']);
            } else {
                $data['password'] = md5($params['password']);
                $res = $mod->edit($data);
            }
            if (!$res) {
                $this->ajaxReturn(R('9993'));
            }
            $this->ajaxReturn(R('9901', true));
        }
        $info = [];
        if (!empty($params['id'])) {
            $info = $mod->getInfo($params['id']);
        }
        $admin_role_mod = &m('adminRole');
        $query = [
            'cond' => 'mark=1',
        ];
        $role_list = $admin_role_mod->getData($query);
        $this->assign('role_list', $role_list);
        $this->assign('info', $info);
        $this->display();
    }
}
