<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 18:42
 */
namespace Admin\Controller;

use OAuth2Manage\Api\ClientsApi;
use Think\Controller;

class ClientsController extends AdminController{



    public function index(){

        $map = array();
        $param = array();

        $page = array('curpage'=>I('get.p',0),'size'=>10);
        $order= " client_id desc ";
        $fields = " create_time ,update_time,user_id,client_id,client_secret,client_name,redirect_uri,grant_types,scope ";
        $result = apiCall(ClientsApi::QUERY,array($map,$page,$order,$fields,$param));

        ifFailedLogRecord($result,__FILE__.__LINE__);

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("list",$result['info']['list']);
        $this->assign("show",$result['info']['show']);
        $this->display();
    }

    public function add(){
        if(IS_GET){
            $this->display();
        }else{
            $form = I('post.form',array());
            $grant_types = I('post.grant_types','');
//            dump($grant_types);
            $entity = array_merge([],$form);

            import("Org.String");

//            $client_id = \String::randString(9,0);
            $entity['grant_types'] = implode(",",$grant_types);
            $entity['user_id'] = UID;
            $entity['client_id'] = "by".uniqid().UID;
            $entity['client_secret'] =  md5(uniqid());
//            dump($entity);
            $result = apiCall(ClientsApi::ADD,array($entity));

            if(!$result['status']){
                $this->error($result['info']);
            }

            $this->success("添加成功!",U('Admin/Clients/index'));

        }
    }


    public function delete(){

        $client_id = I('get.client_id',0);

        $result = apiCall(ClientsApi::DELETE,array(array('client_id'=>$client_id)));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->success("删除成功!",U('Admin/Clients/index'));


    }


}
