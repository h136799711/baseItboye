<?php
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/10/13
 * Time: 14:35
 */
namespace Api\Controller;



use Admin\Api\InternationalAlipayNotifyApi;
use Admin\Api\OrdersPaycodeApi;
use Shop\Api\OrdersApi;
use Shop\Model\OrdersModel;
use Think\Controller\RestController;
use Admin\Api\ConfigApi;


class InternationalAlipayController extends RestController{



    /**
     * 准备支付
     */
    public function ready_pay(){

        $this->display();
    }


    private  $alipay_config;

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->getConfig();
        $this->alipay_config = $this->getAlipayConfig();

        //引入支付官方sdk
        vendor('alipay_submit',APP_PATH.'Api/Vendor/InternationalAlipay/','.class.php');

    }


    /**
     *
    */
    public function confirm_pay(){
        /**************************请求参数**************************/
        //支付类型
        //$payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = $this->alipay_config['notify_url'];// "http://demo001.itboye.com/index.php/notify";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $this->alipay_config['return_url']; //"http://192.168.0.100/oschina/index.php/Api/Alipay/return_url";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $_POST['WIDsubject'];
        //付款金额
        $total_fee =0.01;// $_POST['WIDtotal_fee'];
        //订单描述
        $body = $_POST['WIDbody'];


        $currency=$_POST['WIDcurrency'];
        //商品展示地址
        //$show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        //防钓鱼时间戳
        //$anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        //$exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1

        $rmb_fee=$_POST['WIDrmb_fee'];
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_forex_trade",
            "partner" => trim($this->alipay_config['partner']),
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "currency"	=> $currency,
            "subject" => $subject,
            //"total_fee" => $total_fee,
            "rmb_fee" => $rmb_fee,
            "body" => $body,
            "_input_charset" => trim(strtolower($this->alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "");
        //dump($html_text);
        echo $html_text;
    }



    public function getAlipayConfig(){
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= C('ALIPAY_API.INTERNATIONAL_ALIPAY_PARTNER');

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']			=  C('ALIPAY_API.INTERNATIONAL_ALIPAY_KEY');


        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        $alipay_config['return_url'] = C('ALIPAY_API.INTERNATIONAL_ALIPAY_RETURN_URL');
        //After the payment transaction is done
        //Notification URL
        $alipay_config['notify_url'] = C('ALIPAY_API.INTERNATIONAL_ALIPAY_NOTIFY_URL');

        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';

        return $alipay_config;
    }


    /**
     * 支付宝同步请求此支付回调
     * 支付成功
     */
    public function return_url(){
        //订单号
        $out_trade_no =  $_GET['out_trade_no'];


        $this->assign('out_trade_no',$out_trade_no);
        $this->display();
    }



    /**
     * 支付宝异步请求通知此接口
     *  必须保证服务器异步通知页面(notify_url)上无任何字符,如空格、HTML 标签、开发系统自带抛出的异常提示信息等;
     * (2) 支付宝是用 POST 方式发送通知信息,因此该页面中获取参数的方式,如: request.Form("out_trade_no")、$_POST['out_trade_no'];
     * (3) 支付宝主动发起通知,该方式才会被启用;
     * (4) 只有在支付宝的交易管理中存在该笔交易,且发生了交易状态的改变,支付宝才会通过该方式发起服务器通知(即时到账中交易状态为“等待买家付款”的状态默认是不会发送通知的);
     * (5) 服务器间的交互,不像页面跳转同步通知可以在页面上显示出来,这种交互方式是不可见的;
     * (6) 第一次交易状态改变(即时到账中此时交易状态是交易完成)时,不仅页面跳转同步通知页面会启用,而且服务器异步通知页面也会收到支付宝发来的处理结果通知;
     * (7) 程序执行完后必须打印输出“success”(不包含引号)。如果商户反馈给支付宝的字符不是 success 这 7 个字符,支付宝服务器会不断重发通知,直到 超过24小时22分钟。一般情况下,25 小时以内完成 8 次通知(通知的间隔频率一般是: 2m,10m,10m,1h,2h,6h,15h);
     * (8) 程序执行完成后,该页面不能执行页面跳转。如果执行页面跳转,支付宝会 收不到 success 字符,会被支付宝服务器判定为该页面程序运行出现异常, 而重发处理结果通知;
     *
     * 即时到账交易接口
     * (9) cookies、session 等在此页面会失效,即无法获取这些数据;
     * (10) 该方式的调试与运行必须在服务器上,即互联网上能访问;
     * (11) 该方式的作用主要防止订单丢失,即页面跳转同步通知没有处理订单更新,它则去处理;
     * (12) 当商户收到服务器异步通知并打印出success时,服务器异步通知参数
     *  notify_id 才会失效。也就是说在支付宝发送同一条异步通知时(包含商户并 未成功打印出 success 导致支付宝重发数次通知),服务器异步通知参数 notify_id 是不变的。
     */
    public function notify_url(){
        //交易完成 TRADE_FINISHED
        //支付成功 TRADE_SUCCESS
        //订单号
        $out_trade_nos =  $_POST['out_trade_no'];
        addLog("InternationalAlipay/notify_url",$_GET,$_POST,$out_trade_nos."支付异步通知!");
        //支付成功后返回订单状态


        $map=array(
            'id'=>$out_trade_nos
        );
        $result=apiCall(OrdersPaycodeApi::GET_INFO,array($map));
        if($result['status']){
            if($result['info']!=null){
                $orderCodeArray=explode(';',$result['info']['order_content']);
                array_pop($orderCodeArray);
                foreach($orderCodeArray as $order_code){
                    $map=array(
                        'order_code'=>$order_code,
                    );
                    $entity=array(
                        'pay_status'=>OrdersModel::ORDER_PAID,
                    );
                    apiCall(OrdersApi::SAVE,array($map,$entity));
                }
            }

        }


        $entity=array(
            "notify_id"=>$_POST['notify_id'],
            "notify_type"=>$_POST['notify_type'],
            "sign"=>$_POST['sign'],
            "trade_no"=>$_POST['trade_no'],
            "total_fee"=>$_POST['total_fee'],
            "out_trade_no"=>$_POST['out_trade_no'],
            "currency"=>$_POST['currency'],
            "notify_time"=>strtotime($_POST['notify_time']),
            "trade_status"=>$_POST['trade_status'],
            "sign_type"=>$_POST['sign_type'],
        );


        apiCall(InternationalAlipayNotifyApi::ADD,array($entity));

        echo "success";
        exit();
    }



    /**
     * 从数据库中取得配置信息
     */
    protected function getConfig() {
        $config = S('config_' . session_id() . '_' . session("uid"));
        //dump($config);
        if ($config === false) {
            $map = array();
            $fields = 'type,name,value';
            $result = apiCall(ConfigApi::QUERY_NO_PAGING, array($map, false, $fields));
            if ($result['status']) {
                $config = array();

                if (is_array($result['info'])) {
                    foreach ($result['info'] as $value) {
                        $config[$value['name']] = $this -> parse($value['type'], $value['value']);

                    }
                }
                //缓存配置300秒
                S("config_" . session_id() . '_' . session("uid"), $config, 300);
            } else {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this -> error($result['info']);
            }
        }
        //dump(session_id());

        // dump($config);
        C($config);
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @return array|string
     */
    private static function parse($type, $value) {
        switch ($type) {
            case 3 :
                //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val,2);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }

}