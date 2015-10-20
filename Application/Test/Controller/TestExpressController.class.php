<?php
namespace Test\Controller;


use Api\Service\OAuth2ClientService;
use Think\Controller\RestController;
use Uclient\Model\OAuth2TypeModel;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/7
 * Time: 13:54
 */
class TestExpressController extends RestController{
    public function __construct(){
        parent::__construct();

        $client_id = C('CLIENT_ID');
        $client_secret = C('CLIENT_SECRET');
        $config = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
        );
        $client = new OAuth2ClientService($config);
        $access_token = $client->getAccessToken();
//        dump($access_token);
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }

        $this->assign("error",$access_token);
    }


    public function searchExp(){
        $this->display();
    }

}