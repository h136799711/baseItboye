<?php
namespace Admin\Api;

use Admin\Model\UidMgroupModel;
use Common\Api\Api;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/9/15
 * Time: 15:05
 */
class UidMgroupApi extends Api{
    const QUERY="Admin/UidMgroup/query";

    const ADD="Admin/UidMgroup/add";

    const DELETE="Admin/UidMgroup/delete";

    const SAVE="Admin/UidMgroup/save";

    const SAVE_BY_ID="Admin/UidMgroup/saveById";

    const QUERY_VIEW="Admin/UidMgroup/queryView";

    const GET_INFO="Admin/UidMgroup/getInfo";

    const QUERY_WITH_UID="Admin/UidMgroup/queryWithUid";

    protected function _init(){
        $this->model=new UidMgroupModel();
    }

    /**
     *
     */
    public function queryView($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false){
        $query = $this->model;
        if(!is_null($map)){
            $query = $query->where($map);
        }
        if(!($order === false)){
            $query = $query->order($order);
        }
        if(!($fields === false)){
            $query = $query->field($fields);
        }
        $list = $query -> page($page['curpage'] . ',' . $page['size']) ->alias('a')->join('common_member b on a.uid=b.uid')->join('itboye_mgroup c on a.groupid=c.id')->field('a.id id,b.nickname nickname,c.name,c.discount_ratio,c.commission_ratio,a.createtime')-> select();


        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this -> model -> where($map) -> count();
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

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }


    /**
     *
     */
    public function queryWithUid($uid=0,$storeuid=false){
        $query = $this->model;
        $where="a.uid=".$uid;
        if($storeuid===false){

        }else{
            $where.=' and c.uid='.$storeuid;
        }
        $result = $this->model -> alias('a')->join('__MGROUP__ AS c on a.groupid=c.id')->where($where)->field('a.id as id,a.uid as  uid,c.name,c.discount_ratio,c.commission_ratio,a.createtime,c.uid storeuid,c.remark')-> select();
        if ($result === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        } else {
            return $this -> apiReturnSuc($result);
        }
    }

}