<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/10/8
 * Time: 22:31
 */

namespace Home\Controller;


use Think\Controller;

class PublicController extends Controller{

    public function _initialize(){

        $this->assign("banma_app",C('BANMA_APP_URL'));
    }

    public function _empty(){
        redirect(U('Public/error',array('msg'=>base64_encode("访问地址错误"))));
    }

    public function error(){
        $msg = I('get.msg','','base64_decode');
        $this->assign("msg",$msg);
        $this->theme("app")->display();
    }

    public function login(){

        $this->theme("app")->display();
    }

}