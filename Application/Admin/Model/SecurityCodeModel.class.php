<?php
namespace Admin\Model;
use Think\Model;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/2
 * Time: 15:32
 */
class SecurityCodeModel extends Model{

    /**
     * 注册
     */
    const TYPE_FOR_REGISTER = 1;

    /**
     * 更新密码
     */
    const TYPE_FOR_UPDATE_PSW = 2;

    /**
     * 绑定手机号,之前未绑定过
     */
    const TYPE_FOR_NEW_BIND_PHONE = 3;

    /**
     * 更换手机号,
     */
    const TYPE_FOR_CHANGE_NEW_PHONE = 4;

    protected $_auto = array(
        array('starttime', NOW_TIME, self::MODEL_INSERT),
    );
}