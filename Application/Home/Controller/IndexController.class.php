<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Home\Controller;
use Common\Api\AccountApi;
use Think\Controller;
use Shop\Api\FinAccountBalanceHisApi;
use Shop\Api\MemberConfigApi;
class IndexController extends Controller {

    public function invite_code(){
        $id_code = I('get.id_code','');

        $this->assign('id_code',$id_code);

        $result = apiCall(MemberConfigApi::GET_INFO,array(array('IDCode'=>$id_code)));

        if($result['status'] && is_array($result['info'])){
            $uid = $result['info']['uid'];
            $map = array(
                'invite_id'=>$uid,
            );

            $page = array('curpage'=>I('param.p',0),'size'=>10);
            $result = apiCall(AccountApi::QUERY,array($map,$page,'id desc'));

            if($result['status'] && is_array($result['info'])){
                $this->assign('list',$result['info']['list']);
                $this->assign('show',$result['info']['show']);
            }

        }

        $this->display();
    }

	/*
	 * 首页
	 * 登录地址\注册
	 * */
    public function index(){
        $this->display();
    }
	
	/*
	 * 注册
	 * TODO:短信注册
	 * */
	public function register(){
		if(IS_GET){
			$this->display();
		}
	}
	
	/*
	 * 登录
	 * TODO：第三方登录
	 * */
	public function login(){
		if(IS_GET){
			$this->display();
		}
	}
}