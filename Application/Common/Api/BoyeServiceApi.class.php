<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/11/2
 * Time: 08:38
 */

namespace Common\Api;


use Api\Service\OAuth2ClientService;

/**
 * 以接口方式调用博也PHP接口.
 * Class BoyeServiceApi
 * @package Common\Api
 */
class BoyeServiceApi {

    private $apiUrl ;//接口地址，根地址

    function __construct(){
        $this->apiUrl = C('API_URL').'/';
    }

    /**
     * @param $url Orders/add 接口方法
     * @param $data
     * @return array
     */
    public function callRemote($url, $data){

        $data['access_token'] = $this->getAccessToken();
        //TODO: 对data加密
        return $this->curlPost($url,$data);
    }

    /**
     * 取得accessToken
     * @return mixed
     */
    public function getAccessToken(){

        $access_token = S('boye_access_token');

        if(empty($access_token)){
            $client_id = CLIENT_ID;
            $client_secret = CLIENT_SECRET;
            $config = array(
                'client_id'=>$client_id,
                'client_secret'=>$client_secret,
                'site_url'=>C("SITE_URL"),
            );

            $client = new OAuth2ClientService($config);
            $access_token = $client->getAccessToken();
            $access_token = $access_token['info'];


            S('boye_access_token',$access_token,60*18);
        }
        return $access_token['access_token'];
    }


    /**
     *
     * @param $url Orders/add
     * @param $data  post数据
     * @return array
     */
    protected function curlPost($url, $data) {
//        $data = http_build_query($data);
        $url = $this->apiUrl.$url;
        $ch = curl_init();
        $header = array('Accept-Charset'=>"utf-8");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno = curl_errno($ch);

        if ($errorno) {
            return array('status' => false, 'info' => $errorno);
        } else {
            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return array('status' => true, 'info' => $js);
        }
    }

}