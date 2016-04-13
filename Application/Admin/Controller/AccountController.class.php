<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Controller;

use Admin\Api\AuthGroupAccessApi;
use Admin\Api\MemberApi;
use Admin\Model\MemberModel;
use Common\Api\AccountApi;
use Uclient\Api\UserApi;
use Shop\Api\MemberConfigApi;
use Uclient\Model\OAuth2TypeModel;
use Uclient\Model\UcenterMemberModel;

class AccountController extends AdminController {

	public function index() {
		$params = array();
        $idcode =  I('idcode', '', 'trim');
		$map['idcode'] = array('like', $idcode . "%");

		$page = array('curpage' => I('get.p'), 'size' => 10 );
		$order = " id desc ";
		$params['idcode'] = $idcode;
		$result = apiCall(AccountApi::QUERY, array($map, $page, $order));
		
		if ($result['status']) {
			$this->assign("idcode",$idcode);
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $result['info']['list']);
			$this -> display();
		} else {
			$this -> error($result['info']);
		}
	}
	

	/**
	 * 删除用户
	 * 假删除
	 */
	public function delete(){
		if(is_administrator(I('uid',0))){
			$this->error("禁止对超级管理员进行删除操作！");
		}
		parent::pretendDelete("uid");
	}
	/**
	 * 启用
	 */
	public function enable(){
		parent::enable("uid");
	}
	/**
	 * 禁用
	 */
	public function disable(){
		if(is_administrator(I('uid',0))){
			$this->error("禁止对超级管理员进行禁用操作！");
		}
		parent::disable("uid");
	}

    public function edit(){

        $id = I('get.id',0);
        if(IS_POST){
            $idcode = I('post.idcode','');

            $result = apiCall(MemberConfigApi::GET_INFO,array(array('IDCode'=>$idcode)));

            if($result['status'] && is_array($result['info'])){
                $this->error('该邀请码已存在!');
            }

            $entity = array(
                'IDCode'=>$idcode,
            );

            /* 调用注册接口注册用户 */
            $result = apiCall(MemberConfigApi::SAVE_BY_ID, array($id, $entity));

            if(!$result['status']){
                $this->error('信息更新失败！');
            } else {
                $this->success('信息更新成功！');
            }

        }else{

            $result = apiCall(MemberConfigApi::GET_INFO,array(array('uid'=>$id)));

            $this->assign("vo",$result['info']);

            $this->display();
        }
    }

    public function view(){

        $id = I('get.id',0);

        $result = apiCall(AccountApi::GET_INFO,array($id));

        $this->assign("userinfo",$result['info']);
        $this->display();
    }

	/**
	 * add 
	 */
    public function add(){
		if(IS_POST){

            $username = I('post.mobile','');
            $password = I('post.password','');
            $re_password = I('post.re_password','');
            $idcode = I('post.idcode','');


			if($password != $re_password){
				$this->error("密码和重复密码不一致！");
			}

            $entity = array(
                'username'=>'mobile_'.$username,
                'nickname'=>'mobile_'.$username,
                'password'=>$password,
                'mobile'=>$username,
                'realname'=>'',
                'idcode'=>$idcode,
                'add_uid'=>UID,
                'type'=>UcenterMemberModel::ACCOUNT_TYPE_MOBILE,
                'from'=>OAuth2TypeModel::SELF,
            );

            $result = apiCall(MemberConfigApi::GET_INFO,array(array('IDCode'=>$idcode)));

            if($result['status'] && is_array($result['info'])){
                $this->error('该邀请码已存在!');
            }


			/* 调用注册接口注册用户 */			
			$result = apiCall(AccountApi::REGISTER, array($entity));

            if(!$result['status']){
                if(!empty($result['info'])){
                    $this->error($result['info']);
                }else{
                $this->error('用户添加失败！');
                }
            } else {
                    $this->success('用户添加成功！');
            }

		}else{
			$this->display();
		}
	}
	
	/**
	 * 检测用户名是否已存在
	 */
	public function check_username($username){
		$result = apiCall(UserApi::CHECK_USER_NAME,array($username));
		if($result['status']){
			echo "true";
		}else{
			echo "false";
		}
	}
	
	 /**
	 * 检测用户名是否已存在
	 */
	public function check_email(){
			$result = apiCall(UserApi::CHECK_EMAIL,array($email));
		if($result['status']){
			echo "true";
		}else{
			echo "false";
		}
	}
	
	/**
	 * 
	 */
	public function select(){
			
		$map['nickname'] = array('like', "%" . I('q', '', 'trim') . "%");		
		$map['uid'] = I('q',-1);
		$map['_logic'] = 'OR';
		$page = array('curpage'=>0,'size'=>20);
		$order = " last_login_time desc ";
		
		$result = apiCall(MemberApi::QUERY, array($map,$page, $order,false,'uid,nickname,head'));
		
		if($result['status']){
			$list = $result['info']['list'];
			
			foreach($list as $key=>$g){
                $list[$key]['id']=$list[$key]['uid'];
                $list[$key]['head']= getImageUrl($list[$key]['head']);
			}
			
			$this->success($list);
		}	
	
	}




    public function updatepassword(){
        $this->display();
    }

    /**
     * 提交更新密码
     */
    public function submitpassword(){
        if(IS_GET){
            return ;
        }
        //获取参数
        $password   =   I('post.old');

        empty($password) && $this->error(L('ERR_NEED_OLD_PASSWORD'));
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error(L('ERR_NEED_NEW_PASSWORD'));
        $repassword = I('post.repassword');
        empty($repassword) && $this->error(L('ERR_NEED_CONFIRM_PASSWORD'));

        if($data['password'] !== $repassword){
            $this->error(L('ERR_NEED_SAME_PASSWORD'));
        }
        $new_password = $data['password'];

        $res    = apiCall(UserApi::UPDATEPWD,array(UID, $new_password));

        if($res['status']){
            $this->success(L('RESULT_SUCCESS'));
        }else{
            $this->error($res['info']);
        }
    }

}
