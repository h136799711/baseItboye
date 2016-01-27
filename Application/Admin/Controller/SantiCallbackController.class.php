<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/31
 * Time: 11:36
 */

namespace Admin\Controller;


use Santi\Api\OrderCallbackApi;
use Santi\Api\SantiCallbackApi;

class SantiCallbackController extends AdminController {

    public function index(){
        $mobile = I('post.mobile','');

        $map = array();
        $params= array();
        if(!empty($mobile)){
            $map['mobile'] = $mobile;
            $params['mobile']= $mobile;
        }
        $page = array('curpage'=>I('param.p',0),'size'=>10);

        $api = new OrderCallbackApi();
        $result = $api->query($map,$page,'create_time desc',$params);

        if($result['status']){
            $this->assign("list",$result['info']['list']);
            $this->assign("show",$result['info']['show']);
        }else{
            $this->error($result['info']);
        }

        $this->assign("mobile",$mobile);
        $this->display();
    }

}