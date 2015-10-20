<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/7
 * Time: 15:42
 */

namespace Api\Service;


class OAuth2ClientService {

    private $client_id;
    private $client_secret;
    private $api_url;
    public function __construct($config){

        if(!isset($config['client_id']) || !isset($config['client_secret'])){
            E("必须填写client_id,client_secret缺少");
        }

        $this->client_id = $config['client_id'];
        $this->client_secret = $config['client_secret'];
        $this->api_url = C("API_URL");
    }

    /**
     * Client_Credentials调用获取Access_Token
     */
    public function getAccessToken(){

        $url = $this->api_url. "/Token/index";

        $data['grant_type'] = 'client_credentials';
        $data['client_id'] = $this->client_id;
        $data['client_secret'] = $this->client_secret;

        $result = $this->curlPost($url,$data);

        return ($result);
    }


    //private


    //===============================================================

    protected function curlPost($url, $data=null,$header=null) {
        if(is_array($data)){
            $data = http_build_query($data);
        }
        $ch = curl_init();
        if(empty($header)){
            $header = array(
                'Accept-Charset'=>"utf-8",
                'Content-Type'=>'',
            );
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);

        if ($errorno) {
            return array('status' => false, 'info' => $errorno);
        } else {
            $js = json_decode($tmpInfo,JSON_OBJECT_AS_ARRAY);
            dump($js);
            if($js['code'] == 0){
                return array('status' => true, 'info' => $js['data']);
            }else{
                return array('status' => false, 'info' => $js['data']);
            }
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
        $temp = curl_exec($ch);

        return $temp;
    }

}