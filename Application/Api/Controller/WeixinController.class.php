<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/12
 * Time: 23:34
 */

namespace Api\Controller;

use Api\Service\WeixinService;
use Common\Api\AccountApi;
use Shop\Api\MemberConfigApi;
use Uclient\Model\OAuth2TypeModel;
use Uclient\Model\UcenterMemberModel;

class WeixinController extends ApiController{

    private $appid;
    private $appsecret;

    public function _init(){
        //微信开放应用的appid和appsecret
        $this->appid = "wx0d259d7e9716d3dd";
        $this->appsecret = "94124fb74284c8dae6f188c7e269a5a0";
    }

    /**
     * 微信绑定
     */
    public function bind(){

        $code =  I('get.code','');
        $uid = $this->_post('uid','');
        addLog("Weixin/bind",$_GET,$_POST,"调用绑定接口CODE=".$code);

        $service = new WeixinService($this->appid,$this->appsecret);

        $result = $service->getUserInfo($code);

        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }
        $weixin_userinfo = $result['info'];

        $wxopenid = $weixin_userinfo['openid'];

        $result = apiCall(AccountApi::GET_INFO_BY_WXOPENID,array($wxopenid));

        //如果已经存在，则返回信息
        if($result['status'] && is_array($result['info'])){
            $this->apiReturnErr("该微信已经绑定另一个账户!");
        }

        $entity = array(
            'wxopenid'=>$wxopenid,
            'weixin_bind'=>1,
        );

        $result = apiCall(MemberConfigApi::SAVE_BY_ID,array($uid,$entity));

        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }else{

        }


    }

    /**
     * 微信注册
     */
    public function login(){
        $code =  I('get.code','');

        addLog("Weixin/login",$_GET,$_POST,"调用微信登录接口CODE=".$code);

        $service = new WeixinService($this->appid,$this->appsecret);

        $result = $service->getUserInfo($code);
        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }

        $weixin_userinfo = $result['info'];

        addLog("Weixin/login",$weixin_userinfo,$weixin_userinfo,"微信登录拉取的用户信息");
        //注册用户账户
        /*
         *
         *  ["openid"] => string(28) "ooQDbsnArKx1iBCUp05EfFeOP8f0"
    ["nickname"] => string(19) "老胖子-何必都"
    ["sex"] => int(1)
    ["language"] => string(5) "zh_CN"
    ["city"] => string(8) "Hangzhou"
    ["province"] => string(8) "Zhejiang"
    ["country"] => string(2) "CN"
    ["headimgurl"] => string(129) "http://wx.qlogo.cn/mmopen/HqJEBzbSXQqNzVkND8UsQ8Ric4XkkCNfeVWYT71lAM6ZxuhRhicZdXB3HR5ibc2SsWmXt1ptdbibL6Xk0tjJWJ7MJS8vNE87iaSb6/0"
    ["privilege"] => array(0) {
    }
    ["unionid"] => string(28) "o_4WajjRYUsu6qM3Fn3NvnctZrg0"
         *
         * */
        $openid = $weixin_userinfo['openid'];
        $password = "BM123456";
        $nickname = $weixin_userinfo['nickname'];
        $sex = $weixin_userinfo['sex'];
        $city = $weixin_userinfo['city'];
        $province = $weixin_userinfo['province'];
        $country = $weixin_userinfo['country'];
        $head = $weixin_userinfo['headimgurl'];
        $idcode = getIDCode(rand(10000000000,99999999999),'X');

        $result = apiCall(AccountApi::GET_INFO_BY_WXOPENID,array($openid));

        addLog("Weixin/login",$result,$result,"微信登录拉取的用户信息");
        //如果已经存在，则返回信息
        if($result['status'] && is_array($result['info'])){

            $result['info'] =  $this->convert($result['info']);
            $this->apiReturnSuc($result['info']);
        }elseif($result['status'] === false){
            $this->apiReturnErr($result['info']);
        }

        $entity = array(
            'type'=> UcenterMemberModel::ACCOUNT_TYPE_USERNAME,
            'username' => $openid,
            'password' => $password,
            'from' => OAuth2TypeModel::WEIXIN,
            'mobile' => '',
            'realname' => '',
            'nickname' => $nickname ,
            'email' => '',
            'idnumber' => '',
            'birthday' => time(),
            'idcode'=>$idcode,
            'head'=>$head,
            'weixin_bind'=>1, //绑定微信
            'wxopenid'=>$openid,
        );

        $result = apiCall(AccountApi::REGISTER, array($entity));

        if ($result['status']) {
            $uid = $result['info'];
            if($uid > 0){
                $result = apiCall(AccountApi::GET_INFO,array($uid));

                action_log("api_user_login", "common_member", $uid, $uid);
                if($result['status']){
                    $this->apiReturnSuc($result['info']);
                }
            }
        }


        $this->apiReturnErr($result['info']);


    }

    /**
     * 对用户信息进行转换
     * @param $user
     * @return mixed
     */
    private function convert($user){

        if((strpos($user['head'],"http") === 0)) {
            return $user;
        }
        $pic_id = intval($user['head']);

        if($pic_id > 0){
            $user['head'] = C('API_URL').'/Picture/index?id='.$pic_id;
        }

        return $user;

    }

    function getSupportMethod()
    {
        // TODO: Implement getSupportMethod() method.
    }
}