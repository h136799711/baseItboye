<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

use Common\Api\AccountApi;
use Uclient\Model\OAuth2TypeModel;
use Shop\Api\CategoryApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\OrdersExpressApi;
use Shop\Api\ProductApi;
use Shop\Api\AddressApi;
use Tool\Api\ProvinceApi;
use Tool\Api\CityApi;
use Tool\Api\AreaApi;
use Shop\Api\OrdersApi;
use Shop\Api\OrdersItemApi;
use Admin\Api\MemberApi;
use Admin\Api\SecurityCodeApi;
use Uclient\Api\UserApi;
use Shop\Api\FinAccountBalanceHisApi;
use Shop\Api\MemberConfigApi;
use Shop\Api\OrderRefundApi;
use Admin\Api\UidMgroupApi;
use Shop\Api\StoreApi;


class UserController extends ShopController{
	/**
	 * 
	 * 访问前判断是否登录
	 */
	
	public function _initialize(){
        parent::_initialize();
        $user=session('user');
        if($user==null){
              $this->error("请重新登录!",U('Shop/Index/login'));
        }
    }
	
	/**
	 * 个人信息
	 */	
	public function info(){
		$user=session('user');
		$this->assign('user',$user);
		$this->assign('phone',substr_replace($user['mobile'],'****', 3, 4));
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$this->assign('group',$resultw['info']);
		$this->theme($this->themeType)->display();
	}
	/*
	 * 获取市
	 * */
	public function getcity(){
		$sno=I('sno',0);
		if($sno!=0){
			$map=array('father'=>$sno);
			$result=apiCall(CityApi::QUERY_NO_PAGING,array($map));
			$this->ajaxReturn($result['info'],'json');
		}
	}	
	
