<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 14:22
 */
namespace Api\Controller;

use Admin\Api\MemberApi;
use Admin\Api\SecurityCodeApi;
use Common\Api\AccountApi;
use Shop\Api\MemberConfigApi;
use Uclient\Api\UserApi;
use Uclient\Model\OAuth2TypeModel;
use Uclient\Model\UcenterMemberModel;
use Admin\Model\SecurityCodeModel;

class UserController extends ApiController
{

    protected $business_code = '101';

    protected $allowType = array("json", "rss", "html");

    function getSupportMethod()
    {
        return array(
            'item' => array(
                'param' => 'access_token|username|password',
                'return' => 'array(\"status\"=>返回状态,\"info\"=>\"信息\")',
                'author' => 'hebidu [hebiduhebi@163.com]',
                'version' => '1.0.0',
                'description' => '用户登录验证',
                'demo_url' => 'http://manual.itboye.com#',
            ),
        );

    }

    /**
     * 更新身份证号，实名认证
     */
    public function updateIDNumber(){
        addLog("User/updateIDNumber", $_GET, $_POST, $this->client_id . "调用实名认证接口!");

        $uid = $this->_post('uid', 0,"用户ID无效!");
        $name = $this->_post('name', '','真实姓名参数缺失!');
        $idnumber = $this->_post('idnumber', '',"身份证号参数缺失!");

        $result = apiCall(MemberConfigApi::SAVE_BY_ID,array($uid,array('identity_validate'=>1)));

        if(!$result['status']){
            $this->apiReturnErr($result['info'],$this->business_code.'01');
        }

        $entity = array(
            'realname'=>$name,
            'idnumber'=>$idnumber,
        );

        $result = apiCall(MemberApi::SAVE_BY_ID,array($uid,$entity));

        if(!$result['status']){
            $this->apiReturnErr($result['info'],$this->business_code.'02');
        }else{
            $this->apiReturnSuc($result['info']);
        }
    }

    /**
     * 更换手机号
     */
    public function changePhone(){

        if(IS_POST) {
            addLog("User/changePhone", $_GET, $_POST, $this->client_id . "调用用户手机更换接口!");
            $password = $this->_post('password', '');
            $uid = $this->_post('uid', 0,"用户ID无效!");
            $code = $this->_post('code', '',"密码无效");
            $mobile = $this->_post('mobile', '',"新手机无效");//新手机


            $type = SecurityCodeModel::TYPE_FOR_CHANGE_NEW_PHONE;
            //1. 验证－验证码
            $result = apiCall(SecurityCodeApi::IS_LEGAL_CODE, array($code, $mobile, $type));

            if (!$result['status'] || empty($result['info'])) {
                $this->apiReturnErr("验证码错误!",$this->business_code.'10');
            }

            //2. 验证新手机是否已经被其它账户绑定过
            $result = apiCall(UserApi::FIND,array(array('mobile'=>$mobile)));
            if($result['status'] && is_array($result['info'])){
                $this->apiReturnErr("无法绑定该手机号，因为已经绑定其它账号!",$this->business_code.'11');
            }

            //3. 验证-密码是否有限
            $result = apiCall(UserApi::FIND,array(array('id'=>$uid)));
            $mdPsw = itboye_ucenter_md5($password,UC_AUTH_KEY);

            if (!$result['status']) {
                $this->apiReturnErr($result['info'],$this->business_code.'12');
            }
            if($mdPsw != $result['info']['password']){
                $this->apiReturnErr("密码错误!".$mdPsw,$this->business_code.'13');
            }

            $this->updateMobile($uid,$mobile);

            $this->apiReturnSuc("更换成功!");

        }

    }

    /**
     * 新手机绑定
     */
    public function bind(){

        if(IS_POST){
            addLog("User/bind",$_GET,$_POST,$this->client_id."调用用户手机绑定接口!");
            $username = $this->_post('username','',"手机不能为空");//传入一般为手机号
            $uid = $this->_post('uid',0,"uid为空");
            $code = $this->_post('code','',"验证码错误");

            $type = SecurityCodeModel::TYPE_FOR_NEW_BIND_PHONE;

            $result = apiCall(SecurityCodeApi::IS_LEGAL_CODE,array($code,$username,$type));

            if (!$result['status'] || empty($result['info'])) {
                $this->apiReturnErr("验证码错误!",$this->business_code.'20');
            }

            //验证通过，表明手机可用

            $result = apiCall(UserApi::FIND,array(array('mobile'=>$username)));
            if($result['status'] && is_array($result['info'])){
                $this->apiReturnErr("该手机号已绑定另外一个账号!",$this->business_code.'21');
            }

            $this->updateMobile($uid,$username);

        }

        $this->apiReturnErr("非法请求!",$this->business_code.'22');

    }

