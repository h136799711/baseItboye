<?php
namespace Test\Controller;

use Shop\Api\AddressApi;
use Think\Controller\RestController;
use Api\Service\OAuth2ClientService;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/11
 * Time: 14:54
 */

class TestAddressApiController extends RestController{
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

    /**
     * 添加地址
     */
    public function testAdd(){
        $this->display();
    }


    /**
     * 查询地址
     */
    public function testQueryNoPaging(){
        $this->display();
    }

    /**
     * 测试查询详细地址
     */
    public function testQueryDetail(){
        $this->display();
        /*$map=array(
            'uid'=>95
        );
        dump(apiCall(AddressApi::QUERY_WITH_CITY_WITH_AREA,array($map)));*/
    }


    /**
     * 删除地址
     */
    public function testDelete(){
        $this->display();
    }


    public function setDefaultAddress(){
        $this->display();
    }
}