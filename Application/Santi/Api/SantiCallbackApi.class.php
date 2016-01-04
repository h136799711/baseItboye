<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/31
 * Time: 11:31
 */

namespace Santi\Api;


use Common\Api\Api;
use Santi\Model\SantiCallbackModel;

class SantiCallbackApi extends Api {


    /**
     * 获取信息
     */
    const GET_INFO = "Santi/SantiCallback/getInfo";

    /**
     * 添加信息
     */
    const ADD = "Santi/SantiCallback/add";

    /**
     * 查询信息
     */
    const QUERY = "Santi/SantiCallback/query";

    public function _init(){
        $this->model = new SantiCallbackModel();
    }

}