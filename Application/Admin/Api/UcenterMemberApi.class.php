<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;
use Admin\Model\UcenterMemberModel;

/**
 * 统一用户信息表
 */
class UcenterMemberApi extends Api{
	
	protected function _init(){
		$this->model = new UcenterMemberModel();
	}
	
}
