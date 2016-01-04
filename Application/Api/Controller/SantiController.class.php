<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:11
 */

namespace Api\Controller;

use Api\Vendor\SantiFlow\SFOrder;
use Santi\Api\SantiCallbackApi;
use Santi\Api\SantiOrderApi;
use Uclient\Api\UserApi;

class SantiController extends ApiController {



    public function submit_order(){
        addLog("Santi/submit_order",$_GET,$_POST,'[调试]三体订单提交地址');

        $channelOrderNo = $this->_post('channelOrderNo','');
        $prodPayType = $this->_post('prodPayType','');
        $prodValue = $this->_post('prodValue','');
        $prodId = $this->_post('prodId','');
        $uid = $this->_post('uid','','用户ID缺失');

        if(empty($uid) || $uid < 0){
            $this->apiReturnErr('用户ID非法');
        }
        $result = apiCall(UserApi::GET_INFO,array($uid));
        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }
        if(is_null($result['info'])){
            $this->apiReturnErr('获取用户信息失败，无法发送流量包!');
        }
        $mobile = $result['info']['mobile'];
        $mobile = trim($mobile);
        if(empty($mobile) || strlen($mobile) != 11){
            $this->apiReturnErr($mobile.'手机号不合法!');
        }

        $santi = new SFOrder();

        if(empty($prodId)){
            $this->error('缺少产品ID');
        }
        $channelOrderNo = date('YmdHis',time()).rand(100,999);

        if(empty($prodValue)){
            $result = $santi->createOrder($prodId,$mobile,$prodPayType,$channelOrderNo);
        }else{
            $result = $santi->createOrderWithProdValue($prodValue,$mobile,$prodPayType,$channelOrderNo);
        }

        $orderNo = '';
        $orderStatus = '';
        $createOrderTime = '';
        $err_msg = '';
        if(!$result['status']){
            $err_msg = $result['info'];
        }
        $info = $result['info'];

        if(empty($err_msg) && isset($info['resultCode'])){

            $resultCode = $info['resultCode'];
            $resultReason = $info['resultReason'];

            if($resultCode == '1000'){
                $orderNo = $info['orderNo'];
                $orderStatus = $info['orderStatus'];
                $createOrderTime = $info['createOrderTime'];
            }else{
                $err_msg = $resultReason;
            }
        }

        if(!empty($err_msg)){
            $this->apiReturnErr($err_msg);
        }

        $update_time = strtotime($createOrderTime);

        $entity = array(
            'create_time'=>time(),
            'update_time'=>$update_time,
            'order_status'=>$orderStatus,
            'order_no'=>$orderNo,
            'mobile'=>$mobile,
            'prod_id'=>$prodId,
            'prod_pay_type'=>$prodPayType,
            'channel_order_no'=>$channelOrderNo,
        );

        $result = apiCall(SantiOrderApi::ADD,array($entity));

        if(!$result['status']){

            $this->apiReturnErr($result['info']);
        }

        $this->submit($orderNo);

    }

    private function submit($order_no=''){

        if(empty($order_no)){
            $order_no = I('get.order_no','');
        }

        $request = new SFOrder();

        $result = $request->submit($order_no);

        if(!$result['status']){
            $this->error($result['info']);
        }

        $info = $result['info'];

        $result_code = $info['resultCode'];

        $result_reason = $info['resultReason'];
        $order_status = $info['orderStatus'];
        $order_success_time = time();

        if($result_code == '1000'){


            $entity = array(
                'order_status'=>$order_status,
                'update_time'=>$order_success_time,
            );

            $result = apiCall(SantiOrderApi::SAVE,array(array('order_no'=>$order_no),$entity));
            if($result['status']){
                $this->apiReturnSuc('提交成功！');
            }else{
                $this->apiReturnErr($result['info']);
            }
        }else{
            $this->apiReturnErr($result_reason);
        }

    }

}