<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/3/17
 * Time: 16:59
 */

namespace Admin\Api;

use Admin\Model\DevicePhoneModel;
use Common\Api\Api;

class DevicePhoneApi extends Api {

    /**
     * 查询单条
     */
    const GET_INFO = "Admin/DevicePhone/getInfo";
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/DevicePhone/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/DevicePhone/add";
    /**
     * 保存
     */
    const SAVE = "Admin/DevicePhone/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/DevicePhone/saveByID";

    protected function _init(){
        $this->model = new DevicePhoneModel();
    }

}