	/*
	 * 分佣管理
	 * */
	public function distribution(){
		$user=session('user');
		$this->assign('user',$user);
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$map=array('uid'=>$user['id'],'dtree_type'=>4);
		$fg=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
		$this->assign('userconfig',$fg['info'][0]);
		$page = array('curpage'=>I('post.p',0),'size'=>10);
		$result=apiCall(FinAccountBalanceHisApi::QUERY,array($map,$page));
		$this->assign('jilu',$result['info']['list']);
		$this->assign('show',$result['info']['show']);
		$pro=apiCall(ProvinceApi::QUERY_NO_PAGING,array());
		$this->assign('pro',$pro['info']);
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$this->assign('group',$resultw['info']);
		$this->theme($this->themeType)->display();
	}
//	
	/*
	 * 实名认证
	 * */
	public function truename(){
		$user=session('user');
		$this->assign('user',$user);
		$this->assign('phone',substr_replace($user['mobile'],'****', 3, 4));
		$this->assign('realname',substr_replace($user['idnumber'],'********', 3, 8));
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		if(IS_GET){
			$id=array('uid'=>$user['id']);
			$results=apiCall(MemberConfigApi::QUERY_NO_PAGING, array($id));
			$this->assign('cfg',$results['info'][0]);
			$this->theme($this->themeType)->display();
		}else{
			$id=array('uid'=>$user['id']);
			$entity=array('realname'=>I('realname',''),'idnumber'=>I('idnum',''));
			$en=array('identity_validate'=>2);
			$result=apiCall(MemberApi::SAVE, array($id,$entity));
			$results=apiCall(MemberConfigApi::SAVE, array($id,$en));
			
			if($result['status'] && $results['status'] ){
				$this->success('正在提交审核!');
			}else{
				$this->error('出错了!,请重新填写!');
			}
		}
		
	}
	/*
	 * 用户头像上传
	 * */
	public function uploadheadimg(){
		$user=session('user');
		$id=array('uid'=>$user['id']);
		$entity=array('head'=>I('picurl',''));
		$result=apiCall(MemberApi::SAVE, array($id,$entity));
		if($result['status']){
			$this->success('用户头像修改成功');
		}else{
			$this->error('用户头像修改失败');
		}
	}
	/*
	 * 获取区
	 * */
	public function getarea(){
		$cno=I('cno',0);
		
			$map=array('father'=>$cno);
			$result1=apiCall(AreaApi::QUERY_NO_PAGING,array($map));
			$this->ajaxReturn($result1['info'],'json');
	}	
	/**
	 * 收货地址
	 */	
	public function address(){
		$user=session('user');
		$this->assign('user',$user);
		if(IS_GET){
			$id=I('id',0);
			$user=session('user');
			if($user!=null){
				$uid=array('uid'=>$user['id']);
				$map=array('id'=>$id);
				$pro=apiCall(ProvinceApi::QUERY_NO_PAGING,array());
				$map=array('id'=>140);
				$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
				$this->assign('group',$resultw['info']);
				$map=array('uid'=>$user['id']);
				$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
				$this->assign('userinfo',$result['info'][0]);
	//			dump($pro);
				$this->assign('pro',$pro['info']);
				if($id!=0){
					$result=apiCall(AddressApi::QUERY,array($map));
					$this->assign('add',$result['info']['list'][0]);
					$result=apiCall(AddressApi::QUERY,array($uid));
		//			dump($result);
					$this->assign('address',$result['info']['list']);
					
					$this->theme($this->themeType)->display();
				}else{
					$result=apiCall(AddressApi::QUERY_NO_PAGING,array($uid));
		//			dump($result);
					$this->assign('address',$result['info']);
					$this->theme($this->themeType)->display();
				}
			}else{
				$this->error('获取用户信息失败,请重新登录',U('Shop/Index/login'));
			}
		}else{
			$user=session('user');
			$entity=array(
				'uid'=>$user['id'],
				'country'=>'中国',
				'city'=>I('sheng',''),
				'province'=>I('shi',''),
				'area'=>I('qu',''),
				'detailinfo'=>I('address',''),
				'contactname'=>I('uname',''),
				'mobile'=>I('phone',''),
				'cityid'=>I('shengid',''),
				'provinceid'=>I('shiid',''),
				'areaid'=>I('quid',''),
				'wxno'=>'',
			);
//			dump($entity);
			$result=apiCall(AddressApi::ADD,array($entity));
			if($result['status']){
				$this->assign("add",null);
				$this->success('添加地址成功!');
				
			}else{
				$this->error('添加失败');
			}
		}
	}
	/**
	 * 设置默认地址
	 */
	public function setdefault(){
		$user=session('user');
		$this->success('设置成功!');
	}
	/*
	 * 忘记密码
	 * */
	public function forgetpwd1(){
		$a=A('Index');
		$a->countcookie();
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$this->theme($this->themeType)->display();
	}
	public function forgetpwd2(){
		$map=array('username'=>I('uname',''));
		$user=apiCall(UserApi::FIND,array($map));
		$this->assign('phone',substr_replace($user['info']['mobile'],'****', 3, 4));
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$a=A('Index');
		$a->countcookie();
		$this->theme($this->themeType)->display();
	}
	/*
	 * 修改地址
	 * */
	public function editadd(){
		$id=I('id',0);
		if($id!=0){
			$entity=array(
				'country'=>'中国',
				'city'=>I('sheng',''),
				'province'=>I('shi',''),
				'area'=>I('qu',''),
				'detailinfo'=>I('address',''),
				'contactname'=>I('uname',''),
				'mobile'=>I('phone',''),
				'cityid'=>I('shengid',''),
				'provinceid'=>I('shiid',''),
				'areaid'=>I('quid',''),
				'wxno'=>'',
			);
//			dump($entity);
			$result=apiCall(AddressApi::SAVE_BY_ID,array($id,$entity));
			if($result['status']){
				$this->assign("add",null);
				$this->success('修改地址成功!',U('Shop/User/address'));
			}else{
				$this->error('修改失败');
			}
		}
		
	}
	/*
	 * 删除地址
	 * */
	 public function deladdress(){
	 	$id=I('id',0);
		if($id!=0){
//			dump($id);
			$map=array('id'=>$id);
			$result=apiCall(AddressApi::DELETE,array($map));
			$this->success('删除地址成功!',U('Shop/User/address'));
		}else{
			$this->error('删除失败');
		}
	 }
	/**
	 * 个人中心
	 */
	public function index(){
		$user=session('user');
		$this->assign('user',$user);
		$id=$user['id'];
		$map=array('id'=>$id);
		if($id!=0){
			$result=apiCall(AccountApi::GET_INFO,array($id));
			$this->assign('user',$result['info']);
			$this->assign('phone',substr_replace($result['info']['mobile'],'****', 3, 4));
			$map1=array('uid'=>$user['id'],'pay_status'=>0);
			$result1=apiCall(OrdersApi::QUERY_NO_PAGING,array($map1));
			$map2=array('uid'=>$user['id'],'order_status'=>4);
			$result2=apiCall(OrdersApi::QUERY_NO_PAGING,array($map2));
			$map3=array('uid'=>$user['id'],'comment_status'=>0);
			$result3=apiCall(OrdersApi::QUERY_NO_PAGING,array($map3));
			
			$this->assign('wait',count($result1['info']));
			$this->assign('shouhuo',count($result2['info']));
			$this->assign('pingjia',count($result3['info']));
			$map=array('id'=>140);
			$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
			$this->assign('group',$resultw['info']);
			$a=A('Index');
			$a->countcookie();
			$map=array('uid'=>$user['id']);
			$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
			$this->assign('userinfo',$result['info'][0]);
//			dump($user);
			$this->theme($this->themeType)->display();
		}else{
			$this->error('获取用户信息失败,请重新登录',U('Shop/Index/login'));
		}
		
	}

