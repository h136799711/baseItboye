<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:48
 */

namespace Test\Controller;


use Api\Vendor\SantiFlow\SFFlowFacade;
use Api\Vendor\SantiFlow\SFMobile;
use Api\Vendor\SantiFlow\SFProduct;
use Shop\Api\MemberConfigApi;

class TestSantiProductController extends TestController {

    public function index(){
        $request = new SFProduct();
        $result = $request->getProductList(1,20);
        dump($result);
    }

    private function getIDCode($uid){

        $idcode = dechex($uid+1048576);
        $idcode = str_pad($idcode,6,"0",STR_PAD_LEFT);

        return $idcode;
    }

    public function idcode(){
        $config = new MemberConfigApi();
        $map =  array('uid'=>array('gt',30));
        $page = array('curpage'=>1,'size'=>100);
        $result = $config->query($map,$page);

        $list = $result['info']['list'];

        foreach($list as $vo){
            $entity = array(
                'IDCode'=>$this->getIDCode($vo['uid']),
            );
            $result = $config->saveByID($vo['uid'],$entity);

        }

    }

    public function mobile(){

        if(IS_POST){
            $santi = new SFFlowFacade();
            $request = new SFMobile();
            $mobile = I('post.mobile','');
            $result = $santi->is10000($mobile);
            if($result){
                echo "电信号码";
            }
            $result = $santi->is10010($mobile);
            if($result){
                echo "联通号码";
            }
            $result =  $santi->is10086($mobile);
            if($result){
                echo "移动号码";
            }

            dump($result);
        }else{
            $this->display();
        }
    }

    public function order(){
        if(IS_POST){
            $santi = new SFFlowFacade();
            $mobile = I('post.mobile','');
            $flow = I('post.flow','');
            $result = $santi->createAndSubmit($mobile,$flow);

            var_dump($result);

        }else{
            $this->display();
        }
    }
}