<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/12/25
 * Time: 09:24
 */

namespace Api\Vendor\SantiFlow;


class SFBase {

    //正式地址:http://pms.liulianggo.com:8051
//测试地址：http://test-pms.zjseek.com.cn:10086
//测试appKey:6da2681d45058b8b
//测试appSecret:6c41a870b54abb78efc9874475612345

//正式App_KEY : 5184434d987271c7
//密钥: da5c8e5ea87dce1be0642eb5410caabc

    protected $cfg = array(
        'api_uri'=>'http://pms.liulianggo.com:8051',
        'app_key'=>'5184434d987271c7',
        'app_secret'=>'da5c8e5ea87dce1be0642eb5410caabc',
        'notify_url'=>'http://202.99.20.186:8000/api.php/SantiCallback/index',
    );

    public function getNotifyUrl(){
        return $this->cfg['notify_url'];
    }

    public function getApiUri(){
        return $this->cfg['api_uri'];
    }

    public function getAppKey(){
        return $this->cfg['app_key'];
    }

    public function getAppSecret(){
        return $this->cfg['app_secret'];
    }


    public function postRequest($params,$uri){
        $url = $this->getApiUri().$uri;
        $sign = $this->makeSign($params,$this->getAppSecret());
        $params['sign'] = $sign;

        $result = $this->curlPost($url,$params);

        return $result;
    }

    public function getRequest($params,$uri){
        $url = $this->getApiUri().$uri;

        $sign = $this->makeSign($params,$this->getAppSecret());
        $params['sign'] = $sign;
        $result = $this->curlGet($url,$params);

        return $result;
    }



    public function makeSign($props,$secret){
        ksort($props);

        $val = array();
        foreach($props as $key=>$prop){
            if(isset($props[$key])){
                $val[] = $key;
                $val[] = urlencode($prop);
            }
        }
        $val[] = urlencode($secret);
        $text = trim(implode('',$val));
        $localSign = strtolower(md5($text));
        return $localSign;
    }


    protected function curlGet($url,$data) {
        $url .= '?';
        foreach($data as $key=>$vo){
            $url .= $key.'='.$vo.'&';
        }
        $url = rtrim($url,'&');
        $ch = curl_init();
        $header = array('Accept-Charset'=>"utf-8","Content-type"=>"application/x-www-form-urlencoded");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $error = curl_errno($ch);

        if ($error) {
            return array('status' => false, 'info' => 'curl_error'.$error);
        } else {

            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return array('status' => true, 'info' => $js);
        }
    }


    protected function curlPost($url,$data) {
        $ch = curl_init();
        $header = array('Accept-Charset'=>"utf-8","Content-type"=>"application/x-www-form-urlencoded");
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
        $error = curl_errno($ch);

        if ($error) {
            return array('status' => false, 'info' =>  'curl_error'.$error);
        } else {

            $js = json_decode($tmpInfo,true);
            if(is_null($js)){
                $js = "$tmpInfo";
            }
            return array('status' => true, 'info' => $js);
        }
    }


    public function getErrDesc($code){
        if(isset(SFBase::$error_code[$code])){
            return SFBase::$error_code[$code];
        }
        return "";
    }

    public static $error_code = array(
        '1000'=>'成功',
        '1001'=>'缺少必选参数',
        '1002'=>'签名失败',
        '1003'=>'系统里找不到充值账号或者手机号',
        '1004'=>'找不到对应订单',
        '1005'=>'订单重复',
        '1006'=>'购买数量非法',
        '1007'=>'无效渠道商',
        '1008'=>'无效订单支付方式（套餐内支付，套餐外支付）',
        '1009'=>'无效产品办理方式',
        '1010'=>'渠道商订单号重复',
        '2001'=>'找不到对应的商品',
        '2002'=>'参数有误，被充值帐户有误，比如联通充值时输入了移动的号码    参数格式或值非法，账户有误或者不支持',
        '2003'=>'运营商例行维护',
        '2004'=>'商品库存不足',
        '2005'=>'商品维护中 渠道商正在维护商品',
        '2006'=>'用户余额不足',
        '3001'=>'充值失败',
        '3002'=>'充值超时，订单被取消  如果超过10分钟还充值还未成功，返回充值超时。',
        '5001'=>'发送HTTP请求出错',
        '5002'=>'正在充值中',
        '6001'=>'App key不存在',
        '6002'=>'IP不允许',
        '0499'=>'非法的订单状态转换',
        '9999'=>'未被记录的错误',
        '104'=>'时间戳相差过大',
    );

}