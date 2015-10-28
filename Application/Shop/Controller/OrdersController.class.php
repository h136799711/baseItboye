<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Admin\Api\AddonsApi;
use Shop\Api\AddressApi;
use Shop\Api\OrderCommentApi;
use Shop\Api\OrdersApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\OrdersItemApi;
use Shop\Api\OrderStatusApi;
use Shop\Api\OrderStatusHistoryApi;
use Shop\Api\ProductApi;
use Shop\Api\ProductSkuApi;
use Shop\Api\StoreApi;
use Shop\Model\OrdersModel;
use Shop\Model\OrderStatusHistoryModel;
use Think\Controller;
use Tool\Api\AreaApi;
use Tool\Api\CityApi;
use Tool\Api\ProvinceApi;
use Shop\Api\ShoppingCartApi;
use Shop\Api\CategoryApi;
use Shop\Api\MemberConfigApi;
use Admin\Api\UidMgroupApi;
use Admin\Api\OrdersPaycodeApi;
use Shop\Api\HasIdcodeApi;

class OrdersController extends ShopController {

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
	/*
	 * 获取idcode
	 * */
	public function getidcode(){
			$user=session('user');
			$idcode=I('idcode',0);
			$price=I('price','0.00');
			$map=array('IDCode'=>$idcode);
			$result=apiCall(MemberConfigApi::QUERY_NO_PAGING,array($map));
			if($result['info']==null){
				$this->ajaxReturn(0,'json');
			}else{
				$uid=$result['info'][0]['uid'];
				if($uid!=$user['id']){
					$return=apiCall(UidMgroupApi::QUERY_WITH_UID,array($uid));
					if($return['info']==null){
						$this->ajaxReturn(0,'json');
					}else{
						$return['info'][0]['prices']=$price*$return['info'][0]['discount_ratio'];
						$this->ajaxReturn($return['info'][0],'json');
					}
				}else{
					$this->ajaxReturn(1,'json');
				}
			}
	}
	/*
	 * 订单确认
	 * TODO
	 * */
	public function index() {
		$user=session('user');
		if($user==null){
			session('user',null);
			$this->error('请先登录',U('Shop/Index/login'));
		}else{
			
			$id=I('id',0);
			$uid=array('uid'=>$user['id']);
			$map=array('id'=>$id);
			$pro=apiCall(ProvinceApi::QUERY_NO_PAGING,array());
			$orders=session('orders');
			$shopcart=session('gouwuches');
			$icd=apiCall(HasIdcodeApi::QUERY_NO_PAGING,array($uid));
			$this->assign('lishiid',$icd['info']);
			$this->assign('items',$shopcart);
			$count=0;$postmoney=0;$allprice=0;
			$suoyou=0;$youhui=0;
			$aid=$user['id'];
			$return=apiCall(UidMgroupApi::QUERY_WITH_UID,array($aid));
			if($orders['wc']==0){
				for($a=0;$a<count($orders)-1;$a++){
					for($i=0;$i<count($orders[$a]['items']);$i++){
						$count=$count+$orders[$a]['items'][$i]['count'];
						$postmoney=$postmoney+$orders[$a]['items'][$i]['post_price'];
					}
					$allprice=$allprice+$orders[$a]['price'];
					$orders[$a]['discount_money']=$orders[$a]['price']*(float)$return['info'][0]['discount_ratio'];
					$orders[$a]['price']=$orders[$a]['price']-($orders[$a]['price']*(float)$return['info'][0]['discount_ratio']);
					$suoyou=$allprice-($return['info'][0]['discount_ratio']*$allprice)+$postmoney;
					$youhui=$return['info'][0]['discount_ratio']*$allprice;
				}
			}else{
				for($a=0;$a<count($orders)-1;$a++){
					for($i=0;$i<count($orders[$a]['items']);$i++){
						$count=$count+$orders[$a]['items'][$i]['count'];
						$postmoney=$postmoney+$orders[$a]['items'][$i]['post_price'];
					}
					$suoyou=$suoyou+$orders[$a]['price'];
					$youhui=$youhui+$orders[$a]['discount_money'];
				}
			}
			$orders['wc']=1;
			
			session('orders',$orders);
			
//			dump($orders);
			$this->assign('procount',$count);
			$count=0;
			$this->assign('post_price',$postmoney);
			$this->assign('user',$user);
			$uid = array('uid' => $user['id']);
			$map = array('id' => $id);
			
			$result=apiCall(AddressApi::QUERY_NO_PAGING,array($uid));
			
			$this->assign('allmoney',$suoyou);
			$this->assign('youhui',$youhui);
			$this->assign('address',$result['info']);
			$this->assign('pro',$pro['info']);
			$map=array('id'=>140);
			$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
			$this->assign('group',$result1['info']);
			$a=A('Index');
			$a->countcookie();
			$this -> theme($this -> themeType) -> display(); 
		}
		
		
	}
	/**
	 * 取消订单
	 */
	public function qxorder(){

		$map=array('order_code'=>I('ordercode',0));
		$entity=array('order_status'=>8);
		$result=apiCall(OrdersApi::SAVE,array($map,$entity));
		if($result['status']){
			$this->success('取消成功!');
		}else{
			$this->error('取消失败!');
		}
		
	}
	/** 
	 * 订单支付
	 */	
//	public function gopayorder(){
//		$map=array('order_code'=>I('ordercode',0));
//		
////		$this ->success('正在跳转支付界面!',U('Shop/Orders/index'));
//	}
	