	/**
	 *	订单
	 */
	public function order(){
		$user=session('user');
		if($user!=null){
			$this->assign('user',$user);
			$map1=array('uid'=>$user['id']);
			$mapdh=array('id'=>140);
			$resultdh=apiCall(CategoryApi::QUERY_NO_PAGING,array($mapdh));
			$this->assign('group',$resultdh['info']);
			$page = array('curpage'=>I('post.p',0),'size'=>10);
			$result1=apiCall(OrdersInfoViewApi::QUERY,array($map1,$page));
			$map2=array('uid'=>$user['id'],'order_status'=>4);
			$result2=apiCall(OrdersInfoViewApi::QUERY,array($map2,$page));
			$map3=array('uid'=>$user['id'],'pay_status'=>0);
			$result3=apiCall(OrdersInfoViewApi::QUERY,array($map3,$page));
			$map4=array('uid'=>$user['id'],'order_status'=>8);
			$result4=apiCall(OrdersInfoViewApi::QUERY,array($map4,$page));
			$result=apiCall(OrdersItemApi::QUERY_NO_PAGING);
			$tui=apiCall(OrderRefundApi::QUERY_NO_PAGING);
			$this->assign('ths',$tui['info']);
			$this->assign('pro',$result['info']);
			$this->assign('orders',$result1['info']['list']);
			$this->assign('orders_sh',$result2['info']['list']);
			$this->assign('orders_fk',$result3['info']['list']);
			$this->assign('orders_qx',$result4['info']['list']);
			$this->assign('show1',$result1['info']['show']);
			$this->assign('show2',$result2['info']['show']);
			$this->assign('show3',$result3['info']['show']);
			$this->assign('show4',$result4['info']['show']);
			$a=A('Index');
			$a->countcookie();
			$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
	//		dump($result1);
			$this->theme($this->themeType)->display();
		}else{
			$this->error('请先登录',U('Shop/Index/login'));
		}
		
	}
	/*
	 * 用户信息
	 * */
	public function infoview(){
		$user=session('user');
		$this->assign('user',$user);
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$a=A('Index');
		$a->countcookie();
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$this->assign('phone',substr_replace($user['mobile'],'****', 3, 4));
		$this->assign('mobile',$user['mobile']);
//		dump($map);
		$this->theme($this->themeType)->display();
	}
	/**
	 * 修改用户资料
	 */
	public function edituser(){
		$id=array('uid'=>I('uid',0));
		$entity=array(
			'realname'=>I('username',''),
			'birthday'=>strtotime(I('bdate',0)),
			'nickname'=>I('nickname',''),
			'sex'=>I('sex',0),
		);
		if($id!=0){
			$result=apiCall(MemberApi::SAVE,array($id,$entity));
			if($result['status']){
				$this->success('修改成功');
			}
		}else{
			$this->error('用户信息获取失败');
		}	
		
		
	}
	/*
	 * 资金管理
	 * */
	public function moneymanager(){
		$user=session('user');
		$this->assign('user',$user);
		$mapdh=array('id'=>140);
		$resultdh=apiCall(CategoryApi::QUERY_NO_PAGING,array($mapdh));
		$this->assign('group',$resultdh['info']);
		if(IS_GET){
			$map=array('uid'=>$user['id']);
			$fg=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
			$this->assign('userconfig',$fg['info'][0]);
			$page = array('curpage'=>I('post.p',0),'size'=>10);
			$result=apiCall(FinAccountBalanceHisApi::QUERY,array($map,$page));
			$this->assign('jilu',$result['info']['list']);
			$this->assign('show',$result['info']['show']);
			$pro=apiCall(ProvinceApi::QUERY_NO_PAGING,array());
			$this->assign('phone',substr_replace($user['mobile'],'****', 3, 4));
			$this->assign('pro',$pro['info']);
			$map=array('uid'=>$user['id']);
			$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
			$this->assign('userinfo',$result['info'][0]);
			$this->theme($this->themeType)->display();
		}else{
			$type=I('type',0);
			if($type==1){
				$uid=I('uid',0);
				$entity=array(
					'uid'=>$uid,
					'defray'=>I('price'),
					'income'=>0,
					'create_time'=>time(),
					'notes'=>"支付宝账号：".I('unum','').",收款人：".I('uname').",提现金额：".I('price')."元",
					'dtree_type'=>3,
					'status'=>2,
				);
				$result=apiCall(FinAccountBalanceHisApi::ADD,array($entity));
				if($result['status']){
					$id=$uid;
					$map=array('uid'=>$uid);
					$results=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
					$dcoin=$results['info'][0]['frozencoin'];
					$coin=$dcoin+I('price');
					$yue=$results['info'][0]['coin']-I('price');
					$entity=array('frozencoin'=>$coin,'coin'=>$yue);
					$results=apiCall(MemberConfigApi::SAVE,array($map,$entity));
					$this->success('提交成功,请关注提现动态!');
				}else{
					$this->error($result['info']);
				}
			}else{
				$entity=array(
					'uid'=>$user['id'],
					'defray'=>I('price'),
					'income'=>0,
					'create_time'=>time(),
					'notes'=>"银行名称：".I('bank').",银行卡账号：".I('unum','').",持卡人：".I('uname').",开户地：".I('sheng').I('shi').",提现金额：".I('price')."元",
					'dtree_type'=>3,
					'status'=>2,
				);
				$result=apiCall(FinAccountBalanceHisApi::ADD,array($entity));
				if($result['status']){
					$id=$user['id'];
					$map=array('uid'=>$user['id']);
					$results=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
					$dcoin=$results['info'][0]['frozencoin'];
					$coin=$dcoin+I('price');
					$yue=$results['info'][0]['coin']-I('price');
					$entity=array('frozencoin'=>$coin,'coin'=>$yue);
					$results=apiCall(MemberConfigApi::SAVE,array($map,$entity));
					$this->success('提交成功,请关注提现动态!');
				}else{
					$this->error($result['info']);
				}
			}
		}
		
	}
	/*
	 * 退货
	 * */
	public function resend(){
		$entity=array('create_time'=>time(),'reason'=>I('yy','无'),'valid_status'=>0,'reply_msg'=>'','order_code'=>I('orderid',0));
		$result=apiCall(OrderRefundApi::ADD,array($entity));
		if($result['status']){
			$map=array('order_code'=>I('orderid',0));
			$ent=array('order_status'=>9);
			$resu=apiCall(OrdersApi::SAVE,array($map,$ent));
			$this->success('申请退货成功,等待客服反馈!');
		}else{
			$this->error('申请退货失败!');
		}
	}
	/*
	 * 订单详情
	 * */
	public function orderdetails(){
		$order=I('ordercode',0);
		$map=array('order_code'=>$order);
		$result=apiCall(OrdersExpressApi::QUERY_NO_PAGING,array());
		$results=apiCall(OrdersInfoViewApi::QUERY_NO_PAGING,array($map));
		$resultss=apiCall(OrdersItemApi::QUERY_NO_PAGING,array($map));
		$this->assign('express',$result['info']);
		$a=A('Index');
		$a->countcookie();
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$orderall=$results['info'][0];
		$orderall['items']=$resultss['info'];
		$this->assign('od',$orderall);
		$map=array('uid'=>$user['id']);
		$result=apiCall(MemberApi::QUERY_NO_PAGING,array($map));
		$this->assign('userinfo',$result['info'][0]);
		$this->theme($this->themeType)->display();
	}
	/*
	 * qq
	 * */
	public function zixun(){
		$user=session('user');
		$this->assign('user',$user);
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$this->theme($this->themeType)->display();
	}
	/**
	 * 分享
	 */
	public function show(){
		$user=session('user');
		$this->assign('user',$user);
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$this->theme($this->themeType)->display();
	}
	/*
	 * 我的等级
	 * */
	public function mylevel(){
		$user=session('user');
		$this->assign('user',$user);
		$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
		$uid=$user['id'];
		$return=apiCall(UidMgroupApi::QUERY_WITH_UID,array($uid));
		$result=apiCall(StoreApi::QUERY_NO_PAGING);
//		dump($return);dump($result);
		$this->assign('level',$return['info']);
		$this->assign('store',$result['info']);
		$this->theme($this->themeType)->display();
	}
	/*
        ***聚合数据（JUHE.CN）数据接口调用通用DEMO SDK
        ***DATE:2014-04-14
    */
    public function toMessage()
    {
        $mobile=I("mobile",0);
        //判断该手机是否已经注册过了
        $map=array(
            'mobile'=>$mobile,
        );

//   	dump($map);
        //$this->apiReturnSuc("可以使用");
        /*  header('content-type:text/html;charset=utf-8');*/
        $appkey=C('JUHE_API.MSG_APPKEY'); #通过聚合申请到数据的appkey
        $tpl_id=C('JUHE_API.MSG_TPL_ID');
        //  = 'xxxxxxxxxxxxxxxxxxxxxx';
        $sendUrl =C('JUHE_API.MSG_SENDURL'); #请求的数据接口URL
        $code=rand(1000,9999);
        $smsConf = array(
            'key'   => $appkey, //您申请的APPKEY
            'mobile'    => $mobile, //接受短信的用户手机号码
            'tpl_id'    => $tpl_id, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>'#code#='.$code //您设置的模板变量，根据实际情况修改
        );

        $content =$this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            //  $this->apiReturnSuc($result['info']);
            if($error_code == 0){
                //状态为0，说明短信发送成功
                $map=array(
                    'code'=>$code,
                    'accepter'=>$mobile,
                    'starttime'=>time(),
                    'endtime'=>time()+1800,
                    'ip'=>ip2long(get_client_ip()),
                    'client_id'=>$this->client_id
                );
                apiCall(SecurityCodeApi::ADD,array($map));

                $this->success("短信发送成功,短信ID：".$result['result']['sid']);
                // dump();
            }else{
                //状态非0，说明失败
                $msg = $result['reason'];
                $this->error( "短信发送失败(".$error_code.")：".$msg);
            }
        }else{
            $this->error("请求发送短信失败");
        }
  

