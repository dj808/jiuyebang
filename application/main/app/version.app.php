<?php

class versionApp extends BackendApp
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

        $query = [
            'cond' => 'mark=1',
            'order_by' => 'id DESC',
        ];
        $app_version_mod = &m('version');
        $data = $app_version_mod->findWithPager($query, I('page', 1), I('limit', 10));

        foreach ($data['data'] as $key => &$value) {
            $value['type_name'] = 1 == $value['type'] ? 'IOS' : (2 == $value['type'] ? 'Android' : '');
            $value['is_force'] = 1 == $value['is_force_name'] ? '强制' : (2 == $value['is_force_name'] ? '非强制' : '');
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
        $mod = &m('version');
        if (IS_POST) {
            $data = [
                'type' => $params['type'],
                'version_num' => $params['version_num'],
                'update_version' => $params['update_version'],
                'version_type' => 1,
                'download' => $params['download'],
                'is_force' => $params['is_force'],
                'intro' => $params['intro'],
                'is_update' => $params['is_update'],
                'time_interval' => $params['time_interval'],
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
        $info = [];
        if ($params['id']) {
            $info = $mod->getInfo($params['id']);
        }
        $this->assign('info', $info);
        $this->display();
    }
}
