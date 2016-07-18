<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/1/4
 * Time: 14:25
 */

namespace Admin\Controller;


use Api\Vendor\SantiFlow\SFMobile;
use Api\Vendor\SantiFlow\SFOrder;
use Api\Vendor\SantiFlow\SFProduct;
use Common\Api\BoyeServiceApi;
use Santi\Api\OrderCallbackApi;
use Santi\Api\SantiOrderApi;
use Santi\Model\OrderCallbackModel;
use Santi\Model\SantiOrder;
use Uclient\Api\UserApi;

class SantiController extends AdminController {

    public function order_list(){

        $mobile = I('post.mobile','');

        $map = array();
        $params= array();
        if(!empty($mobile)){
            $map['mobile'] = $mobile;
            $params['mobile']= $mobile;
        }

        $page = array('curpage'=>I('param.p',0),'size'=>10);
        $order = 'create_time desc';

        $result = apiCall(OrderCallbackApi::QUERY,array($map,$page,$order));
        if($result['status']){
            $this->assign("list",$result['info']['list']);
            $this->assign("show",$result['info']['show']);
        }
        $this->assign("mobile",$mobile);
        $this->display();
    }

    public function mobile_info(){
        $mobile = I('get.mobile','');
        $santi = new SFMobile();

        $result = $santi->getInfo($mobile);
//        dump($result);
        if($result['status']){
            $this->assign('mobile_info',$result['info']);
        }
        $this->display();
    }


    /**
     * 产品
     */
    public function products(){


        $request = new SFProduct();
        $carrier = 0;

        $result = $request->getProductList(1,20,$carrier);

        if(!$result['status']){
            addLog("products",$result,$_POST,"[getProductList]");
            $this->error($result['info']);
        }

        $prodList = $result['info']['prodList'];
        $resultCode = $result['info']['resultCode'];
        $resultReason = $result['info']['resultReason'];
        if($resultCode == '1000'){

            $this->assign('prod_list',$prodList);

        }else{
            addLog("products",$resultReason,$resultReason,"[getProductList result]");
            $this->error($resultReason);
        }

        $request = new SFOrder();

        $result = $request->queryBalance();

        if($result['status']){
            $this->assign('channel',$result['info']);
        }

        $this->assign("appkey",$request->getAppKey());

        $this->display();
    }