    /**
     * 更新密码
     */
    public function updatePsw(){
        addLog("User/updatePsw",$_GET,$_POST,'应用'.$this->client_id."调用更新密码接口");
        //
        if(IS_POST){

            $username = $this->_post('username','');
            $old_psw = $this->_post('old_psw','');
            $psw = $this->_post('psw','');
            $code = $this->_post('code','');

            if(empty($old_psw) && empty($code)){
                $this->apiReturnErr("验证参数缺失!");
            }

            //验证码存在时，排除密码
            if(!empty($code)){
                $old_psw = '';
            }

            $old_psw = base64_decode($old_psw);
            $psw = base64_decode($psw);
            $type = $this->getUsernameType($username);
            $result = array('status');
            if($type == UcenterMemberModel::ACCOUNT_TYPE_MOBILE){
                $result = apiCall(UserApi::FIND,array(array('mobile'=>$username)));
            }elseif($type == UcenterMemberModel::ACCOUNT_TYPE_USERNAME){
                $result = apiCall(UserApi::FIND,array(array('username'=>$username)));
            }else{
                $this->apiReturnErr("参数非法!");
            }


            if (!$result['status'] && empty($result['info'])) {
                $this->apiReturnErr("用户登录账户非法!");
            }

            $id = $result['info']['id'];
            addLog("id",$id,$psw,"");
            //**************检测是否合法用户，要修改密码，必须确保用户身份有权限******

            if(!empty($code)){

                $type = SecurityCodeModel::TYPE_FOR_UPDATE_PSW; //

                $result = apiCall(SecurityCodeApi::IS_LEGAL_CODE,array($code,$username,$type));
                if(!$result['status'] || $result['info'] != 1){
                    $this->apiReturnErr("验证失败");
                }

            }elseif(!empty($old_psw)) {
                if ($result['info']['password'] != think_ucenter_md5($old_psw, UC_AUTH_KEY)) {
                    $this->apiReturnErr("原密码错误!");
                }
            }


            //**************************************************************
            if(strlen($psw) < 6){
                $this->apiReturnErr("密码必须大于6位长度!");
            }
            addLog("User/updatePsw",$_GET,$_POST,'应用'.$this->client_id."调用更新密码接口");
            $result = apiCall(UserApi::UPDATEPWD,array($id,$psw));
            //记录成功更新密码的日志
            action_log("api_user_update_psw", "common_member", $id,$this->client_id);

            if (!$result['status']) {
                $this->apiReturnErr("更新密码失败!".$result['info']);
            }

            $this->apiReturnSuc("更新密码成功!");

        }else{
            $this->apiReturnErr("更新密码失败!");
        }
    }

    /**
     * POST: 登录
     * @internal param post.username
     * @internal param post.password
     */
    public function login()
    {

        $username = $this->_post("username","","用户名缺失");

        $password = $this->_post("password","","密码缺失");

        $type = $this->getUsernameType($username);

        $notes = "应用" . $this->client_id . ":[用户" . $username . "],调用登录接口,密码：" . $password;

        addLog("User/login", $_GET, $_POST, $notes);

        $result = apiCall(AccountApi::LOGIN, array($username, $password, $type));

        if ($result['status']) {
            $uid = $result['info'];

            $result = apiCall(AccountApi::GET_INFO, array($uid));
            if(!$result['status']){
                $this->apiReturnErr($result['info']);
            }
            $userinfo = $result['info'];
            $userinfo['head'] = getImageUrl($userinfo['head']);
            action_log("api_user_login", "common_member", $uid, $uid);
            $this->apiReturnSuc($result['info']);
        } else {
            $this->apiReturnErr($result['info']);
        }

    }

    /**
     * POST: 注册
     * username 用户名
     * password 密码
     * mobile 手机
     * realname真实姓名
     * email 电子邮箱
     * idnumber身份证号
     * birthday生日
     */
    public function register()
    {


        $notes = "应用" . $this->client_id . "，调用注册接口";

        addLog("User/register", $_GET, $_POST, $notes);
        if (IS_POST) {

            $type = $this->_post("type", 1);
            $username = $this->_post("username", "");
            $from = $this->_post("from", "");
            $invite_code= $this->_post("invite_code", "");

            $invite_id = $this->getInviteID($invite_code);

            $error = $this->isLegal($type,$username,$from);

            if (!($error === false)) {
                $this->apiReturnErr($error);
            }

            $password = $this->_post("password");
            $email = "";
            $mobile = "";
            $idcode = "123456";
            if($type == UcenterMemberModel::ACCOUNT_TYPE_EMAIL){
                $email = $username;
//                $idcode = getIDCode(rand(10000000000,99999999999),'E');

            }elseif($type == UcenterMemberModel::ACCOUNT_TYPE_MOBILE){
                $mobile = $username;
                $username = 'M'.$mobile;
//                $idcode = getIDCode($mobile,'M');
            }

            if(empty($idcode)){
                $this->apiReturnErr("注册失败!请重试");
            }
            $nickname = $this->_post("nickname", "昵称");

            $entity = array(
                'username' => $username,
                'password' => $password,
                'from' => $from,
                'mobile' => $mobile,
                'realname' => '',
                'nickname' => $nickname ,
                'email' => $email,
                'idnumber' => '',
                'birthday' => time(),
                'idcode'=>$idcode,
                'type'=>$type,
                'invite_id'=>$invite_id,
            );

            $result = apiCall(AccountApi::REGISTER, array($entity));

            if ($result['status']) {

                $this->apiReturnSuc($result['info']);
            } else {
                $this->apiReturnErr($result['info']);
            }
        } else {
            $this->apiReturnErr("只支持POST请求!");
        }

    }

