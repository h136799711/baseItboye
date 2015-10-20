<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 17:21
 */
namespace OAuth2Manage\Api;

use Common\Api\Api;
use OAuth2Manage\Model\JwtModel;
use Think\Model;

class JwtApi extends Api{



    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "OAuth2Manage/Jwt/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "OAuth2Manage/Jwt/add";
    /**
     * 保存
     */
    const SAVE = "OAuth2Manage/Jwt/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "OAuth2Manage/Jwt/saveByID";

    /**
     * 删除
     */
    const DELETE = "OAuth2Manage/Jwt/delete";

    /**
     * 查询
     */
    const QUERY = "OAuth2Manage/Jwt/query";


    protected function _init(){
        $this->model = new JwtModel();
    }

}