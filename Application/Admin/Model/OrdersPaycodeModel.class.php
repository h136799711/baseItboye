<?php
namespace Admin\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/30
 * Time: 13:53
 */
class OrdersPaycodeModel extends Model{
    protected $_auto = array(
        array('createtime', NOW_TIME, self::MODEL_INSERT),
    );
}