<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 09:29
 */

namespace Common\Api;

use Admin\Api\MemberApi;
use Admin\Api\UidMgroupApi;
use Common\Model\ValidUserInfoModel;
use Common\Model\WxuserGroupModel;
use Shop\Api\MemberConfigApi;
use Uclient\Api\UserApi;
use Uclient\Model\UcenterMemberModel;
use Weixin\Api\WxuserApi;

interface IAccount
{


    function login($username, $password,$type);

    function register($entity);

    function getInfo($id);

    function update($uid,$entity);


    function updatePwd($uid,$oldPwd,$newPwd);

}

/**
 * 本系统账号相关操作统一接口
 * Class AccountApi
 * @package Common\Api
 */
class AccountApi implements IAccount
{

    /**
     *  根据登录用户微信ID获取用户信息
     */
    const GET_INFO_BY_WXOPENID = "Common/Account/getInfoByWxOpenID";
    /**
     * 登录
     */
    const LOGIN = "Common/Account/login";
    /**
     * 注册
     */
    const REGISTER = "Common/Account/register";
    /**
     * 获取用户信息
     */
    const GET_INFO = "Common/Account/getInfo";

    const UPDATE="Common/Account/update";

    const QUERY = "Common/Account/query";

    public function query($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false) {

        $query = new ValidUserInfoModel();
        if(!is_null($map)){
            $query = $query->where($map);
        }
        if(!($order === false)){
            $query = $query->order($order);
        }
        if(!($fields === false)){
            $query = $query->field($fields);
        }
        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();

        $query = new ValidUserInfoModel();
        if ($list === false) {
            $error =$query -> getDbError();
            return array('status'=>false,'info'=>$error);
        }

        $count = $query -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new \Think\Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return array('status'=>true,'info'=>array("show" => $show, "list" => $list));
    }

    /**
     * @param $uid 用户ID
     * @param $oldPwd 老密码
     * @param $newPwd 新密码
     */
    public function updatePwd($uid,$oldPwd,$newPwd){
        //根据用户ID查询用户

    }


    /**
     * 用户更新,不包括修改密码,可更新字段如下:
     * email
     * mobile
     * head
     * qq
     * sex
     * birthday
     * idnumber
     * nickname
     * realname
     * 存在$data
     */
    public function update($uid,$data){
//        $trans = M();
//        $trans->startTrans();
        $map=array(
        );

        if(isset($data['email'])){
            $map['email'] = $data['email'];
        }
        if(isset($data['mobile'])) {
            $map['mobile'] = $data['mobile'];
        }
        if(count($map) == 0){
            //无须更新
            $result['status'] = true;
        }else{
            $result= apiCall(UserApi::SAVE_BY_ID,array($uid,$map));
        }

        if($result['status']){
            $m=array(
              'uid'=>$uid
            );
            $map=array(
            );
            if(isset($data['head'])) {
                $map['head'] = $data['head'];
            }
            if(isset($data['qq'])) {
                $map['qq'] = $data['qq'];
            }
            if(isset($data['sex'])) {
                $map['sex'] = $data['sex'];
            }
            if(isset($data['birthday'])) {
                $map['birthday'] = $data['birthday'];
            }
            if(isset($data['idnumber'])) {
                $map['idnumber'] = $data['idnumber'];
            }
            if(isset($data['nickname'])){
                $map['nickname'] = $data['nickname'];
            }
            if(isset($data['realname'])) {
                $map['realname'] = $data['realname'];
            }
            if(isset($data['card_no'])) {
                $map['card_no'] = $data['card_no'];
            }

            $result= apiCall(MemberApi::SAVE,array($m,$map));

            if($result['status']){
//                $trans->commit();
                return array('status' => true, 'info' => $result['info']);
            }else{
//                $trans->rollback();
                return array('status' => false, 'info' => $result['info']);
            }
        }else{
//            $trans->rollback();
            return array('status' => false, 'info' => $result['info']);
        }
    }

