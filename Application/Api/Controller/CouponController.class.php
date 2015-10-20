<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/10/11
 * Time: 14:22
 */

namespace Api\Controller;


use Admin\Api\MgroupApi;
use Admin\Api\UidMgroupApi;
use Admin\Controller\MemberGroupController;
use Shop\Api\MemberConfigApi;
//COUPON = 001
class CouponController extends ApiController{

    protected $business_code = '100';

    public function info(){

        if(IS_GET){
            $this->apiReturnErr("不支持GET请求!",$this->business_code.'03');
        }

        $notes = "客户端" . $this->client_id . "，调用优惠码查询接口";
        addLog("Coupon/info", $_GET, $_POST, $notes);

        $idcode = $this->_post('idcode','',"优惠码缺失");

        $map = array(
            'IDCode'=>$idcode
        );

        $result = apiCall(MemberConfigApi::GET_INFO,array($map));

        if(!$result['status']){
            $this->apiReturnErr($result['info'],$this->business_code.'01');
        }

        if(is_null($result['info'])){
            $this->apiReturnErr("优惠码不存在",$this->business_code.'02');
        }

        $uid = $result['info']['uid'];


        $result = apiCall(UidMgroupApi::QUERY_WITH_UID,array($uid));

        if(!$result['status']){
            $this->apiReturnErr($result['info'],$this->business_code.'03');
        }


        $this->apiReturnSuc($result['info']);

    }

}