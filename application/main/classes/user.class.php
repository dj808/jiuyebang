<?php
	
	/**
	 * 用户相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class User extends Zeus {
		public $mod;
		
		public function __construct () {
			$this->mod = m('user');
			false && $this->mod = new UserMod();
			
		}

		public function edit ( $param ) {
			$id = (int)$param['id'];
			
			$info = $this->mod->getInfo($id);
			
			$type = (int)$param['type'];
			if ( !$type )
				return R('请选择帐号类型' , false);
			
			
			if ( $param['idcard_no'] ) {
				$idcard_no = $this->isValidIdNo($param['idcard_no']);
				if ( !$idcard_no )
					return R("请输入正确格式的身份证号码" , false);
			}
			$param['is_seed'] = $param['is_seed'] == 'on' ? '1' : '2';
			
			//判断是否修改了审核状态
			$realStatus =  $param['real_status'];
			if($info['real_status'] != $realStatus){
				$title = '审核进度通知';
				if($realStatus ==1){    //如果是审核未通过
					if(!$param['res_status'])
						return R("请输入审核未通过的原因" , false);
					
					$msg = ' 很遗憾，您的实名申请未通过审核，未通过原因为：'.$param['res_status'];
				}else{//如果是审核通过
					$msg = ' 恭喜，您的实名申请已通过审核！';
				}
			}
			
			$rs = $this->mod->edit($param , $id);
			if ( !$rs ) {
				return R("添加失败" , false);
			}

			//判断是否修改了审核状态
			if($info['real_status'] != $realStatus){
				Zeus::sendMsg([
					'type' =>['msg','push'],
					'user_id' =>$id,
					'title' =>$title,
					'content' =>$msg,
					'msg_type' =>1,
					'user_type' =>1,
				]);
			}
			
			return R('操作成功' , true);
			
		}
		
		
	}