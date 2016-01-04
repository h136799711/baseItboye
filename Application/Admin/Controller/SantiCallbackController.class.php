<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/31
 * Time: 11:36
 */

namespace Admin\Controller;


use Santi\Api\SantiCallbackApi;

class SantiCallbackController extends AdminController {

    public function index(){

        $map = array();
        $page = array('curpage'=>I('param.p',0),'size'=>10);
        $result = apiCall(SantiCallbackApi::QUERY,array($map,$page,'create_time desc'));
//        dump($result);
        if($result['status']){
            $this->assign("list",$result['info']['list']);
            $this->assign("show",$result['info']['show']);
        }else{
            $this->error($result['info']);
        }

        $this->display();
    }

}