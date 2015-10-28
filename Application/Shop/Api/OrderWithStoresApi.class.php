<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Api;

use Common\Api\Api;
use Shop\Model\OrderWithStoresModel;

class OrderWithStoresApi extends Api
{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/OrderWithStores/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/OrderWithStores/add";
    /**
     * 保存
     */
    const SAVE = "Shop/OrderWithStores/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/OrderWithStores/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/OrderWithStores/delete";

    /**
     * 查询
     */
    const QUERY_WITH_COUNT = "Shop/OrderWithStores/queryWithCount";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/OrderWithStores/getInfo";
    /**
     * 统计单个商品的月销量
     */
    const MONTHLY_SALES = "Shop/OrderWithStores/monthlySales";

    protected function _init()
    {
        $this->model = new OrderWithStoresModel();
    }



}
