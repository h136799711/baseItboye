<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 14:43
 */

return array(

//    /* 调试错误时，可以注释掉下面TMPL */
    'TMPL_CONTENT_TYPE'     =>  'application/json', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     =>  APP_PATH.'Api/View/action_error.json', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  APP_PATH.'Api/View/success.json', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'Api/View/exception.json',// 异常页面的模板文件


    'SESSION_AUTO_START'=>false,
    'DEFAULT_THEME'=>'default',
    'TMPL_PARSE_STRING'  =>array(
        '__UPLOAD__' => __ROOT__.'/Public/Upload', // 更改默认的/Public 替换规则
        '__CDN__' => ITBOYE_CDN, // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
        '__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
        '__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/imgs', // 增加新的JS类库路径替换规则

    ),

    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '127.0.0.1',//rdsrrbifmrrbifm.mysql.rds.aliyuncs.com
    'DB_NAME'                   =>  'boye_2015gd', //boye_ceping
    'DB_USER'                   =>  'root',//boye
    'DB_PWD'                    =>  'fdc656d3e7',//bo-ye2015BO-YE
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'itboye_',

    //裁减图位置无需/结尾
    'THUMBNAIL_PATH'=>'./Uploads/UserPicture_Thumbnail',

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    /* 图片上传相关配置 */
    'USER_PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 500*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/UserPicture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    'PICTURE_UPLOAD_DRIVER'=>'local',

    'DATATREE' =>  array(
        'File_Type_Avatar'=>32 //文件类型_用户头像
    ),


   

);