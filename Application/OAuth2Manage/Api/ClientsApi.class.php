<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 17:21
 */
namespace OAuth2Manage\Api;

use Common\Api\Api;
use OAuth2Manage\Model\ClientsModel;
use Think\Model;

class ClientsApi extends Api{



    /**
     * 获取一条信息
     */
    const GET_INFO = "OAuth2Manage/Clients/getInfo";
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "OAuth2Manage/Clients/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "OAuth2Manage/Clients/add";
    /**
     * 保存
     */
    const SAVE = "OAuth2Manage/Clients/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "OAuth2Manage/Clients/saveByID";

    /**
     * 删除
     */
    const DELETE = "OAuth2Manage/Clients/delete";

    /**
     * 查询
     */
    const QUERY = "OAuth2Manage/Clients/query";


    protected function _init(){
        $this->model = new ClientsModel();
    }

}