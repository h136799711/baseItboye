<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Model;

use Think\Model\ViewModel;

class OrdersWithExpressViewModel extends ViewModel{
	
	public $viewFields = array(
		"Orders"=>array('_table'=>'__ORDERS__','id',	'_type'=>'LEFT','order_code','createtime','updatetime','uid','price','items','status','pay_status','order_status'),
		"OrdersExpress"=>array("_on"=>"Orders.order_code=OrdersExpress.order_code","_table"=>"__ORDERS_EXPRESS__",'expressname','expresscode','expressno')
	);
}
