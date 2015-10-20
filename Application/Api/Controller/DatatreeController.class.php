<?php
namespace Api\Controller;
use Admin\Api\DatatreeApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/22
 * Time: 9:48
 */
class DatatreeController extends ApiController{
    function getSupportMethod(){

    }

    /**
     * 不分页查询
     * parentId 父项ID
     */
    public function queryNoPaging(){

        $notes = "应用" . $this->client_id . "，调用数据字典不分页查询接口";
        addLog("Datatree/queryNoPaging", $_GET, $_POST, $notes);
        $parentId=I('parentId',0);
        if($parentId==0){
            $this->apiReturnErr("父类ID不能为0或空");
        }
        $map=array(
            'parentId'=>$parentId
        );
        $result= apiCall(DatatreeApi::QUERY_NO_PAGING,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("暂无信息");
        }
    }



}