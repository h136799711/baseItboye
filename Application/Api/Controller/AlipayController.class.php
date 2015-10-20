<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/7
 * Time: 10:04
 */

namespace Api\Controller;

use Admin\Api\AlipayNotifyApi;
use Admin\Api\OrdersPaycodeApi;
use Shop\Api\OrdersApi;
use Shop\Model\OrdersModel;
use Think\Controller\RestController;
use Admin\Api\ConfigApi;

class AlipayController extends RestController {

    private  $alipay_config;

    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
        $this->getConfig();
        $this->alipay_config = $this->getAlipayConfig();

        //引入支付官方sdk
        vendor('alipay_submit',APP_PATH.'Api/Vendor/Alipay/','.class.php');

    }

    /**
     * 准备支付
     */
    public function ready_pay(){

        $this->display();
    }

    /**
     * 确认支付
     *  功能：即时到账交易接口接入页
     * 版本：3.3
     * 修改日期：2012-07-23
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

     *************************注意*************************
     * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
     * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
     * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
     * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
     * 如果不想使用扩展功能请把扩展功能参数赋空值。
     */
    public function confirm_pay()
    {

        /**************************请求参数**************************/
        //支付类型
        $payment_type = "1";
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
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($this->alipay_config['partner']),
            "seller_email" => trim($this->alipay_config['seller_email']),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim(strtolower($this->alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($this->alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "");
        //dump($html_text);
        echo $html_text;

    }

    /**
     * 支付宝即时到账接口支付配置信息
     * @return array
     */
    private function getAlipayConfig(){
        $alipay_config =  array();

        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		=C('ALIPAY_API.ALIPAY_PARTNER');// '2088911238480664';

        //收款支付宝账号
        $alipay_config['seller_email']	= C('ALIPAY_API.ALIPAY_SELLER_EMAIL');//'hzboye@163.com';

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']			=C('ALIPAY_API.ALIPAY_KEY'); //'xxuo4izyqjtk2zeui6erwqaxn3owm6jq';

        $alipay_config['notify_url']    =C('ALIPAY_API.ALIPAY_NOTIFY_URL'); //'http://banma.itboye.com/index.php/Api/Alipay/notify_url';

        $alipay_config['return_url']   =C('ALIPAY_API.ALIPAY_RETURN_URL'); //'http://banma.itboye.com/index.php/Api/Alipay/return_url';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

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
        //支付成功后返回订单状态

        $map=array(
            'id'=>$out_trade_nos
        );
        $result=apiCall(OrdersPaycodeApi::GET_INFO,array($map));
        if($result['status']){
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



        /**
         * {
        "notify_type": "trade_status_sync",                //trade_status_sync 同步
        "notify_time": "2015-09-07 12:00:20",
        "notify_id": "05f3a06f1807d239e97130e87b28266ijo",
        "sign_type": "MD5", //签名类型
        "sign": "3048895bf2a34b16e9a28e2f9a6e5324" //签名


        "discount": "0.00",                                //
        "payment_type": "1",
        "subject": "何必都123",                           //描述
        "trade_no": "2015090721001004330065380723",     //支付宝支付单号
        "buyer_email": "13484379290",
        "gmt_create": "2015-09-07 12:00:13",
        "quantity": "1",
        "out_trade_no": "2015090711594485091481",
        "seller_id": "2088911238480664",
        "body": "德国铁元（葡萄",
        "trade_status": "TRADE_SUCCESS",
        "is_total_fee_adjust": "N",
        "total_fee": "0.01",
        "gmt_payment": "2015-09-07 12:00:20",
        "seller_email": "hzboye@163.com",
        "price": "0.01",
        "buyer_id": "2088702508457332",
        "use_coupon": "N",
        }
         */




        /*{
            "discount": "0.00",
            "payment_type": "1",
            "subject": "1254323442314",
            "trade_no": "2015090921001004020040106703",
            "buyer_email": "1357520721@qq.com",
            "gmt_create": "2015-09-09 18:09:01",
            "notify_type": "trade_status_sync",
            "quantity": "1",
            "out_trade_no": "152511753444658814860",
            "seller_id": "2088911238480664",
            "notify_time": "2015-09-09 18:09:08",
            "body": "支付",
            "trade_status": "TRADE_SUCCESS",
            "is_total_fee_adjust": "N",
            "total_fee": "0.01",
            "gmt_payment": "2015-09-09 18:09:08",
            "seller_email": "hzboye@163.com",
            "price": "0.01",
            "buyer_id": "2088912990416029",
            "notify_id": "339149b102ca9fc8ae92f8c9aec60a1g5k",
            "use_coupon": "N",
            "sign_type": "MD5",
            "sign": "d729ef0fb953ae1d1979cf371a319b13"
        }*/

        $entity=array(
            "discount"=> $_POST['discount'],
            "payment_type"=>$_POST['payment_type'],
            "subject"=>$_POST['subject'],
            "trade_no"=>$_POST['trade_no'],
            "buyer_email"=>$_POST['buyer_email'],
            "gmt_create"=>strtotime($_POST['gmt_create']),
            "notify_type"=>$_POST['notify_type'],
            "quantity"=>$_POST['quantity'],
            "out_trade_no"=>$_POST['out_trade_no'],
            "seller_id"=>$_POST['seller_id'],
            "notify_time"=>strtotime($_POST['notify_time']),
            //"body"=>$_POST['body'],
            "trade_status"=>$_POST['trade_status'],
            "is_total_fee_adjust"=>$_POST['is_total_fee_adjust'],
            "total_fee"=>$_POST['total_fee'],
            "gmt_payment"=>strtotime($_POST['gmt_payment']),
            "seller_email"=>$_POST['seller_email'],
            "price"=>$_POST['price'],
            "buyer_id"=>$_POST['buyer_id'],
            "notify_id"=>$_POST['notify_id'],
            "use_coupon"=>$_POST['use_coupon'],
            "sign_type"=>$_POST['sign_type'],
            "sign"=>$_POST['sign'],
        );
       // addLog("Alipay/notify_url",$_GET,$_POST,$entity);


        addLog("Alipay/notify_url",$_GET,$_POST,$out_trade_nos."支付异步通知!");
        apiCall(AlipayNotifyApi::ADD,array($entity));
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