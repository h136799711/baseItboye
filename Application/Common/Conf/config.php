<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */
 

return array(

    'DEFAULT_MODULE'=>'Admin',
    'SHOW_PAGE_TRACE'=>false,
     //自动载入
    'AUTOLOAD_NAMESPACE'=>array('Addons' => ADDON_PATH),
	'LOAD_EXT_CONFIG' => 'datatree,appmeta,express,phpqrcode',
    'IMGURL'=>'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC',
    'SITE_URL'=>'http://gd.itboye.com',
    'API_URL'=>'http://gd.itboye.com/api.php',
	//在线升级配置 APP_KEY,APP_ID,AUTH_DOMAIN
	'UPGRADE_AUTH_KEY'=>'[APP_KEY]',//授权码，用于在线升级
	'UPGRADE_URL'=>'http://appcenter.itboye.com/index.php/Home/Index/upgrade_check/app_id/[APP_ID]/2.json',
	'UPGRADE_AUTH_DOMAIN'=>'[AUTH_DOMAIN]',//授权域名
	
	//唯一管理员用户配置	
   'USER_ADMINISTRATOR' => 1, //管理员用户ID
   'MODULE_DENY_LIST'      =>  array('Common','Ucenter','Uclient','Adaptor'),
   'URL_CASE_INSENSITIVE' =>false,
	// 程序版本
	// DONE:移到数据库中
	// 显示运行时间
	'SHOW_RUN_TIME'=>true,
//	'SHOW_ADV_TIME'=>true,
	// 显示数据库操作次数
//	'SHOW_DB_TIMES'=>true,
	// 显示操作缓存次数
//	'SHOW_CACHE_TIMES'=>true,
	// 显示使用内存
//	'SHOW_USE_MEM'=>true,
	// 显示调用函数次数
//	'SHOW_FUN_TIMES'=>true,
	// 伪静态配置
	'URL_HTML_SUFFIX'=>'shtml'	,
    // 路由配置
    'URL_MODEL'                 =>  1, // 如果你的环境不支持PATHINFO 请设置为3
    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '127.0.0.1',//rdsrrbifmrrbifm.mysql.rds.aliyuncs.com
    'DB_NAME'                   =>  'boye_2015gd', //boye_ceping
    'DB_USER'                   =>  'root',//boye
    'DB_PWD'                    =>  'fdc656d3e7',//bo-ye2015BO-YE
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'itboye_',
    
    
	
   //调试
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_TYPE'              =>  'Db',
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'LOG_DB_CONFIG'=>array(
		'dsn'=>'mysql://root:1@127.0.0.1:3306/boye_2015gd' //本地日志数据库
	),
	
    // Session 配置
    'SESSION_PREFIX' => 'common_',
    
    //权限配置
    'AUTH_CONFIG'=>array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'common_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'common_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'common_auth_rule', //权限规则表
        'AUTH_USER' => 'common_members'//用户信息表
    ),
	
	

    //'msg_appkey'=>'d349133a9ea2f7a12ce0a75abef0bb2d',
    //'msg_tpl_id'=>'5599',
    //'msg_sendUrl'=>'http://v.juhe.cn/sms/send',


    //'express_appkey'=>'56104543f69b2498b3e8041d50ad6a71',
    //'express_sendUrl'=>'http://v.juhe.cn/exp/index',


    //'alipay_partner'=>'2088911238480664',
    //'alipay_seller_email'=>'hzboye@163.com',
    //'alipay_key'=>'xxuo4izyqjtk2zeui6erwqaxn3owm6jq',
    //'alipay_notify_url'=>'http://banma.itboye.com/index.php/Api/Alipay/notify_url',
    //'alipay_return_url'=>'http://banma.itboye.com/index.php/Api/Alipay/return_url'

);



