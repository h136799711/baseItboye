<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:48
 */

namespace Api\Vendor\SantiFlow;


class SFOrder extends SFBase {

    /**
     * 创建订单(根据面值)
     * @param string $prodValue  充值的产品面值（如500,50,10，1024）
     * @param string $mobile 手机号码
     * @param int $prodPayType 付费方式（0：预付费、1：后付费、2：只能通过接口充值的产品，不开通平台手工充值功能）
     * @param string $channelOrderNo 渠道商订单号（可选）校验渠道商订单号唯一性，没有可不传参
     */
    public function createOrderWithProdValue($prodValue,$mobile,$prodPayType,$channelOrderNo){
        $uri = "/r/Channel/createOrderWithProdValue";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'prodValue'=>$prodValue,
            'customer'=>$mobile,
            'prodPayType'=>$prodPayType,
            'notifyUrl'=>$this->getNotifyUrl(),
            'channelOrderNo'=>$channelOrderNo,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * 创建订单
     * @param $id
     * @param $mobile
     * @param $prodPayType
     * @param string $channelOrderNo
     * @return array
     */
    public function createOrder($id,$mobile,$prodPayType,$channelOrderNo=''){
        $uri = "/r/Channel/createOrder";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'prodId'=>$id,
            'customer'=>$mobile,
            'prodPayType'=>$prodPayType,
            'notifyUrl'=>$this->getNotifyUrl(),
            'channelOrderNo'=>$channelOrderNo,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * @param $orderNo
     * @return array resultCode	int 	错误码
     *               resultReason	String	错误信息
     *               orderStatus	int	订单状态
     *               orderSuccessTime	String	"YYYMMDDHHMMSS 订单成功时间"
     *
     * @internal param 付费方式0为预付费 $payType 、1为后付费、2只能通过接口充值的产品，不开通平台手工充值功能
     * @internal param string $channelOrderNo
     * @internal param 产品ID $id
     *
     */
    public function submit($orderNo){

        $uri = "/r/Channel/submitOrder";

        $data = array(
            'appkey'=>$this->getAppKey(),
            'orderNo'=>$orderNo,
            'timestamp'=>time()
        );

        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * 查询订单接口作为通知接口的补充，代理商未收到支付结果通知时，可以调用查询接口获取订单当前的状态。
     * 返回参数中resultCode如果为1000，则可根据Order结构中的orderStatus判断当前状态。
     * @param $orderNo
     * @return array
     */
    public function query($orderNo){
        $uri = "/r/Channel/queryOrder";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'orderNo'=>$orderNo,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * 查询余额
     * @return array    totalFee	double	总金额
                        balance	double	余额
                        consumeFee	double	已消费金额
                        incomeFee	double	佣金余额
     */
    public function queryBalance(){
        $uri = "/r/Channel/queryBalance";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'orderNo'=>$orderNo,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }
}