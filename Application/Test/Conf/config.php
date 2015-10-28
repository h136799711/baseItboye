<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 15:53
 */

return array(
    'DEFAULT_THEME'=>"default",

    'TMPL_PARSE_STRING'  =>array(
//        '__CDN__' => __ROOT__.'/Public/cdn', // 更改默认的/Public 替换规则
        '__CDN__' =>  ITBOYE_CDN,//'http://lanbao.cdn.itboye.com', // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
        '__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
        '__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/imgs', // 增加新的JS类库路径替换规则
    ),

    //测试用

    'CLIENT_ID'=>'by56308c60c09521',
    'CLIENT_SECRET'=>'aea0129a6acf48b4613f9f9c6c9c6bb3',
);