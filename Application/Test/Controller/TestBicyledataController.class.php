<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/29
 * Time: 20:14
 */

namespace Test\Controller;


use Api\Service\OAuth2ClientService;
use Think\Controller;

class TestBicyledataController extends Controller{

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
//        dump($access_token);
//        exit();
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']['access_token']);
        }
        $this->assign("error",$access_token);
    }

    public function testAdd(){
        $this->display();
    }
    public function testDay(){
        $this->display();
    }

    public function testMonth(){
        $this->display();
    }

}