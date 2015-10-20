<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/24
 * Time: 09:17
 */

namespace Api\Controller;



use Admin\Api\MemberApi;
use Uclient\Api\UserApi;

class FileController extends ApiController
{
    protected $Accept_Type = array('avatar','gallery','other');

    public function upload()
    {
        addLog("File/upload",$_GET,$_POST,$this->client_id."调用文件上传接口!");
        if (IS_POST) {

            $uid = $this->_post('uid',0);
            $type = $this->_post('type','');

            if(!in_array($type,$this->Accept_Type)){
                $this->apiReturnErr("文件类型不支持!");
            }

            if($uid <= 0){
                $this->apiReturnErr("用户ID非法!");
            }

            $result = apiCall(UserApi::GET_INFO,array($uid));
            if(!$result['status']){
                $this->apiReturnErr("用户ID不存在!");
            }
            if(!isset($_FILES['image'])){
                $this->apiReturnErr("文件对象必须为image!");
            }

            $result['info'] = "";
            //2.再上传到自己的服务器，
            //TODO:也可以上传到QINIU上
            /* 返回标准数据 */

            /* 调用文件上传组件上传文件 */
            $Picture = D('UserPicture');
            $extInfo = array('uid' => $uid,'imgurl' => C('SITE_URL'),'type'=>$type);
            $info = $Picture->upload(
                $_FILES,
                C('USER_PICTURE_UPLOAD')
                ,$extInfo
            );

            /* 记录图片信息 */
            if($info){
                $info['image']['imgurl'] = C('SITE_URL').$info['image']['path'];

                if($type == 'avatar'){
                    //保存到用户信息表
                    $result = $this->saveToUserHead($uid,$info['image']['id']);
                    $head = C('API_URL').'/Picture/index?id='.$info['image']['id'];
                    $info['image']['api_img_url'] = $head;
                    if($result['status']){
                        $this->apiReturnSuc($info['image']);
                    }else{
                        $this->apiReturnErr($result['info']);
                    }
                }

            } else {
                $this->apiReturnErr($Picture->getError());
            }

        }
    }

    /**
     * 保存图片ID到用户信息的头像字段中
     * @param $uid      用户ID
     * @param $pic_id   图片ID
     * @return mixed
     */
    public function saveToUserHead($uid,$pic_id){

        $result = apiCall(MemberApi::SAVE_BY_ID,array($uid,array('head'=>$pic_id)));

        return $result;
    }

    public function getSupportMethod(){

    }

}