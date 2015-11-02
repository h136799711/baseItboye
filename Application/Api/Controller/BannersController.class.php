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
        $arr = array(
            'app_index'=>6007,
            'app_finance'=>6008,
            'app_carousel'=>6009,
            'app_life'=>6010,
        );
        $position = $this->_post('position', '',"位置参数必须");

        if(!isset($arr[$position])){
            $this->apiReturnErr("不支持的位置参数!");
        }
        $position = $arr[$position];
        $curpage = $this->_post('curpage',0);
        $pagesize = $this->_post('pagesize',10);

        $map=array(
            'position' => $position,
        );
        $order = " sort asc ";
        $page = array('curpage'=>$curpage,'size'=>$pagesize); //分页

        $result=apiCall(BannersApi::QUERY,array($map,$page));

        if($result['status']){
            $list = $result['info']['list'];

            $list = $this->convertImgUrl($list);

            $this->apiReturnSuc($list);
        }else{
            $this->apiReturnErr("没有相关数据!");
        }
    }


    private function convertImgUrl($list){

        foreach($list as &$vo){
            $vo['img_url'] = getImageUrl($vo['img']);
            unset($vo['img']);
            unset($vo['id']);
            unset($vo['notes']);
            unset($vo['uid']);
            unset($vo['storeid']);
            unset($vo['createtime']);
            unset($vo['starttime']);
            unset($vo['endtime']);
            unset($vo['noticetime']);

        }


        return $list;

    }


}