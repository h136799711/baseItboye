<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\HasIdcodeModel;

class HasIdcodeApi extends Api{
    /**
     * 添加
     */
    const ADD = "Shop/HasIdcode/add";
    /**
     * 保存
     */
    const SAVE = "Shop/HasIdcode/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/HasIdcode/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/HasIdcode/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/HasIdcode/query";
	const QUERY_NO_PAGING="Shop/HasIdcode/queryNoPaging";
   protected function _init(){
		$this->model = new HasIdcodeModel();
	}
}

