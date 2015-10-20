<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/25
 * Time: 10:49
 */

namespace Common\Util;

/**
 * 环境检测
 * Class EnvCheck
 * @package Common\Util
 */
class EnvCheck {

    /**
     * 是否使用移动设备访问
     * @return true:是 false:否
     */
    function isMobileVisit(){
        vendor("MobileDetect.Mobile_Detect");
        $mobileDetect = new \Mobile_Detect();
        return $mobileDetect->isMobile();
    }

    /**
     * 是否在微信浏览器中
     */
    function isWeixinBrowse(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
          return true;
        }

        return false;
    }

}