    public function create_order(){

        $channelOrderNo = I('get.channelOrderNo','');
        $prodPayType = I('get.prodPayType','');
        $prodValue = I('get.prodValue','');
        $prodId = I('get.prodId','');

        if(IS_GET){
            $map = array(
                'prod_id'=>$prodId,
            );
            $page = array('curpage'=>I('param.p',0),'size'=>10);
            $order = 'create_time desc';
            $result = apiCall(SantiOrderApi::QUERY,array($map,$page,$order));
            if($result['status']){
                $this->assign("list",$result['info']['list']);
                $this->assign("show",$result['info']['show']);
            }
            $this->display();
        }else{

            $uid = I('post.uid','');
            if(empty($uid) || $uid < 0){
                $this->error('用户ID非法');
            }

            $data = array(
                'channelOrderNo'=>$channelOrderNo,
                'prodPayType'=>$prodPayType,
                'prodValue'=>$prodValue,
                'prodId'=>$prodId,
                'uid'=>$uid,
            );

            $service = new BoyeServiceApi();

            $result = $service->callRemote('Santi/submit_order',$data);

            if($result['status']){
                $info = $result['info'];
                if(!is_array($info)){
                    $this->error($info);
                }
                if(isset($info['code']) && $info['code'] == 0){
                    $this->success($info['data']);
                }else{
                    $this->error($info['data']);
                }
            }else{
                $this->error($result['info']);
            }

        }
    }
    /**
     *
     * 创建订单
     */
//    public function create_order1(){
//
//        $channelOrderNo = I('get.channelOrderNo','');
//        $prodPayType = I('get.prodPayType','');
//        $prodValue = I('get.prodValue','');
//        $prodId = I('get.prodId','');
//
//        if(IS_GET){
//            $map = array(
//                'prod_id'=>$prodId,
//            );
//            $page = array('curpage'=>I('param.p',0),'size'=>10);
//            $order = 'create_time desc';
//            $result = apiCall(SantiOrderApi::QUERY,array($map,$page,$order));
//            if($result['status']){
//                $this->assign("list",$result['info']['list']);
//                $this->assign("show",$result['info']['show']);
//            }
//            $this->display();
//        }else{
//
//            $uid = I('post.uid','');
//            if(empty($uid) || $uid < 0){
//                $this->error('用户ID非法');
//            }
//            $result = apiCall(UserApi::GET_INFO,array($uid));
//            if(!$result['status']){
//                $this->error($result['info']);
//            }
//            if(is_null($result['info'])){
//                $this->error('获取用户信息失败，无法发送流量包!');
//            }
//            $mobile = $result['info']['mobile'];
//            $mobile = trim($mobile);
//            if(empty($mobile) || strlen($mobile) != 11){
//                $this->error($mobile.'手机号不合法!');
//            }
//
//            $santi = new SFOrder();
//
//            if(empty($prodId)){
//                $this->error('缺少产品ID');
//            }
//            $channelOrderNo = date('YmdHis',time()).rand(100,999);
//            $result = $santi->createOrder($prodId,$mobile,$prodPayType,$channelOrderNo);
//
//            $orderNo = '';
//            $orderStatus = '';
//            $createOrderTime = '';
//            $err_msg = '';
//            if(!$result['status']){
//                $err_msg = $result['info'];
//            }
//            $info = $result['info'];
//
//            if(empty($err_msg) && isset($info['resultCode'])){
//                $resultCode = $info['resultCode'];
//                $resultReason = $info['resultReason'];
//                if($resultCode == '1000'){
//                    $orderNo = $info['orderNo'];
//                    $orderStatus = $info['orderStatus'];
//                    $createOrderTime = $info['createOrderTime'];
//                }else{
//                    $err_msg = $resultReason;
//                }
//            }
//
//            if(!empty($err_msg)){
//                $this->error($err_msg);
//            }
//
//
//            $update_time = strtotime($createOrderTime);
//
//            $entity = array(
//                'create_time'=>time(),
//                'update_time'=>$update_time,
//                'order_status'=>$orderStatus,
//                'order_no'=>$orderNo,
//                'mobile'=>$mobile,
//                'prod_id'=>$prodId,
//                'prod_pay_type'=>$prodPayType,
//                'channel_order_no'=>$channelOrderNo,
//            );
//
//
//            $result = apiCall(SantiOrderApi::ADD,array($entity));
//
//            if(!$result['status']){
//
//                $this->error($result['info']);
//            }
//            $this->submit($orderNo);
//            $this->success('操作成功!');
//
//        }
//    }
//
//    public function submit($order_no=''){
//
//        if(empty($order_no)){
//            $order_no = I('get.order_no','');
//        }
//
//        $request = new SFOrder();
//
//        $result = $request->submit($order_no);
//
//        if(!$result['status']){
//            $this->error($result['info']);
//        }
//
//        $info = $result['info'];
//
//        $result_code = $info['resultCode'];
//
//        $result_reason = $info['resultReason'];
//        $order_status = $info['orderStatus'];
//        $order_success_time = time();
//
//        if($result_code == '1000'){
//
//
//            $entity = array(
//                'order_status'=>$order_status,
//                'update_time'=>$order_success_time,
//            );
//
//            $result = apiCall(SantiOrderApi::SAVE,array(array('order_no'=>$order_no),$entity));
//            if($result['status']){
//                $this->success('提交成功！');
//            }else{
//                $this->error($result['info']);
//            }
//        }else{
//            $this->error($result_reason);
//        }
//
//    }

}