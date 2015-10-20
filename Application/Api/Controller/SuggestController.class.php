<?php
namespace APi\Controller;

use Shop\Api\SuggestApi;
use Think\Controller\RestController;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/14
 * Time: 10:20
 */
class SuggestController extends RestController{
    public function index(){
        // $access_token = I("get.access_token");
        C('SHOW_PAGE_TRACE', false);//���ò���ʾtrace
        // $this->assign('access_token',$access_token);
        $this->display();
    }

    /**
     * ���鷴��
     */
    public function feedback(){
        $map=array(
            'name'=>I("name"),
            'text'=>I("text"),
        );
        $result=apiCall(SuggestApi::ADD,array($map));
        if($result['status']){
            $this->apiReturnSuc("����ύ�ɹ���");
        }else{
            $this->apiReturnErr("����ύʧ�ܣ�");
        }
    }



    /**
     * ajax����
     * @param $data
     * @internal param $i
     */
    protected function apiReturnSuc($data){
        $this->ajaxReturn(array('code'=>0,'data'=>$data),"json");
    }

    /**
     * ajax���أ����Զ�д��token����
     * @param $data
     * @param int $code
     * @internal param $i
     */
    protected function apiReturnErr($data,$code=-1){
        $this->ajaxReturn(array('code'=>$code,'data'=>$data),"json");
    }
}