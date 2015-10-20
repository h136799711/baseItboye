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
     * ����ҳ��ѯ
     * parentId ����ID
     */
    public function queryNoPaging(){

        $notes = "Ӧ��" . $this->client_id . "�����������ֵ䲻��ҳ��ѯ�ӿ�";
        addLog("Datatree/queryNoPaging", $_GET, $_POST, $notes);
        $parentId=I('parentId',0);
        if($parentId==0){
            $this->apiReturnErr("����ID����Ϊ0���");
        }
        $map=array(
            'parentId'=>$parentId
        );
        $result= apiCall(DatatreeApi::QUERY_NO_PAGING,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("������Ϣ");
        }
    }



}