    /**
     * 获得用户信息
     * @param $id
     * @return array
     */
    public function getInfo($id){


        $result = apiCall(UserApi::GET_INFO, array($id));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => false, 'info' => '未知错误code=1'.$id);
        }
        //1. 存储userApi里的
        $user_info = $result['info'];

        $result = apiCall(MemberApi::GET_INFO, array(array('uid'=>$id)));


        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => false, 'info' => '未知错误code=2'.$id);
        }

        $member_info = $result['info'];
        $result = apiCall(MemberConfigApi::GET_INFO, array(array('uid'=>$id)));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => false, 'info' => '未知错误code=3'.$id);
        }

        $member_config = $result['info'];

        $info = array_merge($user_info,$member_info,$member_config);
        $info = $this->convert($info);
        return array('status'=>true,'info'=>$info);
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

    /**
     * 获得用户信息
     * @param $id
     * @return array
     */
    public function getInfoByWxOpenID($openid){

        $result = apiCall(MemberConfigApi::GET_INFO, array(array('wxopenid'=>$openid)));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => true, 'info' => '1.无法获取用户信息'.$id);
        }

        $id = $result['info']['uid'];

        $member_config = $result['info'];

        $result = apiCall(UserApi::GET_INFO, array($id));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => true, 'info' => '2.无法获取用户信息'.$id);
        }
        $id = $result['info']['id'];

        $user_info = $result['info'];

        $result = apiCall(MemberApi::GET_INFO, array(array('uid'=>$id)));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }elseif(empty($result['info'])){
            return array('status' => true, 'info' => '3.无法获取用户信息'.$id);
        }

        $member_info = $result['info'];

        $info = array_merge($user_info,$member_info,$member_config);
        return array('status'=>true,'info'=>$info);
    }

    public function login($username, $password,$type)
    {
        // TODO: Implement login() method.
        $result=apiCall(UserApi::LOGIN,array($username, $password,$type));
        return $result;
    }

    /**
     *
     * @param $entity | key＝》username,password ,from,     .realname,email,mobile非必须
     *  type,
     *  username
     *  password,
     *  email,
     *  mobile,
     *  from,realname,nickname,birthday,idcode,head,sex
     * @return array
     */
    public function register($entity)
    {
        if (!isset($entity['username']) || !isset($entity['password']) || !isset($entity['from'])) {
            return array('status' => false, 'info' => "账户信息缺失!");
        }

        if(!isset($entity['idcode'])){
            return array('status'=>false,'info'=>"缺失IDCODE");
        }
        $type     = $entity['type'];
        $username = $entity['username'];
        $password = $entity['password'];
        $email = $entity['email'];
        $mobile = $entity['mobile'];
        $from = $entity['from'];
        $realname=$entity['realname'];
        $nickname=$entity['nickname'];
        $birthday= isset($entity['birthday'])?$entity['birthday']:'';
        $invite_id=isset($entity['invite_id'])?$entity['invite_id']:0;
        $IDCode = isset($entity['idcode'])?$entity['idcode']:'';
        $head = isset($entity['head'])?$entity['head']:'';
        $sex = isset($entity['sex'])?$entity['sex']:"";
        $weixin_bind = isset($entity['weixin_bind'])?$entity['weixin_bind']:0;
//        if(empty($IDCode)){
//        }
        //微信的openid
        $wxopenid  = isset($entity['wxopenid'])?$entity['wxopenid']:'';

        $trans = M();
        $trans->startTrans();
        $error = "";
        $flag = false;
        $result = apiCall(UserApi::REGISTER, array($username, $password, $email, $mobile, $from));
        $uid = 0;
  
        if ($result['status']) {
            $uid = $result['info'];
            $member = array(
                'uid' => $uid,
                'realname' => $realname,
                'nickname' => $nickname,
                'idnumber' => '',
                'sex' =>  $sex,
                'birthday' => $birthday,
                'qq' => '',
                'head'=>$head,
                'score' => 0,
                'login' => 0,

            );
   
            $result = apiCall(MemberApi::ADD, array($member));

            if (!$result['status']) {
                $flag = true;
                $error = $result['info'];
            }else{

            }
        } else {
            $flag = true;
            $error = $result['info'];
        }

        if(!$flag){

            //TODO: 插入到第三张表
            $map=array(
                'uid'=>$uid,
                'phone_validate'=>0,
                'email_validate'=>0,
                'weixin_bind'=>$weixin_bind,
                'IDCode'=>$IDCode,
                'wxopenid'=>$wxopenid,
                'identity_validate'=>0,
                'invite_id'=>$invite_id,
            );

            if($type == UcenterMemberModel::ACCOUNT_TYPE_MOBILE) {
                $map['phone_validate'] = 1;
            }

            $result = apiCall(MemberConfigApi::ADD,array($map));
            if(!$result['status'] ){
                $flag = true;
                $error = $result['info'];
            }
        }


        if ($flag) {
            $result = apiCall(UserApi::DELETE_BY_ID, array($uid));
            $trans->rollback();

            return array('status' => false, 'info' => $error);
        } else {
            $trans->commit();
            return array('status' => true, 'info' => $uid);
        }

    }

}