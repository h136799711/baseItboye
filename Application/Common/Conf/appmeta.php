<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

define('BOYE_SYS_NAME',true);
define("PROJECT_NAME","itboye20150817");
define("ITBOYE_CDN","http://192.168.0.100/github/itboye_cdn/cdn");

// 必须定义
define('UC_AUTH_KEY', '+Ot:#o^8=y!vlnMEw)A3T0uUke<{?z~I>R(6m/5-'); //加密KEY
define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_DB_DSN', 'mysql://root:1@192.168.0.100:3306/boye_2015gd'); // 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', 'itboye_'); // 数据表前缀，使用Model方式调用API必须配置此项