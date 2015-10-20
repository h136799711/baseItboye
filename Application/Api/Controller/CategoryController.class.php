<?php
namespace Api\Controller;
use Shop\Api\CategoryApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/8
 * Time: 14:55
 */
class CategoryController extends ApiController{

    function getSupportMethod(){

    }

  /**
   * 不分页查询,通用类目查询
   * parentId 父项ID
   */
    public function queryNoPaging(){

      $notes = "应用" . $this->client_id . "，调用类目不分页查询接口";
      addLog("Category/queryNoPaging", $_GET, $_POST, $notes);
      $parent=I('parentId',0);
      if($parent==""){
        $parent=0;
      }
      $map=array(
        'parent'=>$parent
      );
      $result= apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
      if($result['status']){
        $this->apiReturnSuc($result['info']);
      }else{
        $this->apiReturnErr("暂无信息");
      }
    }


  /**
   * 分页类目查询
   * parentId 父项ID
   * pageNo 页码
   * pageSize 显示个数
   */
  public function query(){

      $notes = "应用" . $this->client_id . "，调用类目分页查询接口";
      addLog("Category/query", $_GET, $_POST, $notes);
      $parent=I('parentId',0);
      if($parent==""){
        $parent=0;
      }
      $map=array(
          'parent'=>$parent
      );
      $page = array('curpage'=>I('pageNo',0),'size'=>I('pageSize',10)); //分页

      $result=apiCall(CategoryApi::QUERY,array($map,$page));
      if($result['status']){
        $this->apiReturnSuc($result['info']['list']);
      }else{
        $this->apiReturnErr("暂无信息");
      }
  }

}