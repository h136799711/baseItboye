<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Model;
use Think\Model\RelationModel;

class ProductGroupModel extends RelationModel{
	protected $_link = array(
		'Product' => array(
			'mapping_type'  => self::BELONGS_TO,
			'class_name'    => 'Product',
			'foreign_key'   => 'p_id',
			'parent_key'=>'id',
			'mapping_name'  => 'product',

		),
	);

	/*protected $_auto = array(
		array('update_time','time',self::MODEL_INSERT,'function')
	);*/

}
