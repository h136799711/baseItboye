<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Model;

use Think\Model\ViewModel;

class OrderWithStoresModel extends ViewModel{
	
	public $viewFields = array(

        "Orders"=>array('_table'=>'__ORDERS__','_type'=>'LEFT','taxAmount','goodsAmount','id','order_code','post_price','discount_money','taxAmount','goodsAmount','createtime','updatetime','uid','price','status','storeid','pay_status','order_status'),
//        "OrderItem"=>array("_on"=>"Orders.order_code=OrderItem.order_code","_table"=>"__ORDERS_ITEM__",'_type'=>'LEFT','weight','p_id','count','sku_desc','sku_id','post_price','has_sku','name','img','price'=>'item_price','ori_price'),
        'Store'=>array('_on'=>'Orders.storeid=Store.id','_table'=>'__STORE__','_type'=>'LEFT','name'=>'store_name','desc'=> 'store_desc','banner'=>'store_banner','logo'=>'store_logo','service_phone'),

    );
}
