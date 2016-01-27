<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/1/4
 * Time: 15:33
 */

namespace Santi\Api;


use Common\Api\Api;
use Santi\Model\OrderCallbackModel;
use Santi\Model\SantiOrderModel;

class OrderCallbackApi extends Api {


    const  QUERY = "Santi/OrderCallback/query";

    public function _init(){
        $this->model = new OrderCallbackModel();
    }
}