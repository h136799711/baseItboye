<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/11
 * Time: 14:55
 */

namespace Api\Service;

/**
 * 短信服务
 * Class SMSServices
 * @package Api\Service
 */
class SMSServices {

    const  JUHE_SMS = "juhe_sms";

    /**
     * 聚合短信发送接口
     * $config = array(
     * 'type'=>'类型' // 目前只支持聚合
     *                  如果是聚合则
     *          'appkey'=>聚合appkey
     *          'mobile'=>手机号
     *          'content'=>内容
     *          'tpl_id'=>短信模版ID
     *
     * )
     * @param $config
     * @return bool|mixed
     */
    public static function send($config){

        if($config == null){
            throw new \InvalidArgumentException("聚合配置信息为空");
        }
        $type = $config['type'];

        if($type == self::JUHE_SMS){
            return self::juheSend($config['appkey'],$config['mobile'],$config['content'],$config['tpl_id']);
        }

        return false;
    }

    private static function juheSend($appkey,$mobile,$content,$tpl_id){

        if(empty($appkey)
            || empty($mobile)
            || empty($content)
            || empty($tpl_id)){
            throw new \InvalidArgumentException("聚合配置信息错误");
        }
        $sendUrl= "http://v.juhe.cn/sms/send"; #请求的数据接口URL

        $params = array(
            'key'   => $appkey, //您申请的APPKEY
            'mobile'    => $mobile, //接受短信的用户手机号码
            'tpl_id'    => $tpl_id, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>  $content //您设置的模板变量，根据实际情况修改
        );

        $content = self::httpcurl($sendUrl,$params,1); //请求发送短信

        return $content;
    }

    /*
            ***请求接口，返回JSON数据
            ***@url:接口地址
            ***@params:传递的参数
            ***@is_post:是否以POST提交，默认GET
        */
    static function httpcurl($url,$params=null,$is_post=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $is_post )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if(!empty($params)){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            #echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }


}