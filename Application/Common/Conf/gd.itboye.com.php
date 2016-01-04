<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */


//用于PHP端调用接口的配置
define("CLIENT_ID","by568a2ed8632aa1");
define("CLIENT_SECRET","78066a762646c5b1e7e46caa291d2f14");

define("ITBOYE_CDN","http://gd.cdn.itboye.com");

define('UC_DB_DSN', 'mysql://root:fdc656d3e7@127.0.0.1:3306/boye_2015gd'); // 数据库连接，使用Model方式调用API必须配置此项

return array(

    'SITE_URL'=>'http://gd.itboye.com',
    'API_URL'=>'http://gd.itboye.com/api.php',
    
    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '127.0.0.1',//rdsrrbifmrrbifm.mysql.rds.aliyuncs.com
    'DB_NAME'                   =>  'boye_2015gd', //boye_ceping
    'DB_USER'                   =>  'root',//boye
    'DB_PWD'                    =>  'fdc656d3e7',//bo-ye2015BO-YE
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'itboye_',

    'LOG_DB_CONFIG'=>array(
		'dsn'=>'mysql://root:fdc656d3e7@127.0.0.1:3306/boye_2015gd' //本地日志数据库
	),

);



