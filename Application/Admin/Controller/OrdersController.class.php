<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

use Common\Api\WeixinApi;
use Shop\Api\OrdersApi;
use Shop\Api\OrdersExpressApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\OrdersItemApi;
use Shop\Api\OrderStatusApi;
use Shop\Api\OrderStatusHistoryApi;
use Shop\Model\OrdersModel;
use Weixin\Api\WxaccountApi;
use Weixin\Api\WxuserApi;
use Shop\Api\OrderRefundApi;

class OrdersController extends AdminController {
	/**
	 * 初始化
	 */
	protected function _initialize() {
		parent::_initialize();
	}
	
	/**
	 * 商家主动退回订单
	 */
	public function backOrder(){
		$id = I('post.orderid',0);
		$reason = I('post.reason','商家主动取消订单');
		
		$result = apiCall(OrdersApi::GET_INFO, array(array('id'=>$id)));
		
		if(!$result['status']){
			$this->error($result['status']);
		}
		
		if(is_null($result)){
			$this->error("订单信息获取失败，请重试！");
		}
		$wxuserid = $result['info']['wxuser_id'];
		$orderid = $result['info']['orderid'];
		$cur_status = $result['info']['order_status'];

		//检测当前订单状态是否合法
		if($cur_status != 2){
			$this->error("当前订单状态无法变更！");
		}

		//
		$result = apiCall(OrderStatusApi::BACK_ORDER, array($id,$reason,false,UID));
		
		if(!$result['status']){
			$this->error($result['info']);
		}

        //TODO: 发送退回消息移动到消息模块
		$text = "您的订单:".$orderid." [被退回],原因:".$reason.". [查看详情]";
		$orderViewLink = "<a href=\"".C('SITE_URL').U('Shop/Orders/view')."?id=$id\">$text</a>";
		$this->sendTextTo($wxuserid,$orderViewLink);
        //======================================
		$this->success("退回成功!");

	}
	
	/**
	 * 统计
	 */
	public function statics(){
		//TODO:可能数据库写一个视图效率更高吧，之后考虑
		//TODO:目前先是全表统计，之后可能考虑按时间，当天，本月，本年计算

		/**
		 * 订单退回
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_BACK,
		);
		$orderback=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 待确认
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_TOBE_CONFIRMED,
		);
		$ordertobeconfirmed=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 待发货
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_TOBE_SHIPPED,
		);
		$ordertobeshipped=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 已发货
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_SHIPPED,
		);
		$ordershipped=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 已收货
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_RECEIPT_OF_GOODS,
		);
		$orderreceiptofgoods=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 已退货
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_RETURNED,
		);
		$orderreturned=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 已完成
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_COMPLETED,
		);
		$ordercompleted=apiCall(OrdersApi::COUNT,array($map));
		/**
		 * 取消或交易关闭
		 */
		$map=array(
			'order_status'=>OrdersModel::ORDER_CANCEL,
		);
		$ordercancel=apiCall(OrdersApi::COUNT,array($map));
		//dump($orderback);
		$orderstatus=array(
			'order_back'=>$orderback['info'],
			'order_tobe_confirmed'=>$ordertobeconfirmed['info'],
			'order_tobe_shipped'=>$ordertobeshipped['info'],
			'order_shipped'=>$ordershipped['info'],
			'order_receipt_of_goods'=>$orderreceiptofgoods['info'],
			'order_returned'=>$orderreturned['info'],
			'order_completed'=>$ordercompleted['info'],
			'order_cancel'=>$ordercancel['info']
		);
		$map=array();
		$price=apiCall(OrdersApi::SUM,array($map,"price"));

		$this->assign('order_status',$orderstatus);
		$this->assign('price',$price['info']);
		$this->display();
	}