    private function getInviteID($invite_code){
        //TODO: 获取邀请码

        //

        return "";
    }

    /**
     * 用户信息更新
     */
    public function update()
    {

        $notes = "应用" . $this->client_id . "，调用用户更新接口";

        addLog("User/update", $_GET, $_POST, $notes);

        if (IS_POST) {
            $uid = $this->_post('uid', 0,'intval');

            if($uid <= 0){
                $this->apiReturnErr("用户ID非法!");
            }

            $mobile = $this->_post("mobile", "");
            $realname = $this->_post("realname", "");
            $email = $this->_post("email", "");
            $idnumber = $this->_post("idnumber", "");
            $birthday = strtotime($this->_post("birthday", 0));
            $nickname = $this->_post("nickname", "");
            $sex = $this->_post("sex", 0);
            $qq = $this->_post("qq", "");
            $head = $this->_post("head", "");
            $card_no = $this->_post("card_no", "");

            if($sex != 0 && $sex != 1){
                $this->apiReturnErr("性别参数错误!");
            }

            $entity = array();

            if(!empty($card_no)){
                $entity['card_no'] = $card_no;
            }

//            if(!$sex)){
                $entity['sex'] = $sex;
//            }
            if(!empty($head)){
                $entity['head']=$head;
            }

            if(!empty($qq)){
                $entity['qq']=$qq;
            }
            if(!empty($birthday)){
                $entity['birthday']=$birthday;
            }
            if(!empty($idnumber)){
                $entity['idnumber']=$idnumber;
            }
            if(!empty($email)){
                $entity['email'] = $email;
            }
            if(!empty($realname)){
                $entity['realname'] = $realname;
            }
            if(!empty($nickname)){
                $entity['nickname'] = $nickname;
            }

            if(!empty($mobile)){
                $entity['mobile'] = $mobile;
            }

            $result = apiCall(AccountApi::UPDATE, array($uid, $entity));

            if ($result['status']) {
                $this->apiReturnSuc("操作成功！");
            } else {
                $this->apiReturnErr($result['info']);
            }
        }
    }

    /**
     * 检查这些参数是否合法
     * @param $type
     * @param $username
     * @param $from
     * @return bool|string
     */
    private function isLegal($type,$username,$from){

        //TODO: 检查from的值是否合法
        //TODO: 检测用户账号是否合法
        //1. $type = 1 的时候 $username 是用户名
        //2. $type = 2 的时候 $username 是邮箱,应该符合邮箱规则
        //3. $type = 3 的时候 $username 是手机,应该符合手机的规则
        $from = intval($from);
        if($from != OAuth2TypeModel::BAIDU
            && $from != OAuth2TypeModel::OTHER_APP
            && $from != OAuth2TypeModel::QQ
            && $from != OAuth2TypeModel::SINA
            && $from != OAuth2TypeModel::WEIXIN
            && $from != OAuth2TypeModel::SELF){
            return "用户来源参数无效";
        }

        if($type == UcenterMemberModel::ACCOUNT_TYPE_MOBILE && $this->getUsernameType($username) != $type){
                $this->apiReturnErr("手机号非法!");
        }

        $notes=  "检测用户名:".$username."来源";

        $result =  apiCall(UserApi::CHECK_USER_NAME,array($username));
        if(!$result['status']){
            return "用户名已占用!";
        }

        $result = apiCall(UserApi::CHECK_MOBILE,array($username));
        if(!$result['status']){
            return "手机号已占用!";
        }



        addLog("isLegal",$result,"",$notes);

        return false;

    }

    /**
     * 更新用户手机号
     * @param $uid
     * @param $mobile
     */
    private function updateMobile($uid,$mobile){

        $entity = array(
            'phone_validate'=>1,
        );

        $result = apiCall(UserApi::SAVE_BY_ID,array($uid,array('mobile'=>$mobile)));
        if($result['status']){
            $result = apiCall(MemberConfigApi::SAVE_BY_ID,array($uid,$entity));
            if($result['status']){
                $this->apiReturnSuc("绑定成功!");
            }else{
                $this->apiReturnErr($result['info']);
            }
        }else{
            $this->apiReturnErr($result['info']);
        }
    }

    /**
     * 检测登录账户的类型
     * 3: 手机号
     * 2:  EMAIL
     * @param $str
     * @return int
     */
    private function getUsernameType($str){

        if(preg_match("/^1\d{10}$/",$str)){
            return UcenterMemberModel::ACCOUNT_TYPE_MOBILE;
        }

        return UcenterMemberModel::ACCOUNT_TYPE_USERNAME;
    }

}