<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/1/26
 * Time: 16:51
 */

namespace Api\Vendor\SantiFlow;
use Santi\Api\SantiOrderApi;


/**
 * 流量赠送
 * Class SFFlowFacade
 * @package Api\Vendor\SantiFlow
 */
class SFFlowFacade {



    /**
     * 是否移动号码
     * @param $mobile
     * @return bool
     */
    public function is10086($mobile){
        return $this->getCarrier($mobile,1);
    }

    /**
     * 是否电信号码
     * @param $mobile
     * @return bool
     */
    public function is10000($mobile){
        return $this->getCarrier($mobile,2);
    }

    /**
     * 是否联通号码
     * @param $mobile
     * @return bool
     */
    public function is10010($mobile){
        return $this->getCarrier($mobile,3);
    }

    /**
     * 获取手机运营商
     * @param $mobile
     * @param $carrier
     * @return bool
     */
    public function getCarrier($mobile,$carrier){
        $result = $this->getMobileInfo($mobile);
        if($result['status']){
            $info = $result['info'];
            if($info['carrierId'] == $carrier){
                return true;
            }
        }

        return false;
    }

    public function getMobileInfo($mobile){
        $sf = new SFMobile();

        $result = $sf->getInfo($mobile);
        if($result['status']){

            $info = $result['info'];

            if($info['resultCode'] == 1000){
                $mobile_info =  $info;
                unset($mobile_info['resultCode']);
                unset($mobile_info['resultReason']);
                return array('status'=>true,'info'=>$mobile_info);
            }else{
                return array('status'=>false,'info'=>$info['resultReason']);
            }

        }else{
            return $result;
        }
    }

    /**
     * 创建流量订单，并提交
     * @param $mobile
     * @param $flow
     * @return array
    'orderNo'=>$info['orderNo'],
    'channelOrderNo'=>$channelOrderNo,
    'prodPayType'=>$prodPayType,
     * orderStatus
     */
    public function createAndSubmit($mobile,$flow){
        $result = $this->create($mobile,$flow);

        if($result['status']){
            $info = $result['info'];
            $orderNo = $info['orderNo'];
            $channel_order_no = $info['channelOrderNo'];
            $result = $this->submit(($orderNo));
            if($result['status']){
                $info['orderStatus'] = $result['info'];
                 $data = array(
                     'create_time'=>time(),
                     'order_status'=>$info['orderStatus'],
                     'order_no'=>$orderNo,
                     'mobile'=>$mobile,
                     'prod_id'=>$flow.'M',//
                     'prod_pay_type'=>0,
                     'channel_order_no'=>$channel_order_no,
                     'update_time'=>time(),
                 );

                $api = new SantiOrderApi();
                $result = $api->add($data);

                return array('status'=>true,'info'=>$info);
            }
            return $result;
        }

        return $result;

    }

    public function submit($orderNo){

        $order = new SFOrder();
        $result = $order->submit($orderNo);
        if($result['status']){
            $info = $result['info'];

            if($info['resultCode'] == 1000){
                return array('status'=>true,'info'=>$info['orderStatus']);
            }else{
                return array('status'=>false,'info'=>$info['resultReason']);
            }

        }else{
            return $result;
        }
    }

    public function create($mobile,$flow,$prodPayType=0){
        //推荐人 10m 联通是20m 使用推荐码成功注册 200m   未使用推荐码自己注册送100m

        $order = new SFOrder();
        $channelOrderNo = $mobile.$flow.time().rand(1000,3000);
        $result = $order->createOrderWithProdValue($flow,$mobile,$prodPayType,$channelOrderNo);
        if($result['status']){

            $info = $result['info'];

            if($info['resultCode'] == 1000){
                $entity = array(
                    'orderNo'=>$info['orderNo'],
                    'channelOrderNo'=>$channelOrderNo,
                    'prodPayType'=>$prodPayType,
                );

                //0 订单未提交 1 准备充值 2 订单取消3充值中 4充值成功 5充值失败
                return array('status'=>true,'info'=>$entity);

            }else{
                return array('status'=>false,'info'=>$info['resultReason']);
            }

        }else{
            return $result;
        }
    }
}