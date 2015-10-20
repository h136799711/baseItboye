<?php
namespace Admin\Controller;
use Admin\Api\MgroupApi;
use Admin\Api\UidMgroupApi;
use Shop\Api\StoreApi;
use Uclient\Api\UserApi;
use Admin\Api\MemberApi;

/**
 *
 * Created by PhpStorm.
 * User: zcs
 * Date: 2015/9/15
 * Time: 11:50
 */
class MemberGroupController extends AdminController{
    /**
     * 会员等级
     */
    public function groupIndex(){
        //根据当前登录的用户查
        $map=array(
            'uid'=>UID,
        );
        $result=apiCall(MgroupApi::QUERY,array($map));
        if($result['status']){
            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
        }else{

        }
        $this->display();
    }




    /**
     * 用户关联
     */
    public function uidMgroupIndex(){
        $id=I('id',0);
        $groupname=I('groupname',"");
        $map=array(
            'groupid'=>$id,
            'creator_uid'=>UID,
        );
        $this->assign('groupid',$id);
        $this->assign('groupname',$groupname);
        $result=apiCall(UidMgroupApi::QUERY_VIEW,array($map));
       if($result['status']){

            $this->assign('list',$result['info']['list']);
            $this->assign('show',$result['info']['show']);
            $map=array(
               'uid'=>UID,
                //'id'=>array('neq',$id),
            );
            $result=apiCall(MgroupApi::QUERY_NO_PAGING,array($map));
            $this->assign('groups',$result['info']);

        }else{

        }
        $this->display();
    }

    /**
     * 添加等级
     */
    public function addGroup(){
        if(IS_GET) {
            $this->display();
        }else{
            $name=I('name','');
            $remark=I('remark','');
            $discount_ratio=I('discount_ratio','');
            $commission_ratio=I('commission_ratio','');
            $conditions=I('conditions','');
            $iconurl=I("iconurl","");

            $map=array(
              'uid'=>UID,
            );
            $result=apiCall(StoreApi::GET_INFO,array($map));
            if(!$result['status']){

                $this->error("没有店铺不能添加等级");
            }else{
                if($result['info']==null){
                    $this->error("没有店铺不能添加等级");
                }
            }



            $entity=array(
                'name'=>$name,
                'remark'=>$remark,
                'uid'=>UID,
                'iconurl'=>$iconurl,
                'discount_ratio'=>$discount_ratio/100,
                'commission_ratio'=>$commission_ratio/100,
                'conditions'=>$conditions,
                'store'=>$result['info']['id'],
            );
            $result=apiCall(MgroupApi::ADD,array($entity));
            if($result['status']){
                //
                //$this->
                $this->success("添加成功!",U('Admin/MemberGroup/groupIndex'));
            }else {
                $this->error("添加失败!");
            }
        }
    }

    /**
     * 添加用户关联
     */
    public function addUidMgroup(){
            $uid=I("uid",0);
            if($uid==0){
                $this->error("请选择用户");
            }
            $groupid=I("groupid",0);
            $groupname=I("groupname","");
            $map=array(
                'uid'=>$uid,
                'creator_uid'=>UID,
            );
            $result=apiCall(UidMgroupApi::GET_INFO,array($map));
            if($result['info']!=null){
                $this->error("添加失败,您已经添加过该会员的等级了");
            }
            $entity=array(
                'uid'=>$uid,
                'groupid'=>$groupid,
                'creator_uid'=>UID,
            );
            $result=apiCall(UidMgroupApi::ADD,array($entity));
            if($result['status']){
                $this->success("添加成功!",U('Admin/MemberGroup/uidMgroupIndex',array('id'=>$groupid,'groupname'=>$groupname)));
            }else {
                $this->error("添加失败!");
            }
    }

    /**
     * 删除等级
     */
    public function deleteGroup(){
        $id=I('id','');
        $map=array(
            'id'=>$id,
        );
        $result=apiCall(MgroupApi::DELETE,array($map));
        if($result['status']){
            $this->success("删除成功!");
        }else {
            $this->error("删除失败!");
        }

    }

    /**
     * 删除用户关联
     */
    public function deleteUidMgroup(){
        $id=I('id','');
        $map=array(
            'id'=>$id,
        );
        $result=apiCall(UidMgroupApi::DELETE,array($map));
        if($result['status']){
            $this->success("删除成功!");
        }else {
            $this->error("删除失败!");
        }

    }



