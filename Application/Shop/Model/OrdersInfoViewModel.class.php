<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Model;

use Think\Model\ViewModel;

class OrdersInfoViewModel extends ViewModel{
	
	public $viewFields = array(

        "Orders"=>array('_table'=>'__ORDERS__','_type'=>'LEFT','taxAmount','goodsAmount','id','order_code','post_price','discount_money','createtime','updatetime','uid','price','status','storeid','pay_status','order_status'),
        "OrderInfo"=>array("_on"=>"Orders.order_code=OrderInfo.order_code","_table"=>"__ORDERS_CONTACTINFO__",'_type'=>'LEFT','wxno','detailinfo','contactname','country','city','province','detailinfo','area','mobile','id_card'),
        "UcenterMember"=>array('_on'=>'Orders.uid=UcenterMember.id','_table'=>'__UCENTER_MEMBER__','_type'=>'LEFT','username','email'),
        'Store'=>array('_on'=>'Orders.storeid=Store.id','_table'=>'__STORE__','_type'=>'LEFT','name'=>'store_name','desc'=> 'store_desc','banner'=>'store_banner','logo'=>'store_logo','service_phone'),

    );
}
