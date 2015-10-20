<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/7
 * Time: 09:43
 */

namespace Api\Controller;

use Api\Service\OAuth2Service;
use OAuth2\Request;
use Think\Controller\RestController;

class ResourceController extends RestController{


    protected $allowMethod = array('get','post');

    // REST允许请求的资源类型列表
    protected   $allowType      =   array('json');

    public function authorize(){

        $api = new OAuth2Service();
        $server = $api->init(OAuth2Service::ALL);
        if (!$server->verifyResourceRequest(Request::createFromGlobals())){

            $resp = $server->getResponse();
            $params = $resp->getParameters();

            return array('status'=>$resp->getStatusCode(),'info'=>$params['error_description']);//,"json");
        }

        return array('status'=>0,'info'=>'你通过了Api的验证');//,"json");
    }

    function getSupportMethod()
    {
        // TODO: Implement getSupportMethod() method.
    }
}