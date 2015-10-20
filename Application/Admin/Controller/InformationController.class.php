<?php
namespace Admin\Controller;
use Admin\Api\InformationRecordsApi;
use Shop\Api\SuggestApi;

/**
 * Created by PhpStorm.
 * User: zcs
 * Date: 2015/9/9
 * Time: 10:17
 */
class InformationController extends AdminController{


    //发件箱
    public function outbox(){
        $map=array(
            'type'=>3,
            'receiver'=>array('neq',UID)
        );
        $order='sendtime desc';
        $result= apiCall(InformationRecordsApi::QUERY_WITH_NAME,array($map,$order));
        if($result['status']){
            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
        }else{

        }
        $this->display();
    }

    //收件箱
    public function inbox(){

        //TODO:之后可能要做回复功能，可能需要添加数据表字段用于记录所回复的短信父项。
        $map=array(
            'type'=>3,
            'receiver'=>UID
        );
        $order='sendtime desc';
        $result= apiCall(InformationRecordsApi::QUERY_WITH_NAME,array($map,$order,2));
        if($result['status']){
            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
        }else{

        }
        $this->display();
    }

    //发送站内信
    public function sendLetter(){
        if(IS_GET){

            $this->display();
        }else{
            $receiver=I('post.uid','');    //目标，接收人
            $title=I('post.title','');  //标题
            $content=I('post.content',''); //内容
            $result=$this->add(UID,$receiver,$title,$content,0,3);
            if($result['status']){
                //
                //$this->
                $this->success("发送成功!",U('Admin/Information/outbox'));
            }else {
                $this->error("发送失败!");
            }
        }

    }


    /**
     * @param $uid    当前用户ID
     * @param $receiver  接收人，可以是用户ID,手机，邮箱
     * @param $title  标题
     * @param $content 内容
     * @param $isRead 是否阅读
     * @param $type  类型1短信，2邮箱，3站内信
     * @return mixed
     */
    private function add($uid,$receiver,$title,$content,$isRead,$type){
        $map=array(
            'uid'=>$uid,
            'receiver'=>$receiver,
            'title'=>$title,
            'content'=>$content,
            'isRead'=>$isRead,
            'type'=>$type,
            'sendtime'=>time(),
        );
       return apiCall(InformationRecordsApi::ADD,array($map));
    }


    //短信
    public function shortMessage(){
        $this->display();
    }

    //发送短信
    public function sendMessage(){
        $this->display();
    }


    //邮件
    public function email(){
        $this->display();
    }

    //发送邮件
    public function sendEmail(){
        $this->display();
    }

    //删除
    public function delete(){
        $map=array(
            'id'=>I('id'),
        );
        $result=apiCall(InformationRecordsApi::DELETE,array($map));
        if($result['status']){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }

    }

    //查看站内信详情
    public function view(){
        if(IS_GET){
            $map=array(
               'id'=>I('id'),
            );
            $result=apiCall(InformationRecordsApi::GET_INFO,array($map));
            if($result['status']){
                $this->assign('record',$result['info']);
                $this->display();
            }

        }
    }


    //查看站内信详情
    public function view2(){
        if(IS_GET){
            $id=I('id');
            $map=array(
                'isRead'=>1
            );
            apiCall(InformationRecordsApi::SAVE_BY_ID,array($id,$map));
            $map=array(
                'id'=>$id,
            );
            $result=apiCall(InformationRecordsApi::GET_INFO,array($map));
            if($result['status']){
                $this->assign('record',$result['info']);
                $this->display();
            }

        }
    }


    /**
     * 意见建议反馈
     */
    public function feedback(){
        $result=apiCall(SuggestApi::QUERY);
        if($result['status']){
            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
        }else{

        }
        $this->display();
    }

    /**
     * 删除反馈
     */
    public function deleteFeedback(){
        $map=array(
            'id'=>I("id"),
        );
        $result=apiCall(SuggestApi::DELETE,array($map));
        if($result['status']){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

    /**
     * 查看
     */
    public function view3(){
        $map=array(
            'id'=>I('id'),
        );
        $result=apiCall(SuggestApi::GET_INFO,array($map));
        if($result['status']){
            $this->assign('suggest',$result['info']);
            $this->display();
        }
    }

}