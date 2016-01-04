<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:48
 */

namespace Api\Vendor\SantiFlow;


class SFProduct extends SFBase {

    /**
     * @param int $page
     * @param int $pageSize
     * @param int $carrierId    5、运营商ID说明（carrierId）
                                0	未知
                                1	移动
                                2	电信
                                3	联通
     * @param int $areaId
     * @return array
     */
    public function getProductList($page=1,$pageSize=10,$carrierId=1,$areaId=2){
        $uri = "/c/Product/getProductList";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'carrierId'=>$carrierId,
            'areaId'=>$areaId,
            'page'=>$page,
            'pageSize'=>$pageSize,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * @param $id  产品ID
     * @param $payType  付费方式0为预付费、1为后付费、2只能通过接口充值的产品，不开通平台手工充值功能
     * @return array
     */
    public function getProduct($id,$payType){
        $uri = "/c/Product/queryProduct";
        $data = array(
            'appkey'=>$this->getAppKey(),
            'prodId'=>$id,
            'prodPayType'=>$payType,
            'pageSize'=>$pageSize,
            'timestamp'=>time()
        );
        $result = $this->getRequest($data,$uri);
        return $result;
    }

    /**
     * 地域
     * 0   未知
    1   全国
    2   浙江
    3   广西
    4   重庆
    5   北京
    6   上海
    7   天津
    8   江苏
    9   广东
    10  河北
    11  山西
    12  黑龙
    13  河南
    14  辽宁
    15  吉林
    16  内蒙
    17  福建
    18  山东
    19  安徽
    20  江西
    21  湖北
    22  湖南
    23  海南
    24  四川
    25  贵州
    26  云南
    27  西藏
    28  陕西
    29  甘肃
    30  宁夏
    31  青海
    32  新疆
     */

}