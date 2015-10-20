<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 17:26
 */

namespace OAuth2Manage\Api;

use Common\Api\Api;
use OAuth2Manage\Model\ScopesModel;
use Think\Model;

/**
 * ScopesModel model
 */
class ScopesApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "OAuth2Manage/Scopes/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "OAuth2Manage/Scopes/add";
    /**
     * 保存
     */
    const SAVE = "OAuth2Manage/Scopes/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "OAuth2Manage/Scopes/saveByID";

    /**
     * 删除
     */
    const DELETE = "OAuth2Manage/Scopes/delete";

    /**
     * 查询
     */
    const QUERY = "OAuth2Manage/Scopes/query";

    protected function _init(){
        $this->model = new ScopesModel();
    }

}