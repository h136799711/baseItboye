<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;

use \Common\Api\Api;
use \Admin\Model\ActionLogModel;

class ActionLogApi extends Api{
	const ADD="Admin/ActionLog/add";

	protected function _init(){
		$this->model = new ActionLogModel();
	}
	
}