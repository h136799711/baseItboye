<?php
namespace Api\Controller;

use Admin\Api\SecurityCodeApi;
use Admin\Model\SecurityCodeModel;
use Api\Service\SMSServices;
use Uclient\Api\UserApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/1
 * Time: 17:55
 */
class MessageController extends ApiController
{

    protected $allowType = array("json", "rss", "html");

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


    /*
        ***聚合数据（JUHE.CN）数据接口调用通用DEMO SDK
        ***DATE:2014-04-14
    */
    public function send()
    {
//        addLog('Message/send',serialize(I('get.')).serialize($_COOKIE).serialize($_REQUEST).serialize($_FILES),serialize($_POST).serialize($_SESSION),$notes);

        $mobile = $this->_post("mobile");
         /*
         * 验证类型
         */
        $type = $this->_post("type","");
        addLog("Message/send", $_GET, $_POST,$this->client_id."用于".$notes.",发送了验证码");
        if($type==""){
            $this->apiReturnErr("type参数不能为空!");
        }
        if($type==1){
            $notes="注册";
        }else if($type==2){
            $notes="密码更新";
        }

        if(empty($mobile)){
            $this->apiReturnErr("手机号不能为空!");
        }

        //判断该手机是否已经注册过了
        $map = array(
                'mobile' => $mobile,
            );

        $result = apiCall(UserApi::FIND, array($map));

        if($type == SecurityCodeModel::TYPE_FOR_REGISTER){
            if ($result['info'] != null) {
                $this->apiReturnErr("该手机号已注册!");
            }
        }elseif($type == SecurityCodeModel::TYPE_FOR_UPDATE_PSW ){
            if ($result['info'] == null) {
                $this->apiReturnErr("该手机号未注册!");
            }
        }

        $appkey = C('JUHE_API.MSG_APPKEY'); #通过聚合申请到数据的appkey
        $tpl_id = C('JUHE_API.MSG_TPL_ID');

        $code = rand(100000, 999999);
        $sms_config = array(
            'type'=>SMSServices::JUHE_SMS,//聚合短信
            'appkey' => $appkey, //您申请的APPKEY
            'mobile' => $mobile, //接受短信的用户手机号码
            'tpl_id' => $tpl_id, //您申请的短信模板ID，根据实际情况修改
            'content' => '#code#=' . $code //您设置的模板变量，根据实际情况修改
        );

        $entity = array(
            'code' => $code,
            'accepter' => $mobile,
            'starttime' => time(),
            'endtime' => time() + 1800,
            'ip' => ip2long(get_client_ip()),
            'client_id' => $this->client_id,
            'type'=>$type,
            'status'=>0,// 未验证
        );

        $result = apiCall(SecurityCodeApi::ADD, array($entity));

        if (!$result['status']) {
            LogRecord($result['info'], __FILE__ . __LINE__);
            $this->apiReturnErr($result['info']);
        }

        $this->apiReturnSuc($code);


        //开发环境下关闭短信发送

//            $result = SMSServices::send($sms_config);
//
//            $error_code = "-1";
//            $msg = "";
//            if ($result) {
//                $json_result = json_decode($result, true);
//                $error_code = $json_result['error_code'];
//                $msg = $json_result['reason'];
//
//                if (intval($error_code) == 0) {
//                    //状态为0，说明短信发送成功
//                    $this->apiReturnSuc("短信发送成功,短信ID：" . $json_result['result']['sid']);
//                }
//
//            }
//
//            $this->apiReturnErr("短信发送失败(" . $error_code . ")：" . $msg);




    }

    /*
     * 验证码验证
     */
    public function checkCode()
    {

        $code=$this->_post('code','');
        $mobile=$this->_post('username','');
        $type = $this->_post('type','');
        $note = '[未知]';
        if($type == SecurityCodeModel::TYPE_FOR_REGISTER){
            $note = "[注册]";
        }elseif($type == SecurityCodeModel::TYPE_FOR_UPDATE_PSW ){
            $note = "[找回密码]";
        }elseif($type == SecurityCodeModel::TYPE_FOR_NEW_BIND_PHONE){
            $note = "[绑定新手机]";
        }

        addLog("Message/checkCode", $_GET, $_POST,"用户".$mobile."用于".$note.",发送了验证码");


        if(empty($code) || empty($mobile) || empty($type)){
            $this->apiReturnErr("参数缺失!");
        }

        $result = apiCall(SecurityCodeApi::IS_LEGAL_CODE,array($code,$mobile,$type));

        addLog("Message/checkCode",$result,'',"验证码验证结果!");
        if($result['status'] && $result['info'] == 1){
            $this->afterSuccess($type);
            $this->apiReturnSuc("验证通过!");
        }else{
            $this->apiReturnErr("验证失败!");
        }

    }

    private function afterSuccess($type){

    }

}