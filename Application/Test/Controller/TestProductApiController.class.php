<?php
namespace Test\Controller;
use Think\Controller\RestController;
use Api\Service\OAuth2ClientService;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/7
 * Time: 13:50
 */

class TestProductApiController extends RestController{

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
        dump($access_token);
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }
        $this->assign("error",$access_token);
    }


    /**
     * 测试分页查询
     */
    public function testQuery(){
        $this->display();
    }

    /**
     * 测试不分页查询
     */
    public function testQueryNoPaging(){
        $this->display();
    }

    /**
     * 商品详情
     */
    public function testDetail(){
        $this->display();
    }


}