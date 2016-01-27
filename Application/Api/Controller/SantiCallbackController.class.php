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

/*
 * {"channelOrderNo":"158581990641014538769441118","extraData":"",
 * "orderNo":"1453876701223039",
 * "orderStatus":"4","providerResultCode":"1",
 * "providerResultReason":"\u8ba2\u8d2d\u6210\u529f",
 * "resultCode":"1000","resultMessage":"\u6210\u529f",
 * "sign":"8194b64649a34e8619a74c76c5ef436e"}*/
        addLog("Santi/callback",$_GET,$_POST,'[调试]三体回调地址');
//        exit;

        $order_no = $this->_get('orderNo','','缺失orderNo参数');
        $order_status = $this->_get('orderStatus','');
        $channel_order_no = $this->_get('channelOrderNo','');
        $result_code = $this->_get('resultCode','');
        $result_message = $this->_get('resultMessage','');
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
            'extra_data'=>$extraData,
            'sign'=>$sign,
            'create_time'=>time(),
        );


        $update_entity = array(
            'order_status'=>$order_status,
        );

        $result = apiCall(SantiCallbackApi::ADD,array($entity));

       $result = apiCall(SantiOrderApi::SAVE,array(array('order_no'=>$order_no),$update_entity));


        if($result['status']){

            echo "SUCCESS";
        }else{
            echo "FAIL";
        }

    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _get($key,$default='',$emptyErrMsg=''){
        $value = I("get.".$key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            echo "FAIL";
            exit;
        }
        return $value;
    }
}