	public function gopay(){
		$user=session('user');
		$id[]=I('ids',0);
		$counts[]=I('counts',0);
//		$price[]=I('prices',0);
//		$skuval[]=I('skuval','无');
//		$allmoney=I('allmoney',0);
		$maps=array('uid'=>$user['id']);
		$results=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($maps));
		for($i=0;$i<count($results['info']);$i++){
			$store[]=$results['info'][$i]['store_id'];
		}
		$a=$this->a_array_unique($store);
		$storeid[]=array_values(array_unique($a));
		for($i=0;$i<count($storeid[0]);$i++){
			$allprice=0;
			$resultw=array();
			for($a=0;$a<count($results['info']);$a++){
//				dump($results['info'][$a]['store_id']);
				if($results['info'][$a]['store_id']==$storeid[0][$i]){
					for($b=0;$b<count($id[0]);$b++){
						
						if($id[0][$b]==$results['info'][$a]['p_id']){
							$results['info'][$a]['count']=$counts[0][$a];
							$resultw[]=$results['info'][$a];
							$gwc[]=$results['info'][$a];
							$allprice=$allprice+$results['info'][$a]['price']*$results['info'][$a]['count'];
						}
					}
				}
			}
			
			$orderid=getOrderid($uid);
			$order[]=array('uid'=>$user['id'],'order_code'=>$orderid,'price'=>$allprice,
					 'post_price'=>'0.00','note'=>'',
					 'comment_status'=>0,'items'=>$resultw);
//					 dump($order[$i]);
		}
		
