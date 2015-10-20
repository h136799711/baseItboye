<?php
namespace Test\Controller;

use Think\Controller\RestController;
use Api\Service\OAuth2ClientService;
use Uclient\Model\OAuth2TypeModel;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/24
 * Time: 9:38
 */
class TestShoppingCartApiController extends RestController{
    public function __construct(){
        parent::__construct();

        $client_id = C('CLIENT_ID');
        $client_secret = C('CLIENT_SECRET');
        $config = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
            'site_url'=>C("SITE_URL"),
        );
        $client = new OAuth2ClientService($config);
        $access_token = $client->getAccessToken();
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }
        $this->assign("error",$access_token);
    }


    public function testAdd(){
        $this->display();
    }

    public function testDelete(){
        $this->display();
    }

    public function testUpdate(){
        $this->display();
    }

    public function testQuery(){
        $this->display();
    }

    public function testGetInfo(){
        $this->display();
    }


    public function testCount(){
        $this->display();
    }

    public function testAddArray(){
        $this->display();
    }
}