<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 20:22
 */

namespace Api\Controller;


use Api\Vendor\crypt\DesCrypt;
use OAuth2Manage\Api\AccessTokensApi;
use Think\Controller\RestController;
use Admin\Api\ConfigApi;

/**
 * 接口基类
 * Class ApiController
 *
 * @author 老胖子-何必都 <hebiduhebi@126.com>
 * @package Api\Controller
 */
abstract class ApiController extends RestController{

    protected $encrypt_key = "";
    protected $client_id = "";
    /**
     * 构造函数
     */
    public function __construct(){
        parent::__construct();

        if(method_exists($this,"_init")){
            $this->_init();
        }

    }

    protected function decodePost(){

        $post = $this->_post('itboye','');
        if(!empty($post)){
            $data = DesCrypt::decode(base64_decode($post),$this->client_id);
            $data = $this->filter_post($data);
            $obj = json_decode($data,JSON_OBJECT_AS_ARRAY);
            $_POST = $obj;
        }else{
            $_POST = array();
        }


    }

    /**
     * 过滤末尾多余空白符 ASCII码等于7的奇怪符号
     * @param $post
     * @return string
     */
    protected function filter_post($post){
        $post = trim($post);
        for ($i=strlen($post)-1;$i>=0;$i--) {
            $ord = ord($post[$i]);
            if($ord > 31 && $ord != 127){
                $post = substr($post,0,$i+1);
                return $post;
            }
        }
        return $post;
    }

    protected function _init(){
        $access_token = I("get.access_token");
        if(empty($access_token)){
            $access_token = I("post.access_token");
            unset($_POST['access_token']);
        }
        if(empty($access_token)){
            $this->apiReturnErr("缺失access_token!");
        }

        $_GET['access_token'] = $access_token;
        //TODO: 对POST过来的传输数据进行解密,并将解密后的数据放入$_POST变量中
//        if(APP_DEBUG){
//            addLog("Api/_init",$_GET,$_POST,"基类验证!");
//        }
        $resCtrl = new ResourceController();

        $result = $resCtrl->authorize();

        if($result['status'] !== 0){
            $this->apiReturnErr($result['info'],$result['status']);
        }
        $result = apiCall(AccessTokensApi::GET_INFO,array(array('access_token'=>$access_token)));
        if($result['status']){
            $this->client_id = $result['info']['client_id'];
        }

//        $this->decodePost();

        $this->getConfig();
    }


    public function _empty(){
        $this->apiReturnErr("访问地址错误","404");
    }

    /**
     * ajax返回
     * @param $data
     * @internal param $i
     */
    protected function apiReturnSuc($data){
         $this->ajaxReturn(array('code'=>0,'data'=>$data),"json");
    }

    /**
     * ajax返回，并自动写入token返回
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data,$code=-1){
        $this->ajaxReturn(array('code'=>$code,'data'=>$data),"json");
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _post($key,$default='',$emptyErrMsg=''){
        $value = I("post.".$key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * @param $key
     * @param string $default
     * @param string $emptyErrMsg  为空时的报错
     * @return mixed
     */
    public function _get($key,$default='',$emptyErrMsg=''){
        $value = I("get.".$key,$default);

        if($default == $value && !empty($emptyErrMsg)){
            $this->apiReturnErr($emptyErrMsg);
        }
        return $value;
    }

    /**
     * 从数据库中取得配置信息
     */
    protected function getConfig() {
        $config = S('global_config');

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
                S("global_config", $config, 300);
            } else {
                LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
                $this -> error($result['info']);
            }
        }

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