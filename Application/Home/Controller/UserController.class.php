<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/10/8
 * Time: 22:08
 */

namespace Home\Controller;


use Shop\Api\FinAccountBalanceHisApi;
use Shop\Api\MemberConfigApi;
use Uclient\Api\UserApi;
use Uclient\Model\UcenterMemberModel;
use Common\Api\AccountApi;
use Admin\Api\UidMgroupApi;
use Shop\Api\OrderRefundApi;
use Shop\Api\OrdersApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\OrdersItemApi;
use Shop\Api\OrdersExpressApi;


class UserController extends AppBaseController {



    public function _initialize(){
        parent::_initialize();
    }

	/**
	 * 用户提现界面
	 */
	 public function outmoney(){
       
//		$id=97;
		$user=apiCall(AccountApi::GET_INFO,array($this->uid));
//		dump($user);
		$this->assign('user',$user['info']);
        $this->theme($this->themeType)->display();
    }
	


    /**
     * 用户分享页面
     */
    public function share(){
      	$id=$this->uid;

		$user=apiCall(AccountApi::GET_INFO,array($id));
//		dump($user);
		$this->assign('user',$user['info']);
		$return=apiCall(UidMgroupApi::QUERY_WITH_UID,array($id));
		$this->assign('umember',$return['info'][0]);
//		dump($return);

        $this->theme($this->themeType)->display();
    }

    /**
     * 用户返佣页面
     */
    public function rebate(){

        //TODO: 查询用户相关信息
        $map=array('uid'=>$this->uid);
        $fg=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
        $this->assign('userconfig',$fg['info'][0]);
        $result=apiCall(FinAccountBalanceHisApi::QUERY_NO_PAGING,array($map));
        $this->assign('jilu',$result['info']['list']);


        $this->theme($this->themeType)->display();
    }

    /**
     * 用户订单页面
     */
    public function order(){
			$uid=$this->uid;
			$page = array('curpage'=>I('post.p',0),'size'=>50);
			$result1=apiCall(OrdersInfoViewApi::QUERY,array($map1,$page));
			$map2=array('uid'=>$uid,'order_status'=>3,'pay_status'=>1);
			$result2=apiCall(OrdersInfoViewApi::QUERY,array($map2,$page));
			$map3=array('uid'=>$uid,'pay_status'=>0,'order_status'=>2);
			$result3=apiCall(OrdersInfoViewApi::QUERY,array($map3,$page));
			$map4=array('uid'=>$uid,'order_status'=>9);
			$result4=apiCall(OrdersInfoViewApi::QUERY,array($map4,$page));
			$map5=array('uid'=>$uid,'pay_status'=>1,'order_status'=>4);
			$result5=apiCall(OrdersInfoViewApi::QUERY,array($map4,$page));
			$result=apiCall(OrdersItemApi::QUERY_NO_PAGING);
			$tui=apiCall(OrderRefundApi::QUERY_NO_PAGING);
			$this->assign('ths',$tui['info']);
			$this->assign('pro',$result['info']);
			$this->assign('orders',$result1['info']['list']);
			$this->assign('orders_sh',$result2['info']['list']);
			$this->assign('orders_fk',$result3['info']['list']);
			$this->assign('orders_qx',$result4['info']['list']);
			$this->assign('orders_qr',$result5['info']['list']);
			$result=apiCall(OrdersExpressApi::QUERY_NO_PAGING,array());
			$this->assign('express',$result['info']);
//			dump($result3);
			$this->theme($this->themeType)->display();
	
    }
	
	/**
	 * 物流
	 */
	public function express(){
		$this->theme($this->themeType)->display();
	}



    //===私有


}