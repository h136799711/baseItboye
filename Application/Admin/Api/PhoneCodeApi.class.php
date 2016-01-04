<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;

use Admin\Model\PhoneCodeModel;
use \Common\Api\Api;

class PhoneCodeApi extends Api{

    const GET_INFO="Admin/PhoneCode/getInfo";

    const DELETE="Admin/PhoneCode/delete";

    const SAVE_BY_ID="Admin/PhoneCode/saveById";

    const QUERY="Admin/PhoneCode/query";

    const QUERY_NO_PAGING="Admin/PhoneCode/queryNoPaging";

	protected function _init(){
		$this->model = new PhoneCodeModel();
	}

}