    /**
     *等级视图
     *
     */
    public function groupView(){
        $this->getGroup();
        $this->display();
    }

    /**
     *  修改等级
     */
    public function groupUpdate(){
        if(IS_GET){
            $this->getGroup();
            $this->display();
        }else{
            $id=I('id','');
            $name=I('name','');
            $remark=I('remark','');
            $discount_ratio=I('discount_ratio',0);
            $commission_ratio=I('commission_ratio',0);
            $conditions=I('conditions',0);
            $iconurl=I("iconurl","");
            $entity=array(
                'name'=>$name,
                'remark'=>$remark,
                'discount_ratio'=>$discount_ratio/100,
                'commission_ratio'=>$commission_ratio/100,
                'conditions'=>$conditions,
                'iconurl'=>$iconurl
            );

            $result=apiCall(MgroupApi::SAVE_BY_ID,array($id,$entity));

            if($result['status']){
                $this->success("更新成功!",U("Admin/MemberGroup/groupIndex"));
            }else {
                $this->error("更新失败!");
            }
        }
    }


    private function getGroup(){
        $id=I('id','');
        $map=array(
            'id'=>$id,
        );
        $result=apiCall(MgroupApi::GET_INFO,array($map));
        //dump($result);
        $this->assign('view',$result['info']);
    }


    public function index(){
        $params = array();
        $where['nickname'] = array('like', "%" . I('nickname', '', 'trim') . "%");
        $where['uid'] = I('nickname',-1);
        $where['_logic'] = 'OR';
        $map['_complex'] = $where;
        $map['add_uid'] =UID;
        $page = array('curpage' => I('get.p'), 'size' => C('LIST_ROW'));
        $order = " last_login_time desc ";
        $params['nickname'] = I('nickname','','trim');
        $result = apiCall("Admin/Member/query", array($map, $page, $order));
        if ($result['status']) {

            $this -> assign("show", $result['info']['show']);
            $this -> assign("list", $result['info']['list']);
            $this -> display();
        } else {
            $this -> error($result['info']);
        }
    }


    /**
     * add
     */
    public function add($username = '', $password = '', $repassword = '', $email = ''){
        if(IS_POST){

            if($password != $repassword){
                $this->error("密码和重复密码不一致！");
            }

            /* 调用注册接口注册用户 */
            $result = apiCall(UserApi::REGISTER, array($username, $password, $email));

            if($result['status']){ //注册成功
                $entity = array(
                    'uid'=>$result['info'],
                    'nickname'=>$username,
                    'realname'=>'',
                    'idnumber'=>'',
                    'add_uid'=>UID
                );
                $result = apiCall(MemberApi::ADD, array($entity));
                if(!$result['status']){
                    $this->error('用户添加失败！');
                } else {
                    $this->success('用户添加成功！',U('MemberGroup/index'));
                }
            } else { //注册失败，显示错误信息
                $this->error($result['info']);
            }

        }else{
            $this->display();
        }
    }


    /**
     * 删除用户
     * 假删除
     */
    public function delete(){
        if(is_administrator(I('uid',0))){
            $this->error("禁止对超级管理员进行删除操作！");
        }
        //parent::pretendDelete("uid");

        $id = I("uid", -1);
        $ids = I("uid" . 's', -1);
        if ($ids === -1 && $id === -1) {
            $this -> error("缺少参数！");
        }
        if ($ids === -1) {
            $map = array("uid" => $id);
        } else {
            $map = array();
            $ids = implode(',', $ids);
            $map = array("uid" => array('in', $ids));
        }

        $result = apiCall("Admin/Member/pretendDelete", array($map));

        if ($result['status']) {

            $this -> success("删除成功！", U('Admin/MemberGroup/index'));

        } else {
            $this -> error($result['info']);
        }
    }


    /**
     * 启用
     */
    public function enable(){
        parent::enable("uid");
    }


    /**
     * 禁用
     */
    public function disable(){
        if(is_administrator(I('uid',0))){
            $this->error("禁止对超级管理员进行禁用操作！");
        }
        parent::disable("uid");
    }



}