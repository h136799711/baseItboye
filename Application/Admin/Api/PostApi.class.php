<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;

use Cms\Model\PostModel;
use Common\Api\Api;

class PostApi extends Api{

	const QUERY_NO_PAGING="Admin/Post/queryNoPaging";

	const QUERY="Admin/Post/query";

	const SAVE="Admin/Post/save";

	const GET_INFO="Admin/Post/getInfo";
	
	protected function _init(){
		$this->model = new PostModel();
	}
	
}
