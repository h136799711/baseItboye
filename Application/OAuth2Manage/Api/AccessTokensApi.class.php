<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 17:21
 */
namespace OAuth2Manage\Api;

use Common\Api\Api;
use OAuth2Manage\Model\AccessTokensModel;
use Think\Model;

class AccessTokensApi extends Api{



    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "OAuth2Manage/AccessTokens/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "OAuth2Manage/AccessTokens/add";
    /**
     * 保存
     */
    const SAVE = "OAuth2Manage/AccessTokens/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "OAuth2Manage/AccessTokens/saveByID";

    /**
     * 删除
     */
    const DELETE = "OAuth2Manage/AccessTokens/delete";

    /**
     * 查询
     */
    const QUERY = "OAuth2Manage/AccessTokens/query";

    /**
     * 查询数据
     */
    const GET_INFO = "OAuth2Manage/AccessTokens/getInfo";


    protected function _init(){
        $this->model = new AccessTokensModel();
    }

}