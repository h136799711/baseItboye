<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/12
 * Time: 23:59
 */

namespace Test\Controller;


use Api\Service\WeixinService;

class TestWeixinController {

    public function index(){
        $code = "04138715114be21b573b4353d3591b1U";
        $appid = "wx0d259d7e9716d3dd";
        $appsecret = "94124fb74284c8dae6f188c7e269a5a0";
        $service = new WeixinService($appid,$appsecret);

//        $result = $service->getAccessTokenAndOpenid($code);

//        dump($result);

        $result = $service->getUserInfo($code);

        dump($result);

        exit();
    }

    public function test(){

        $access_token = "OezXcEiiBSKSxW0eoylIeLL__w8mh9_H1IFEtNHG8D2z6hbexaHPFPMffNopjx5onqFQjX4jHlPEPc8GQus44d0931Ra874-icp1HpDHhoL3GE8OeoKW9OPoS7sOjlLKeOQUfLO7YzYNEGcmtvKGPw";
        $openid ="ooQDbsnArKx1iBCUp05EfFeOP8f0";
        $unionid = "o_4WajjRYUsu6qM3Fn3NvnctZrg0";


        $service = new WeixinService($appid,$appsecret);


        $result = $service->getUserInfoBy($access_token,$openid);

        dump($result);

    }

}