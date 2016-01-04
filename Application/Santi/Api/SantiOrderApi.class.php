<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/1/4
 * Time: 15:33
 */

namespace Santi\Api;


use Common\Api\Api;
use Santi\Model\SantiOrderModel;

class SantiOrderApi extends Api {

    const  ADD = "Santi/SantiOrder/add";

    const  QUERY = "Santi/SantiOrder/query";

    const  SAVE = "Santi/SantiOrder/save";

    public function _init(){
        $this->model = new SantiOrderModel();
    }
}