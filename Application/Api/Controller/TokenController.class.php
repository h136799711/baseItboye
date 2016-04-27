<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 15:04
 */
namespace Api\Controller;

use Api\Service\OAuth2Service;
use Api\Vendor\crypt\DesCrypt;
use OAuth2\Request;
use Think\Controller\RestController;
use Think\Log;

class TokenController extends RestController{

    protected $allowMethod = array('get','post');

    // REST允许请求的资源类型列表
    protected   $allowType      =   array('json');

    public function index(){
        addLog("Token/index",$_GET,$_POST,"");
        $grant_type = I('get.grant_type','');
        if(empty($grant_type)){
            $grant_type = I('post.grant_type','');
        }
        if(empty($grant_type)){
            $this->apiReturnSuc("无效的Token获取类型!");
        }

        $client_id = I('get.client_id','');
        if(empty($client_id)){
            $client_id = I('post.client_id','');
        }

        $client_secret = I('get.client_secret','');
        if(empty($client_secret)){
            $client_secret = I('post.client_secret','');
        }

        if(empty($client_id)
            || empty($grant_type) || empty($client_secret)){

            $this->ajaxReturn(array('code'=>-1,'data'=>$grant_type."参数缺失!".$client_id),"json");
        }

        $notes = $client_id."调用接口";

        $_POST['grant_type'] = $grant_type;
        $_POST['client_id'] = $client_id;
        $_POST['client_secret'] = $client_secret;
        unset($_GET['client_id']);
        unset($_GET['client_secret']);
        unset($_GET['grant_type']);
        addLog("Token/index",$_GET,$_POST,"");

        $this->credentials($grant_type);
    }


    private function credentials($type){
        $api = new OAuth2Service();
        $server = $api->init($type);
        $req = Request::createFromGlobals();
        $result = $server->handleTokenRequest($req);
        $params = $result->getParameters();
        if($result->getStatusCode() != 200){
            $this->ajaxReturn(array('code'=>$result->getStatusCode(),'data'=>$params),"json");
        }else{
            $this->ajaxReturn(array('code'=>0,'data'=>$params),"json");
        }

    }


}