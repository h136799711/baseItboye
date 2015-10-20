<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/12
 * Time: 23:37
 */

namespace Api\Service;


class WeixinService {

    protected $appid = "";
    protected $appsecret = "";

    function __construct($appid, $appsecret) {
        $this -> appid = $appid;
        $this -> appsecret = $appsecret;
    }

    public function getAccessTokenAndOpenid($code){

        $url_get = 'https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code&appid=' . $this -> appid . '&secret=' . $this -> appsecret.'&code='.$code;
        $json = $this -> curlGet($url_get);
//        dump($json);
        if(!$json['status']){
            return $json;
        }

        $data  = $json['info'];

        if($data->errcode ){
            return array('status'=>false,'info'=>$data['errmsg']);
        }


        return array('status'=>true,'info'=>array(
            'access_token'=>$data['access_token'],
            'openid'=>$data['openid'],
            'unionid'=>$data['unionid'],
        ));

    }


    public function  getUserInfo($code){
        $data = $this->getAccessTokenAndOpenid($code);
//        dump($data);
        if(!$data['status']){
            return $data;
        }

        if(is_array($data['info'])){
            $data = $data['info'];
            $access_token = $data['access_token'];
            $openid = $data['openid'];

            $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid;

            $json = $this -> curlGet($url);

            return $json;

        }else{
            return array('status'=>false,'info'=>'未知错误!');
        }
    }

//    public function getUserInfoBy($access_token,$openid){
//
//        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid;
//
//        $json = $this -> curlGet($url);
//
//        return $json;
//    }


    //===============================================================

    protected function curlPost($url, $data) {

        $ch = curl_init();
        $header = array('Accept-Charset'=>"utf-8");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);
        if ($error) {
            return array('status' => false, 'info' => $error);
        } else {
            return array('status' => true, 'info' => json_decode($tmpInfo,true));
        }
    }

    protected function curlGet($url) {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);
        if ($error) {
            return array('status' => false, 'info' => $error);
        } else {
            return array('status' => true, 'info' => json_decode($tmpInfo,true));
        }

    }


}