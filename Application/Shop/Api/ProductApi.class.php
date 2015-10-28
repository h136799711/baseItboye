<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Api;
use \Common\Api\Api;
use Shop\Model\ProductAttrModel;
use \Shop\Model\ProductModel;
use Think\Page;

class ProductApi extends Api{

    /**
     * 查询商品信息根据用户ID
     */
    const QUERY_WITH_STORE_UID = "Shop/Product/queryWithStoreUID";
    /**
     * 查询商品信息根据分组
     */
    const QUERY_BY_GROUP = "Shop/Product/queryByGroup";
    /**
     * 查询商品信息并包含店铺信息
     */
    const QUERY_WITH_STORE = "Shop/Product/queryWithStore";

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Product/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/Product/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Product/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Product/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Product/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Product/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Product/getInfo";

	const COUNT="Shop/Product/count";
	
	//减少库存
	const SET_DEC="Shop/Product/setDec";

    const QUERY_WITH_COUNT="Shop/Product/queryWithCount";


    protected function _init(){
		$this->model = new ProductModel();
	}

    /**
     *
     * 商品添加信息
     * @param $entity
     * @return bool|void
     */
    public function add($entity){

        $attr_entity = array(
            'post_free' => $entity['post_free'],
            'has_receipt' => $entity['has_receipt'],
            'under_guaranty' => $entity['under_guaranty'],
            'support_replace' => $entity['support_replace'],
        );

        unset($entity['post_free']);
        unset($entity['has_receipt']);
        unset($entity['under_guaranty']);
        unset($entity['support_replace']);
        $this->model->startTrans();
        $error = false;
        $attrModel = new ProductAttrModel();
        $result = $this -> model -> create($entity, 1);
        $pid = 0;
        //1. 插入到商品表
        if ($result === false) {
            $error = $this -> model -> getError();
        } else {
            $result = $this -> model -> add();
            if($result === false){
                $error = $this->model->getDbError();
            }else{
                $pid = $result;
            }
        }

        if($error === false){
            //2. 前面没出错则插入到商品属性表
            if($attrModel->create($attr_entity,1) === false){
                $error = $attrModel->getError();
            }else{
                $result = $attrModel->add();
                if($result === false){
                    $error = $this->model->getDbError();
                }
            }
        }


        if($error === false){

            $this->model->commit();
            $this->apiReturnSuc($pid);
        }else{

            $this->model->rollback();
            $this->apiReturnErr($error);
        }

    }

    /**
     *
     * @param int $uid
     * @param null $map
     * @param array $page
     * @param bool $order
     * @param bool $params
     * @param bool $fields
     * @return array
     */
	public function queryWithStoreUID($uid=0,$map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false){
		$query = $this->model;
		
		$result = $query->query("select * from __STORE__ where uid = $uid");
		
		if($result === false){
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}
		$storeidlist = array('0');
		foreach($result as $store){
			array_push($storeidlist,($store['id']));
		}
		
		$_map['storeid'] = array('in',$storeidlist);
		
		$map = array_merge($_map,$map);
		
		if(!is_null($map)){
			$query = $query->where($map);
		}
		
		if(!($order === false)){
			$query = $query->order($order);
		}
		if(!($fields === false)){
			$query = $query->field($fields);
		}

		$list = $query -> page($page['curpage'] . ',' . $page['size'])  -> select();
		

		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		$count = $this -> model -> where($map) -> count();
		// 查询满足要求的总记录数
		$Page = new Page($count, $page['size']);

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
     * @param $name
     * @param $type
     * @param $page
     * @param $order
     * @param $params
     * @return array
     */
    public function queryWithStore($name,$type,$page,$order,$params){


        $query = $this->model;//->field("")->alias(" pd ")->join("LEFT JOIN __WXSTORE__ as st on st.id = pd.storeid ");

        $sql = "select  pd.name as name ,pd.id,pd.main_img,pd.buy_limit,pd.attrext_ispostfree,pd.attrext_ishasreceipt,pd.attrext_issupportreplace,pd.loc_country,pd.loc_province,pd.loc_city,pd.loc_address,pd.has_sku,pd.ori_price,pd.price,pd.quantity,pd.product_code,pd.cate_id,
		pd.createtime,pd.updatetime,pd.onshelf,pd.status,pd.storeid,pd.properties,pd.sku_info,pd.detail,st.uid,st.name as storename,st.desc,st.isopen,st.logo,st.banner,st.wxno,st.exp";
        $sql .= " from __PRODUCT__ as pd LEFT JOIN __STORE__ as st on st.id = pd.storeid  ";
        if($type == '1'){
            $whereName = " pd.name ";
        }else{
            $whereName = "st.name";
        }
        $sql .= " where pd.onshelf = ".ProductModel::STATUS_ONSHELF;
        if(!empty($name)){
            $sql .= " and  $whereName like '%".$name."%' ";
        }
        $sql .= ' order by '. $order;
//		if(!($order === false)){
//			$query = $query->order($order);
//		}
        $sql .= " LIMIT ".(intval($page['curpage'])*$page['size']) . ',' . $page['size'];

        $list = $query->query($sql);


        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }
        $countSql = " select count(*) as cnt from __PRODUCT__ as pd LEFT JOIN __STORE__ as st on st.id = pd.storeid   ";
        if(!empty($name)){
            $countSql .= " where $whereName like '%".$name."%'";
        }

        $count = $query->query($countSql);
        $count = $count[0]['cnt'];
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        // 分页跳转的时候保证查询条件
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
     * @param $group_id
     * @param $map
     * @param $page
     * @return array
     */
    public function queryByGroup($group_id,$map,$page){
        $result = $this->model->query("select g_id,p_id from __PRODUCT_GROUP__ where `g_id` = ".$group_id);
        if($result === FALSE){
            return $this->apiReturnErr($this->model->getDbError());
        }
        $product_ids = array(-1);

        foreach($result as $vo){
            array_push($product_ids,$vo['p_id']);
        }

        if(is_null($map)){
            $map = array();
        }

        $map['id'] = array('in',$product_ids);


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
        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();


        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this -> model -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        // 分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));

    }

}

