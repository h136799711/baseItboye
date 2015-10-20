<?php
namespace Api\Controller;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/7
 * Time: 11:50
 */
class ExpressController extends ApiController{
    protected  $allowType = array("json","rss","html");
    function getSupportMethod()
    {
        /*   return array(
               'item'=>array(
                   'param'=>'access_token|username|password',
                   'return'=>'array(\"status\"=>返回状态,\"info\"=>\"信息\")',
                   'author'=>'hebidu [hebiduhebi@163.com]',
                   'version'=>'1.0.0',
                   'description'=>'用户登录验证',
                   'demo_url'=>'http://manual.itboye.com#',
               ),
           );*/

    }

    /**
     * 查询订单
     */
    function  searchExpress(){

        /*目前支持的快递公司
         *顺丰 sf
         *申通 sto
	     *圆通 yt
         *韵达 yd
         *天天 tt
         *EMS ems
         *中通 zto
         *汇通 ht
        */
        $url =C('JUHE_API.EXPRESS_SENDURL');#请求的数据接口URL
        $com=$this->_post("com");
        $no=$this->_post("no");
        $params='com='.$com.'&no='.$no.'&dtype=json&key='.C('JUHE_API.EXPRESS_APPKEY');

        $content = $this->juhecurl($url,$params,0);
        if($content){
            $result = json_decode($content,true);
            $result_code = $result['resultcode'];
            if($result_code == 200){
                $this->apiReturnSuc($result['result']);
            }else{
                $this->apiReturnErr("订单查询失败,错误ID号：".$result_code);
            }
        }else{
            $this->apiReturnErr("订单查询失败");
        }
    }


    /**
     * 查询订单
     */
    function  searchExpressHtml(){

       // $url=C('JUHE_API.EXPRESS_SENDURL'); #请求的数据接口URL
        //dump($url);
        /*$com=$this->_post("com");
        $no=$this->_post("no");
        $params='com='.$com.'&no='.$no.'&dtype=json&key='.C('JUHE_API.EXPRESS_APPKEY');
        $content = $this->juhecurl($url,$params,0);
        if($content){
            $result = json_decode($content,true);
            //rsort($result['result']['list']);
            $this->assign('result',$result);
        }else{

        }*/

        /******** 测试数据*************/
       $result['error_code']=0;
        $list[]= array(
                "datetime"=>"2013-06-25  10:44:05",
                "remark"=>"已收件",
                "zone"=>"台州市"
        );
        $list[]= array(
                "datetime"=> "2013-06-25  11:05:21",
                "remark"=>"快件在 台州  ,准备送往下一站 台州集散中心  ",
                "zone"=> "台州市"

        );
        $list[]= array(
            "datetime"=>"2013-06-25  20:36:02",
            "remark"=>"快件在 台州集散中心  ,准备送往下一站 台州集散中心 ",
            "zone"=>"台州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-25  21:17:36",
            "remark"=>"快件在 台州集散中心 ,准备送往下一站 杭州集散中心",
            "zone"=>"台州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-26  12:20:00",
            "remark"=>"快件在 杭州集散中心  ,准备送往下一站 西安集散中心 ",
            "zone"=>"杭州市"
        );
        $list[]= array(
            "datetime"=>"2013-06-27  05:48:42",
            "remark"=>"快件在 西安集散中心 ,准备送往下一站 西安  ",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:03:03",
            "remark"=>"正在派件.. ",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:51:33",
            "remark"=>"派件已签收",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27 08:51",
            "remark"=>"派件已签收",
            "zone"=>"西安市/咸阳市"
        );

        $list[]= array(
            "datetime"=>"2013-06-27  08:51:33",
            "remark"=>"签收人是：已签收",
            "zone"=>"西安市/咸阳市"
        );


        rsort($list); //数组倒序
        $result['result']=array(
           "company"=>"顺丰",
           "com"=>"sf",
           "no"=>"575677355677",
           "status"=>1,
            "list"=>$list
        );
        $this->assign("result",$result);
        /******** 测试数据*************/
        $this->display();
    }


    /*
      ***请求接口，返回JSON数据
      ***@url:接口地址
      ***@params:传递的参数
      ***@ispost:是否以POST提交，默认GET
  */
    function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_0 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
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


    /**
     * 计算运费
     */
    function price(){
        $notes = "应用" . $this->client_id . "，调用物流运费接口";
        addLog("Express/price", $_GET, $_POST, $notes);


        $pid=$this->_post('pid','','缺少商品ID');
        $count=$this->_post('count',0,'缺少商品数量');
        $addressid=$this->_post('addressid','','缺少收货地址ID');
        //TODO:这里需要根据运费模板计算运费

        $this->apiReturnSuc(0);
    }

}