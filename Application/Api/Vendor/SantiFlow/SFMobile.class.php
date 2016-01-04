<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/31
 * Time: 11:47
 */

namespace Api\Vendor\SantiFlow;


class SFMobile extends SFBase {

    /**
     * 查询手机的信息
     *
     * @param $customer
     * @return array
     */
    public function getInfo($customer){
        $uri = '/c/Mobile/getMobileInfo';

        $data = array(
            'appkey'=>$this->getAppKey(),
            'customer'=>$customer,
            'timestamp'=>time(),
        );

        $result = $this->getRequest($data,$uri);
        return $result;
    }

}