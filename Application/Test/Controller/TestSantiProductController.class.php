<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:48
 */

namespace Test\Controller;


use Api\Vendor\SantiFlow\SFMobile;
use Api\Vendor\SantiFlow\SFProduct;

class TestSantiProductController extends TestController {

    public function index(){

        $request = new SFProduct();

        $result = $request->getProductList(1,20);

        dump($result);

    }

    public function mobile(){

        if(IS_POST){
            $request = new SFMobile();
            $mobile = I('post.mobile','');
            $result = $request->getInfo($mobile);
            dump($result);
        }else{
            $this->display();
        }
    }
}