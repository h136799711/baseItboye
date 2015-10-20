<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;
use Shop\Api\FinAccountBalanceHisApi;
use Shop\Api\MemberConfigApi;
class IndexController extends Controller {
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