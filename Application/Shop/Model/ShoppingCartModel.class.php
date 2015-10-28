<?php
namespace Shop\Model;

use Think\Model\RelationModel;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/20
 * Time: 17:34
 */

class ShoppingCartModel extends RelationModel{

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );
}