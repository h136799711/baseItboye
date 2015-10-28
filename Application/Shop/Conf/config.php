<?php
return array(
	//'配置项'=>'配置值',
	'TMPL_PARSE_STRING'  =>array(
     	'__CDN__' => ITBOYE_CDN, // 更改默认的/Public 替换规则
		'__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
     	'__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
     	'__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/img', // 增加新的JS类库路径替换规则
     	'__QRCODE__'     => __ROOT__.'/Uploads/QrcodeMerge', // 增加新的JS类库路径替换规则
     	'__UPLOAD__' => __ROOT__.'/Public/Upload', // 更改默认的/Public 替换规则
     
	),	
	'DEFAULT_THEME'=>'default',
	'SESSION_PREFIX'=>'Shop_',
	'PICTURE_UPLOAD_DRIVER'=>'local',
    
     //TODO: 一套系统对应一个公众号的商城
    'STORE_ID'=>5,
    
	'SHOW_PAGE_TRACE'=>false,

//    'WXPAY_CONFIG'=>array(
//
//        'APPID'=>'wxb17e88453be034d6',
//        'APPSECRET'=>'97c88bd38fa282cc0ac3aa7dd76fbdf2',
//        'MCHID'=>'1228859402',
//        'KEY'=>'E48D86C64D6B8EA672BEBF6ACC75AD94',//在微信发送的邮件中查看,patenerkey
////
//        'NOTIFYURL'=>'http://renren.itboye.com/index.php/Shop/WxpayNotify/index',
//        'JSAPICALLURL'=>'http://renren.itboye.com/index.php/Shop/Orders/pay?showwxpaytitle=1',
//        'SSLCERTPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
//        'SSLKEYPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
//        'CURL_PROXY_HOST' => "0.0.0.0",
//        'CURL_PROXY_PORT' => '0',
//        'REPORT_LEVENL' => 1,
//		'PROCESS_URL'=>'http://renren.itboye.com/index.php/Shop/WxpayNotify/aysncNotify?key=hebidu',//异步处理地址
//    ),
);