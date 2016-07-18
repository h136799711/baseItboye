<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */

define("ITBOYE_CDN","http://192.168.0.100/github/itboye_cdn/cdn");

define('UC_DB_DSN', 'mysql://root:1@192.168.0.100:3306/boye_2015gd'); // 数据库连接，使用Model方式调用API必须配置此项


return array(

    'SITE_URL'=>'http://192.168.0.100/github/baseItboye',
    'API_URL'=>'http://192.168.0.100/github/baseItboye/api.php',

    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '192.168.0.100',//rdsrrbifmrrbifm.mysql.rds.aliyuncs.com
    'DB_NAME'                   =>  'boye_2015gd', //boye_ceping
    'DB_USER'                   =>  'root',//boye
    'DB_PWD'                    =>  '1',//bo-ye2015BO-YE
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'itboye_',

    'LOG_DB_CONFIG'=>array(
		'dsn'=> UC_DB_DSN //本地日志数据库
	),

);



