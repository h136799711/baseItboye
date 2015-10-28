<?php
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\FinAccountBalanceHisModel;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/12
 * Time: 14:47
 */
class FinAccountBalanceHisApi extends Api{

    /**
     * 添加
     */
    const ADD="Shop/FinAccountBalanceHis/add";

    /**
     * 分页查询
     */
    const QUERY="Shop/FinAccountBalanceHis/query";


    const SAVE_BY_ID="Shop/FinAccountBalanceHis/saveById";

    /**
     * 删除
     */
    const DELETE="Shop/FinAccountBalanceHis/delete";

    /**
     * 修改
     */
    const SAVE="Shop/FinAccountBalanceHis/save";

    /**
     * 不分页查询
     */
    const QUERY_NO_PAGING="Shop/FinAccountBalanceHis/queryNoPaging";

    protected function _init(){
        $this->model=new FinAccountBalanceHisModel();
    }
}