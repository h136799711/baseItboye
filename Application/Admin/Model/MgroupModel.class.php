<?php
namespace Admin\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * User: zcs
 * Date: 2015/9/15
 * Time: 14:16
 */
class MgroupModel extends Model{
    protected $_auto = array(
        array('createtime', NOW_TIME, self::MODEL_INSERT),
    );
}