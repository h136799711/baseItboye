<?php
namespace Api\Controller;

use Shop\Api\BannersApi;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/8
 * Time: 13:42
 */
class BannersController extends ApiController{

    function getSupportMethod(){

    }

    /**
     * 不分页查询
     */
    public function queryNoPaging()
    {
        $notes = "应用" . $this->client_id . "，调用Banners不分页查询接口";
        addLog("Banners/queryNoPaging", $_GET, $_POST, $notes);
        // dump(getDatatree('SHOP_INDEX_BANNERS'));
        $postion = I('position', 18);
        $map = array(
            'position' => $postion,
        );
        $result = apiCall(BannersApi::QUERY_NO_PAING, array($map));
        if ($result['status']) {
            $this->apiReturnSuc($result['info']);
        } else {
            $this->apiReturnErr("暂无信息");
        }


    }

    /**
     * 广告分页查询
     * position广告位置
     * pageNo页码
     * pageSize 显示个数
     */
    public function query(){
        $notes = "应用".$this->client_id."，调用Banners分页查询接口";
        addLog("Banners/query",$_GET,$_POST,$notes);
        $postion = I('position', 18);
        $map=array(
            'position' => $postion,
        );

        $page = array('curpage'=>I('pageNo',0),'size'=>I('pageSize',10)); //分页

        $result=apiCall(BannersApi::QUERY,array($map,$page));
        if($result['status']){
            $this->apiReturnSuc($result['info']['list']);
        }else{
            $this->apiReturnErr("暂无广告");
        }
    }



}