		session('gouwuches',$gwc);
		$order['wc']=0;
		session('orders',$order);
		$this ->success('正在跳转支付界面!',U('Shop/Orders/index'));
//		



		
		
		
	}
	function a_array_unique($array)//写的比较好
	{
	   $out = array();
	   foreach ($array as $key=>$value) {
	       if (!in_array($value, $out))
		{
	           $out[$key] = $value;
	       }
	   }
	   return $out;
	} 
	

	/*
	 * TODO:订单全部 首页购物车数据统计
	 * 添加订单
	 * */
	public function addorder(){
		$user=session('user');
		$orders=session('orders');
		if($orders==null){
			$this->error('请勿重复提交！',U('Shop/Index/index'));
		}else{
			$zhekou=I('zk',0);
//			dump($orders);
			for($a=0;$a<count($orders)-1;$a++){
//				$orders=array();
				$orders[$a]['contactname']=I('username','');
				$orders[$a]['wxno']='';
				$orders[$a]['discount_money']=$orders[$a]['discount_money']+$orders[$a]['price']*$zhekou;
				$orders[$a]['price']=$orders[$a]['price']-($orders[$a]['price']*$zhekou);
				$orders[$a]['idcode']=I('idcode','');
				$orders[$a]['mobile']=I('userphone','');
				$orders[$a]['country']='中国';
				$orders[$a]['province']=I('sheng' ,'');
				$orders[$a]['city']=I('shi','');
				$orders[$a]['area']=I('qu','');
				$orders[$a]['detailinfo']=I('details','');
				$orders[$a]['storeid']=$orders[$a]['items'][0]['store_id'];
				$orders[$a]['from']=1; 
				$orders[$a]['note']=I('mess','');
//				dump($orders);
				if(I('username','')==''){
					$this->error('请选择收货地址!');
				}else{
//					dump($orders[$a]);
					$result=apiCall(OrdersApi::ADD_ORDER,array($orders[$a]));
					
				}	
				
				
			}
			if($result['status']){
				session('ods',$orders);
				$map=array('uid'=>$user['id']);
				$result=apiCall(ShoppingCartApi::DELETE,array($map));
				cookie('shopcat',null);
				$map=array('idcode'=>I('idcode',0));
				$resw=apiCall(HasIdcodeApi::QUERY_NO_PAGING,array($map));
				$entity=array('uid'=>$user['id'],'idcode'=>I('idcode',0),'get_time'=>time(),'status'=>1);
				if($resw['info']==null){
					$ress=apiCall(HasIdcodeApi::ADD,array($entity));
					$this->success('订单创建完成,正在跳转支付界面',U('Shop/Orders/paytype'));
				}else{
					$this->success('订单创建完成,正在跳转支付界面',U('Shop/Orders/paytype'));
				}
				
				
				
			}else{
				$this->error('订单创建失败');
			}
//			
				
			
			
		}
	}
	/*
	 * 支付方式
	 * */
	public function paytype() {
		session('orders',null);
		$user=session('user');
		$this->assign('user',$user);
		$orders=session('ods');
		
		$orderid=I('ordercode',0);
		$this->assign('isnull',$orderid);
//		dump($orders);
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
		$zongjia=0;$youhui=0;$youfei=0;
		if($orderid==0){
			for($a=0;$a<count($orders)-1;$a++){
	   			$address["contactname"] = $orders[$a]['contactname'];
				$address["mobile"] = $orders[$a]['mobile'];
				$address["country"] =$orders[$a]['country'];
				$address["province"] = $orders[$a]['province'];
				$address["city"] = $orders[$a]['city'];
				$address["area"] = $orders[$a]['area'];
				$zongjia=$zongjia+$orders[$a]['price'];
				$youhui=$youhui+$orders[$a]['discount_money'];
				
				$youfei=$youfei+$orders[$a]['post_price'];
				$ordercode=$ordercode.$orders[$a]['order_code'].";";
			}
			$shopcart=session('gouwuches');
//			array_pop($shopcart);
			for($a=0;$a<count($shopcart);$a++){
				$name=$name.$shopcart[$a]['name']." ";
			}
			$entity=array(
				'order_content'=>$ordercode,
				'createtime'=>time(),
				'uid'=>$user['id'],
				'pay_type'=>1,
				);
			$re=apiCall(OrdersPaycodeApi::ADD,array($entity));
			if($re['status']){
				$this->assign('paycode',$re['info']);
			}
			$this->assign('name',$name);
			$this->assign('items',$shopcart);
			$this->assign('address',$address);
			$this->assign('zongjia',round($zongjia,2));
			$this->assign('youhui',round($youhui,2));
			$this->assign('youfei',round($youfei,2));
		}else{
			$map=array('order_code'=>$orderid);
			$results=apiCall(OrdersInfoViewApi::QUERY_NO_PAGING,array($map));
			$resultss=apiCall(OrdersItemApi::QUERY_NO_PAGING,array($map));
			for($a=0;$a<count($resultss['info']);$a++){
				$name=$name.$resultss['info'][$a]['name']." ";
			}
			$entity=array(
				'order_content'=>$orderid,
				'createtime'=>time(),
				'uid'=>$user['id'],
				'pay_type'=>1,
				);
			$re=apiCall(OrdersPaycodeApi::ADD,array($entity));
			if($re['status']){
				$this->assign('paycode',$re['info']);
			}
//			dump($results);dump($resultss);
			$this->assign('name',$name);
			$this->assign('items',$resultss['info']);
			$this->assign('address',$results['info'][0]);
			$this->assign('zongjia',$results['info'][0]['price']);
			$this->assign('youhui',$results['info'][0]['discount_money']);
			$this->assign('youfei',$results['info'][0]['post_price']);
		}
		

		$this ->theme($this->themeType)->display();
		/*if($orders==null){
			$map=array('order_code'=>$orderid);
			$results=apiCall(OrdersInfoViewApi::QUERY_NO_PAGING,array($map));
			$resultss=apiCall(OrdersItemApi::QUERY_NO_PAGING,array($map));
			$orderall=$results['info'][0];
			$orderall['items']=$resultss['info'];
			$this->assign('orderinfo',$results['info'][0]);
			$this->assign('orderitems',$resultss['info']);
			$this->assign('od',$orderall);
			session('ods',null);
			$a=A('Index');
			$a->countcookie();
			
//			$this->error('请勿重复提交！',U('Shop/Index/index'));
		}else{
			$this->assign('od',$orders);
			session('ods',null);
			$this ->theme($this->themeType)->display();
		}*/
		
	}
	/*
	 * 确认收货
	 * */
	public function ok(){
		$map=array('order_code'=>I('ordercode',0));
		$entity=array('order_status'=>5);
		$result=apiCall(OrdersApi::SAVE,array($map,$entity));
		if($result['status']){
			$this->success('成功确认收货!');
		}else{
			$this->error('确认收货失败!');
		}
		
	}
	/*
	 * 支付成功
	 * */
	public function paysucc(){
		cookie('shopcat',null);
		session('orders',null);
		$user=session('user');
		$this->assign('user',$user);
		$idc=I('WIDout_trade_no','');
//		dump($idc);
		/*if($idc==''){
			$this->error('请勿重复提交！',U('Shop/Index/index'));
//			dump($idc);
		}else{
			$map=array('order_code'=>I('WIDout_trade_no',0));
			$entity=array('pay_status'=>1,'order_status'=>2);
			$result=apiCall(OrdersApi::SAVE,array($map,$entity));
			$id=array('id'=>$result['info']);
			$uid=array('uid'=>$user['id']);
			$results=apiCall(OrdersInfoViewApi::QUERY_NO_PAGING,array($map));
			$resultss=apiCall(OrdersItemApi::QUERY_NO_PAGING,array($map));
			$c=apiCall(ShoppingCartApi::DELETE,array($uid));
			$this->assign('orderinfo',$results['info'][0]);
			$this->assign('orderitems',$resultss['info']);
			session('ods',null);
			$this ->theme($this->themeType)->display();
		}*/
		$this ->theme($this->themeType)->display();
	}
	
	/*
	 * /*目前支持的快递公司
         *顺丰 sf
         *申通 sto
	     *圆通 yt
         *韵达 yd
         *天天 tt
         *EMS ems
         *中通 zto
         *汇通 ht
        
	 * 
        $url =C('express_sendUrl');#请求的数据接口URL
        $com=I("com",'');
        $no=I("no",0);
        $params='com='.$com.'&no='.$no.'&dtype=json&key='.C('express_appkey');
        $content = $this->juhecurl($url,$params,0);
        if($content){
            $result = json_decode($content,true);
            $result_code = $result['resultcode'];
            if($result_code == 200){
                $this->ajaxReturn($result['result'],'json');
            }else{
                 $this->ajaxReturn("订单查询失败,错误ID号：".$result_code);
            }
        }else{
             $this->ajaxReturn("订单查询失败");
        }
	 * */
	
	 /**
     * 查询订单
     */
    function  searchExpress(){

         $url =C('JUHE_API.EXPRESS_SENDURL');#请求的数据接口URL
        $com=I("com",0);
        $no=I("no",0);
        $params='com='.$com.'&no='.$no.'&dtype=json&key='.C('JUHE_API.EXPRESS_APPKEY');

        $content = $this->juhecurl($url,$params,0);
        if($content){
            $result = json_decode($content,true);
            $result_code = $result['resultcode'];
            if($result_code == 200){
                $this->ajaxReturn($result['result']);
            }else{
                $this->ajaxReturn("订单查询失败,错误ID号：".$result_code);
            }
        }else{
            $this->ajaxReturn("订单查询失败");
        }
    }
	
	
	/**
     * 查询订单
     */
    function  searchExpressHtml(){

        /*$url=C('JUHE_API.EXPRESS_SENDURL'); #请求的数据接口URL
        //dump($url);
        $com=I("com",0);
        $no=I("no",0);
        $params='com='.$com.'&no='.$no.'&dtype=json&key='.C('JUHE_API.EXPRESS_APPKEY');
        $content = $this->juhecurl($url,$params,0);
        if($content){
            $result = json_decode($content,true);
            //rsort($result['result']['list']);
            $this->assign('result',$result);
        }else{

        }*/


        /******** 测试数据*************/
       $result['error_code']=0;
        $list[]= array(
                "datetime"=>"2013-06-25  10:44:05",
                "remark"=>"已收件",
                "zone"=>"台州市"
        );
        $list[]= array(
                "datetime"=> "2013-06-25  11:05:21",
                "remark"=>"快件在 台州  ,准备送往下一站 台州集散中心  ",
                "zone"=> "台州市"

        );
        $list[]= array(
            "datetime"=>"2013-06-25  20:36:02",
            "remark"=>"快件在 台州集散中心  ,准备送往下一站 台州集散中心 ",
            "zone"=>"台州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-25  21:17:36",
            "remark"=>"快件在 台州集散中心 ,准备送往下一站 杭州集散中心",
            "zone"=>"台州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-26  12:20:00",
            "remark"=>"快件在 杭州集散中心  ,准备送往下一站 西安集散中心 ",
            "zone"=>"杭州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-27  05:48:42",
            "remark"=>"快件在 西安集散中心 ,准备送往下一站 西安  ",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:03:03",
            "remark"=>"正在派件.. ",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:51:33",
            "remark"=>"派件已签收",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27 08:51",
            "remark"=>"派件已签收",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:51:33",
            "remark"=>"签收人是：已签收",
            "zone"=>"西安市/咸阳市"
        );


        rsort($list); //数组倒序
        $result['result']=array(
           "company"=>"顺丰",
           "com"=>"sf",
           "no"=>"575677355677",
           "status"=>1,
            "list"=>$list
        );
        $this->assign("result",$result);
        /******** 测试数据*************/
        $this ->theme($this->themeType)->display();
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





	/**
	 * 获取订单
	 * @param post.type 0=全部,1=待付款,2=待发货,3=待收货,4＝待评论
	 */
	public function orderlist() {
		$type = I('post.type', 0, 'intval');

		//是否获取物流信息
		$shouldGetExpressInfo = false;
		$map = array();
		if ($type == 1) {
			//待付款
			$map['pay_status'] = OrdersModel::ORDER_TOBE_PAID;
		} elseif($type != 0) {
			//货到付款，在线已支付
			$map['pay_status'] = array('in', array(OrdersModel::ORDER_PAID, OrdersModel::ORDER_CASH_ON_DELIVERY));

		}

		if ($type == 2) {
			//1. 已支付、货到付款
			//2. 待发货
			//
			$map['order_status'] = OrdersModel::ORDER_TOBE_SHIPPED;

		} elseif ($type == 3) {
			//1. 已支付、货到付款
			//2. 已发货
			$map['order_status'] = OrdersModel::ORDER_SHIPPED;
			$shouldGetExpressInfo = true;
		} elseif ($type == 4) {
			//1. 已支付、货到付款
			//2. 已收货
			//3. 待评论
			$map['order_status'] = OrdersModel::ORDER_RECEIPT_OF_GOODS;
			$map['comment_status'] = OrdersModel::ORDER_TOBE_EVALUATE;
			$shouldGetExpressInfo = true;

		}

		$map['wxuser_id'] = $this -> userinfo['id'];
		//TODO: 订单假删除时不查询
		$map['status'] = 1;
		$orders = " createtime desc ";
		$page = array('curpage'=>I('post.p',0),'size'=>3);

		$result = apiCall(OrdersApi::QUERY, array($map, $page, $orders));

		ifFailedLogRecord($result, __FILE__ . __LINE__);

		//1. 订单信息
		$order_list = $result['info']['list'];
		$store_ids = array();
		$order_ids = array();
		$result_list = array();

		$store_key = array();

		foreach ($order_list as $vo) {
			$entity = array(
				'orderid' => $vo['id'],
				'price' => number_format($vo['price']/100.0,2), //订单总价
				'storeid' => $vo['storeid'],
				'order_status'=>$vo['order_status'],
				'comment_status'=>$vo['comment_status'],
				'order_status_desc'=> getTaobaoOrderStatus($vo['order_status']),
				'pay_status'=>$vo['pay_status'],
				'_items' => array(), //商品列表
				'_store' => array(), //店铺信息
			);

			$result_list[$vo['id']] = $entity;
			if(!array_key_exists($vo['storeid'], $store_key)){
				array_push($store_ids, $vo['storeid']);
				$store_key[$vo['storeid']] = $vo['storeid'];
			}
			array_push($order_ids, $vo['id']);
		}


		if (count($store_ids) > 0) {
			$mapStore = array();
			$mapStore['id'] = array('in', $store_ids);
			//2. 获取店铺信息
			$result = apiCall(StoreApi::QUERY_NO_PAGING, array($mapStore));
			ifFailedLogRecord($result, __FILE__ . __LINE__);
			foreach ($result_list as &$vo_obj){
				foreach ($result['info'] as $vo) {
					if ($vo['id'] == $vo_obj['storeid']) {
						$vo_obj['_store'] = $vo;
						break;
					}
				}
			}

		}
		//3. 获取订单商品信息

		if (count($store_ids) > 0) {
			$mapOrder = array();
			$mapOrder['orders_id'] = array('in', $order_ids);
			$result = apiCall(OrdersItemApi::QUERY_NO_PAGING, array($mapOrder));

			ifFailedLogRecord($result, __FILE__ . __LINE__);

			foreach ($result['info'] as $vo) {
				$entity = array(
					'name'=>$vo['name'],
					'p_id'=>$vo['p_id'],
					'img'=>$vo['img'],
					'price'=> number_format($vo['price']/100.0,2),
					'ori_price'=> number_format($vo['ori_price']/100.0,2),
					'sku_id'=>$vo['sku_id'],
					'sku_desc'=>$vo['sku_desc'],
					'count'=>$vo['count'],
					'orders_id'=>$vo['orders_id'],
					'createtime'=> date("Y-m-d H:i:s",$vo['createtime']),
				);

				if(isset($result_list[$vo['orders_id']])){
					array_push($result_list[$vo['orders_id']]['_items'], $entity);
				}

			}

		}

		if(IS_POST){
			//dump($result_list);
			$this->success($result_list);
		}else{
			$this->error("禁止访问！");
		}

	}


	/**
	 * 手机端
	 */
	public function ordermobile(){

		//防止不能谁都可以访问


		$this->theme($this->themeType)->display();
	}

}
