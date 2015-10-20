<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 15:04
 */
namespace Test\Controller;


use Think\Controller;

/**
 * 测试用OAuth2
 * Class OAuth2Controller
 * @package Test\Controller
 */
class OAuth2Controller extends Controller{


    /**
     *
     */
    public function user_credentials(){

        $this->display();
    }

    public function index(){

        $map = array();
        $this->display();
    }

    public function implicit(){
        $this->display();
    }

    public function auth_code(){
        if(!isset($_GET['code'])){
            $this->display();
        }else{
            $this->assign("state",I('get.state',''));
            $this->assign("code",I('get.code',''));
            $this->display();
        }
    }

    public function client_credentials(){
        $this->display();
    }


}