        //var_dump("短信发送失败");
      // $this->apiReturnErr();

    }

        /*
         * 验证码验证
         */
       public function checkCode(){
       	   $user=session('user');
           $code=I('code',0);
           $mobile=I('mobile',0);
           $map=array(
               'code'=>$code,
               'accepter'=>$mobile,
               'endtime'=>array('GT',time()),
               'status'=>0,
           );
           $order="endtime desc";
           $result=apiCall(SecurityCodeApi::QUERY_NO_PAGING,array($map,$order)); 
           if($result['info']==null){
               $this->error("验证失败，验证码过期或者填写错误");
           }else{
               $map=array(
                   'code'=>$code,
                   'accepter'=>$mobile,
                   'status'=>1,
               );
			   $id=$user['id'];
			   $entity=array('mobile'=>I('newmobile',0));
			   $result3=apiCall(UserApi::SAVE_BY_ID,array($id,$entity));
               $result2=apiCall(SecurityCodeApi::SAVE_BY_ID,array($result['info'][0]['id'],$map));
               if($result2['status']){
                   $this->success("修改成功");
               }else{
                   $this->error("修改失败");
               }

           }
       }


        /*
            ***请求接口，返回JSON数据
            ***@url:接口地址
            ***@params:传递的参数
            ***@ispost:是否以POST提交，默认GET
        */
        function juhecurl($url,$params=false,$ispost=0){
            $httpInfo = array();
            $ch = curl_init();

            curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_0 );
            curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
            curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
            curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
            if( $ispost )
            {
                curl_setopt( $ch , CURLOPT_POST , true );
                curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
                curl_setopt( $ch , CURLOPT_URL , $url );
            }
            else
            {
                if($params){
                    curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
                }else{
                    curl_setopt( $ch , CURLOPT_URL , $url);
                }
            }
            $response = curl_exec( $ch );
            if ($response === FALSE) {
                #echo "cURL Error: " . curl_error($ch);
                return false;
            }
            $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
            $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
            curl_close( $ch );
            return $response;
        }
}

