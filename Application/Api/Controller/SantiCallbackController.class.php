<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/1/4
 * Time: 16:59
 */

namespace Api\Controller;


use Santi\Api\SantiCallbackApi;
use Santi\Api\SantiOrderApi;

use Think\Controller\RestController;

class SantiCallbackController extends RestController {


    /**
     * 流量购回调地址
     */
    public function index(){


        addLog("Santi/callback",$_GET,$_POST,'[调试]三体回调地址');

        $order_no = $this->_get('orderNo','','缺失orderNo参数');
        $order_status = $this->_get('orderStatus','');
        $channel_order_no = $this->_get('channelOrderNo','');
        $result_code = $this->_get('resultCode','');
        $result_message = $this->_get('orderNo','');
        $provider_result_code = $this->_get('providerResultCode','');
        $provider_result_reason = $this->_get('providerResultReason','');
        $extraData = $this->_get('extraData','');
        $sign = $this->_get('sign','','缺失sign参数');



        $entity = array(
            'order_no'=>$order_no,
            'order_status'=>$order_status,
            'channel_order_no'=>$channel_order_no,
            'result_code'=>$result_code,
            'result_message'=>$result_message,
            'provider_result_code'=>$provider_result_code,
            'provider_result_reason'=>$provider_result_reason,
            'extraData'=>$extraData,
            'sign'=>$sign,
            'create_time'=>time(),
        );

        $update_entity = array(
            'order_status'=>$order_status,
        );

       apiCall(SantiOrderApi::SAVE,array(array('order_no'=>$order_no),$update_entity));



        $result = apiCall(SantiCallbackApi::ADD,array($entity));

        if($result['status']){

            echo "SUCCESS";
        }else{
            echo "FAIL";
        }

    }

}