	/**
	 * 订单管理
	 */
	public function index() {
		$arr = getDataRange(3);
		$payStatus = I('paystatus', '');
		$orderStatus = I('orderstatus', '');
		$orderid = I('post.orderid', '');
		$userid = I('uid', 0);
		$startdatetime = urldecode($arr[0]);
		//I('startdatetime', , 'urldecode');
		$enddatetime = urldecode($arr[1]);
		//I('enddatetime',   , 'urldecode');

		//分页时带参数get参数
		$params = array('startdatetime' => $startdatetime, 'enddatetime' => ($enddatetime),'wxaccountid'=>getWxAccountID());

		$startdatetime = strtotime($startdatetime);
		$enddatetime = strtotime($enddatetime);

		if ($startdatetime === FALSE || $enddatetime === FALSE) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error(L('ERR_DATE_INVALID'));
		}

		$map = array();
		//$map['wxaccountid'] = getWxAccountID();
		if (!empty($orderid)) {
			$map['orderid'] = array('like', $orderid . '%');

		}
		if ($payStatus != '') {
			$map['pay_status'] = $payStatus;
			$params['paystatus'] = $payStatus;
		}
		if ($orderStatus != '') {
			$map['order_status'] = $orderStatus;
			$params['orderstatus'] = $orderStatus;
		}
		$map['createtime'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if ($userid > 0) {
			$map['uid'] = $userid;
		}

		//
		$result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));
		//dump($result);
		//
		if ($result['status']) {
			$this -> assign('orderid', $orderid);
			$this -> assign('orderStatus', $orderStatus);
			$this -> assign('payStatus', $payStatus);
			$this -> assign('startdatetime', $startdatetime);
			$this -> assign('enddatetime', $enddatetime);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 订单确认
	 */
	public function sure() {
		$orderid = I('orderid', '');
		$payStatus = I('payStatus', OrdersModel::ORDER_PAID);
		
		$userid = I('uid', 0);
		$params = array();
		$map = array();
		$map['order_status'] = OrdersModel::ORDER_TOBE_CONFIRMED;
		if(!empty($payStatus)){
			$map['pay_status'] = $payStatus;			
		}
		
		//$map['wxaccountid']=getWxAccountID();
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if (!empty($orderid)) {
			$map['orderid'] = array('like', $orderid . '%');
			$params['orderid'] = $orderid;
		}
		if ($userid > 0) {
			$map['uid'] = $userid;
			$params['uid'] = $userid;
		}

		//
		$result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('orderid', $orderid);
			$this -> assign('payStatus', $payStatus);
			//$this -> assign('orderStatus', $orderStatus);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 发货
	 */
	public function deliverGoods() {
		$orderStatus = I('orderstatus','3');
		$ordercode = I('order_code', '');
		$userid = I('uid', 0);
		$params = array();

		$map = array();
		//$map['wxaccountid']=getWxAccountID();
		//$params['uid'] = $map['uid'];
		if (!empty($ordercode)) {
			$map['order_code'] = array('like', $ordercode . '%');
			$params['order_code'] = $ordercode;
		}


		if($orderStatus != ''){
			$map['order_status'] = $orderStatus;
			$params['order_status'] = $orderStatus;
		}

		//		$map['createtime'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');
		//$map['pay_status'] = OrdersModel::ORDER_PAID;
		
		$map['pay_status']=array(
			'in',array(OrdersModel::ORDER_CASH_ON_DELIVERY,OrdersModel::ORDER_PAID)
		);
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if ($userid > 0) {
			$map['u'] = $userid;
		}

		//
		$result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('order_code', $ordercode);
			$this -> assign('orderStatus', $orderStatus);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 查看
	 */
	public function view() {
		if (IS_GET) {
			$id = I('get.id',0);
			$map = array('id'=>$id);
			$result = apiCall(OrdersInfoViewApi::GET_INFO, array($map));
			if ($result['status']) {
				$orderid = $result['info']['order_code'];			
				$this -> assign("order", $result['info']);
				$result = apiCall(OrdersItemApi::QUERY_NO_PAGING, array(array('order_code'=>$orderid)));
				if(!$result['status']){
					ifFailedLogRecord($result, __FILE__.__LINE__);
					$this->error($result['info']);
				}
//				dump($result);
				$this -> assign("items", $result['info']);
				
				//查询订单状态变更纪录				
				$result = apiCall(OrderStatusHistoryApi::QUERY_NO_PAGING, array(array('order_code'=>$orderid),"create_time desc"));
//				dump($result);
				if(!$result['status']){
					ifFailedLogRecord($result, __FILE__.__LINE__);
					$this->error($result['info']);
				}
				
				$this -> assign("statushistory", $result['info']);
				$this -> display();
			} else {
				$this -> error($result['info']);
			}
		}
	}

	/**
	 * 单个发货操作
	 */
	public function deliver() {
		$expresslist = C("CFG_EXPRESS");
		if (IS_GET) {
			$id = I('get.id',0);
			$map = array('id'=>$id);
			$result = apiCall(OrdersInfoViewApi::GET_INFO, array($map));
			if($result['status']){
				$this->assign("order",$result['info']);
			}else{
				$this->error("订单信息获取失败！");
			}
			//dump($result);
			$map = array('order_code'=>$result['info']['order_code']);
			$result = apiCall(OrdersExpressApi::GET_INFO, array($map));
			//dump($result);
			if($result['status'] && is_array($result['info'])){
				$this->assign("express",$result['info']);
			}
			$this->assign("expresslist",$expresslist);
			$this->display();
		} elseif (IS_POST) {
			
			$expresscode = I('post.expresscode','');
			$expressno = I('post.expressno','');
			$uid = I('post.uid',0);
			$ordercode = I('post.ordercode','');
			$orderOfid = I('post.orderOfid','');
			if(empty($expresscode) || !isset($expresslist[$expresscode])){
				$this->error("快递信息错误！");
			}
			if(empty($expressno)){
				$this->error("快递单号不能为空");
			}
			$id = I('post.id',0);
			$entity = array(
				  'expresscode'=>$expresscode,
				  'expressname'=>$expresslist[$expresscode],
				  'expressno'=>$expressno,
				  'note'=>I('post.note',''),
				  'order_code'=>$ordercode,
				  'uid'=>$uid,
			);
			
			if(empty($entity['order_code'])){
				$this->error("订单编号不能为空");
			}
			if(empty($id) || $id <= 0){
				$result = apiCall(OrdersExpressApi::ADD, array($entity));
			}else{
				$result = apiCall(OrdersExpressApi::SAVE_BY_ID, array($id,$entity));
			}
			
			
			if($result['status']){
				
				// 1. 修改订单状态为已发货
//                $result = ServiceCall("Common/Order/shipped", array($orderOfid,false,UID));
                $result = apiCall(OrderStatusApi::SHIPPED, array($orderOfid,false,UID));

                if(!$result['status']){
					ifFailedLogRecord($result['info'], __FILE__.__LINE__);
				}

                //TODO: 移动到消息模块处理
                //========================================
				$text = "亲，您订单($ordercode)已经发货，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";

				// 2.发送提醒信息给指定用户
				$this->sendTextTo($uid,$text);
                //========================================
				
				$this->success(L('RESULT_SUCCESS'),U('Admin/Orders/deliverGoods'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	

	/**
	 * 退货管理
	 */
	public function returned() {
//		if (IS_GET) {
			$orderid = I('orderid', '');
			$userid = I('uid', 0);
			$valid_status=I('valid_status',0);
			if($valid_status==1){
				$orderStatus = I('orderstatus', OrdersModel::ORDER_RETURNED);
			}else {
				$orderStatus = I('orderstatus', OrdersModel::ORDER_RECEIPT_OF_GOODS);
			}
			$params = array();
			$map = array();
			if (!empty($orderid)) {
				$map['orderid'] = array('like', $orderid . '%');
				$params['orderid'] = $orderid;
			}
			//$map['wxaccountid'] = getWxAccountID();
			$map['order_status'] = 9;
			$map['pay_status'] = OrdersModel::ORDER_PAID;
			
			$params['order_status'] = $orderStatus;
			$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
			$order = " createtime desc ";

			if ($userid > 0) {
				$map['uid'] = $userid;
			}

			//
			$result = apiCall(OrdersInfoViewApi::QUERY, array($map, $page, $order, $params));

			$map=array(
				'valid_status'=>$valid_status
			);

			$tui=apiCall(OrderRefundApi::QUERY,array($map));
			$this->assign('ths',$tui['info']['list']);
			//
			if ($result['status']) {
				$this -> assign('orderid', $orderid);
				$this -> assign('orderStatus', $orderStatus);
				$this -> assign('valid_status', $valid_status);
				$this -> assign('show', $tui['info']['show']);
				$this -> assign('list', $result['info']['list']);
				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
//		} elseif (IS_POST) {
//			
//		}
	}
	
	/**
	 * 单个退货操作
	 */
	public function returnGoods(){
		if(IS_GET){
			$id = I('get.id',0);
			$thid = I('get.thid',0);
			$this->assign("thid",$thid);
			$this->assign("id",$id);
			$this->display();
		}else{
			$id = I('id',0);
			$thid=I('thid',0);
			$entity=array('valid_status'=>1,'reply_msg'=>I('note','无'));
			$map=array('order_status'=>6);
			$result = apiCall(OrdersApi::SAVE_BY_ID,array($id,$map));
			if($result['status']){
				$resu=apiCall(OrderRefundApi::SAVE_BY_ID,array($thid,$entity));
				$this->success("操作成功！",U('Admin/Orders/returned'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	/*
	 * 修改订单信息
	 * */
	public function editorder(){
		if(IS_GET){
			$id = I('get.id',0);
			$price = I('get.price',0);
			$this->assign("id",$id);
			$this->assign("price",$price);
			$this->display();
		}else{
			$id = I('id',0);
			$entity=array('price'=>I('price','0.00'),'post_price'=>I('postprice','0.00'));
			$resu=apiCall(OrdersApi::SAVE_BY_ID,array($id,$entity));
			if($resu['status']){
				$this->success("操作成功！",U('Admin/Orders/sure'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	/**
	 * 驳回退货申请
	 */
	public function returnGoodsbh(){
		if(IS_GET){
			$id = I('get.id',0);
			$thid = I('get.thid',0);
			$this->assign("thid",$thid);
			$this->assign("id",$id);
			$this->display();
		}else{
			$id = I('id',0);
			$thid=I('thid',0);
			$entity=array('valid_status'=>2,'reply_msg'=>I('note','无'));
			$resu=apiCall(OrderRefundApi::SAVE_BY_ID,array($thid,$entity));
			if($resu['status']){
				$this->success("操作成功！",U('Admin/Orders/returned'));
			}else{
				$this->error($result['info']);
			}
		}
	}

	
	/**
	 * 批量发货
	 * TODO:批量发货
	 */
	public function bulkDeliver(){
		$this->error("功能开发中...");
	}

	/**
	 * 批量确认订单
	 */
	public function bulkSure() {
		if (IS_POST) {

			$ids = I('post.ids', -1);
			if ($ids === -1) {
				$this -> error(L('ERR_PARAMETERS'));
			}

			
			foreach($ids as $id){
				$result = apiCall(OrderStatusApi::CONFIRM_ORDER, array($id , false , UID));
				if (!$result['status']) {
					$this -> error($result['info']);
				}
			}
			
			
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/Orders/sure'));
			} else {
				$this -> error($result['info']);
			}
		}
	}


	private function sendTextTo($wxuserid,$text){
		//
		$wxaccountid = getWxAccountID();
		$result = apiCall(WxuserApi::GET_INFO,array(array("id"=>$wxuserid)));
		$wxaccount = apiCall(WxaccountApi::GET_INFO, array(array("id"=>$wxaccountid)));
		$openid = "";
		if($result['status'] && is_array($result['info'])){
			$openid = $result['info']['openid'];
		}
		if($wxaccount['status'] && is_array($wxaccount['info'])){
			$appid =  $wxaccount['info']['appid'];
			$appsecret =  $wxaccount['info']['appsecret'];				
			$wxapi = new WeixinApi($appid,$appsecret);
			$wxapi->sendTextToFans($openid, $text);
			$wxapi->sendTextToFans($openid, $text);//发2次
		}
	}

}
