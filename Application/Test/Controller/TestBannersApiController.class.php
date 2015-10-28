<?php
namespace Test\Controller;

use Think\Controller\RestController;
use Api\Service\OAuth2ClientService;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/8
 * Time: 14:24
 */
class TestBannersApiController extends TestController{


        /**
         * 测试不分页
         */
        public function testQueryNoPaging(){
            $this->display();
        }

        /**
         * 测试分页
         */
        public function testQuery(){
            $this->display();
        }

}