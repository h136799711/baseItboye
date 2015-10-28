<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Common\Model\AddressModel;

class AddressApi extends Api{
    /**
     * 添加
     */
    const ADD = "Shop/Address/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Address/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Address/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Address/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Address/query";

    const QUERY_NO_PAGING="Shop/Address/queryNoPaging";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Address/getInfo";

    const QUERY_WITH_CITY_WITH_AREA = "Shop/Address/queryWithCityWithArea";

    const SET_DEFAULT_ADDRESS="Shop/Address/setDefaultAddress";


	protected function _init(){
		$this->model = new AddressModel();
	}


    /**
     * 查询地址
     * @param $map
     * @return array
     */
    public function queryWithCityWithArea($map,$order){
        $query = $this->model;
        if(!is_null($map)){
            $query = $query->where($map);
        }
        if(!is_null($order)){
            $query = $query->order($order);
        }

        $list = $query->alias('a')->join('left join common_city b ON a.city = b.cityID')->join('left join common_area c ON a.area=c.areaID')->join('left join common_province d ON a.province=d.provinceID')->field('a.id,a.uid,a.country,b.cityid,b.city,d.province,d.provinceid,a.detailinfo,c.areaid,c.area,a.contactname,a.mobile,a.wxno,a.postal_code,a.default_address') -> select();


        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }

        return $this -> apiReturnSuc($list);

    }

    /**
     * 设置默认地址
     */
    public function setDefaultAddress($map){
        $map1=array(
            'uid'=>$map['uid'],
            'default_address'=>1
        );
        $entity1=array(
            'default_address'=>0
        );

        $trans = M();
        $trans->startTrans();
        $result=$this->save($map1,$entity1);
        if($result['status']){
            $entity2=array(
                'default_address'=>1
            );
            $result=$this->saveByID($map['id'],$entity2);
            if($result['status']){
                $trans->commit();
                return $this->apiReturnSuc("设置默认地址成功");
            }else{
                $trans->rollback();
            }

        }else{
            $trans->rollback();
        }
        return $this -> apiReturnErr("设置默认地址失败");
    }
	
}

