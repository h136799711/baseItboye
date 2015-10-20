<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/10/14
 * Time: 11:49
 */

namespace Api\Controller;


use Admin\Api\ConfigApi;

class ConfigController extends ApiController{

    protected $business_code = "";

    public function app(){

        $notes = "客户端" . $this->client_id . "，调用APP配置查询接口";
        addLog("Config/app", $_GET, $_POST, $notes);

        $group = 6;//6是接口参数

        $result = apiCall(ConfigApi::QUERY_NO_PAGING,array(array('group'=>$group)));
        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }

        $this->apiReturnSuc($this->simpleResult($result['info']));

    }

    private function simpleResult($result){
        $simpleResult = array();
        foreach($result as $vo){
            array_push($simpleResult,array('name'=>$vo['name'],'value'=>$vo['value']));
        }
        return $simpleResult;
    }

}