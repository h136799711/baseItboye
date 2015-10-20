<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 17:15
 */
namespace Admin\Controller;

use Think\Controller;

class OAuth2Controller extends Controller{

    /**
     * 客服端\应用管理
     **/
    public function clients(){

        $this->display();
    }

}