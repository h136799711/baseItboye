<?php
namespace Api\Controller;


use Shop\Api\AddressApi;
use Shop\Api\MemberConfigApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/11
 * Time: 11:14
 */
class AddressController extends ApiController{
    function getSupportMethod(){

    }

    /**
     * 添加收货地址
     */
    public function add(){
        $notes = "应用".$this->client_id."，调用Address添加接口";
        addLog("Address/add",$_GET,$_POST,$notes);

        $uid=$this->_post("uid",0,"UID不能为空");
        $map = $this->getAddressModel();
        $map['uid'] =$uid;

        $result= apiCall(AddressApi::ADD,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("添加失败");
        }
    }


    /**
     * 查看收货地址
     */
    public function queryNoPaging(){
        $notes = "应用".$this->client_id."，调用Address查询接口";
        addLog("Address/add",$_GET,$_POST,$notes);

        $map=array(
          'uid'=>$this->_post('uid',0,"用户ID获取失败"),
        );

        $result=apiCall(AddressApi::QUERY_NO_PAGING,array($map," id desc "));
        if($result['status']){
            if(empty($result['info'])){
                $result['info'] = array();
            }
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("查询失败");
        }

    }

    /**
     * 查看收货地址
     */
    public function queryWithCityWithArea(){
        $notes = "应用".$this->client_id."，调用Address查询接口";
        addLog("Address/queryWithCityWithArea",$_GET,$_POST,$notes);

        $map=array(
            'uid'=>$this->_post('uid',0,"用户ID不能为空"),
        );

        $order="default_address desc";
        $result=apiCall(AddressApi::QUERY_WITH_CITY_WITH_AREA,array($map,$order));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("查询失败");
        }

    }

    //设置默认地址的接口
    public function setDefaultAddress(){
        $notes = "应用".$this->client_id."，调用Address设置默认地址接口";
        addLog("Address/setDefaultAddress",$_GET,$_POST,$notes);
        $uid=$this->_post('uid',0,"用户ID不能为空");
        $map=array(
            'uid'=>$uid,
            'default_address'=>$this->_post('id','',"地址ID不能为空"),
        );

        //apiCall(MemberConfigApi::SAVE_BY_ID)

        $result=apiCall(MemberConfigApi::SAVE_BY_ID,array($uid,$map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("设置失败");
        }
    }


    //删除地址
    public function delete(){
        $notes = "应用".$this->client_id."，调用Address删除接口";
        addLog("Address/delete",$_GET,$_POST,$notes);

        $map=array(
            'id'=>$this->_post('id',0,'缺失地址ID'),
        );

        $result=apiCall(AddressApi::DELETE,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("删除失败");
        }
    }




    /**
     * 添加收货地址
     */
    public function update(){
        $notes = "应用".$this->client_id."，调用Address修改接口";
        addLog("Address/update",$_GET,$_POST,$notes);

        $id=$this->_post("id",0,"地址参数ID不能为空");
        $map = $this->getAddressModel();
        $map['id'] = $id;
        $result= apiCall(AddressApi::SAVE_BY_ID,array($id,$map));

        addLog("Address/update",$map,$result,$notes.",调用结果");
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("修改失败");
        }
    }

    /**
     * 获取默认地址的接口
     */
    public function getInfo(){

        $notes = "应用".$this->client_id."，调用Address设置默认地址接口";
        addLog("Address/getInfo",$_GET,$_POST,$notes);
        $id=$this->_post('id',0,"地址ID不能为空");
        $uid=$this->_post('uid',0,"用户ID不能为空");
        $map=array(
            'id'=>$id,
            'uid'=>$uid
        );

        $result=apiCall(AddressApi::GET_INFO,array($map));
        if(!$result['status']){
            $this->apiReturnErr($result['info']);
        }else{
            $this->apiReturnSuc($result['info']);
        }
    }

    //--private

    private function getAddressModel(){

        $province=$this->_post('province','',"省份不能为空");
        $city=$this->_post('city','',"城市不能为空");
        $area=$this->_post('area','',"详细地址不能为空");
        $provinceid=$this->_post('provinceid','',"省份编码不能为空");
        $cityid=$this->_post('cityid','',"城市编码不能为空");
        $areaid=$this->_post('areaid','',"地区编码不能为空");
        $detailinfo=$this->_post('detailinfo','');
        $contactname=$this->_post('contactname','',"联系人不能为空");
        $mobile=$this->_post('mobile','',"联系电话不能为空");
        $postal_code=$this->_post('postal_code','',"邮政编码不能为空");
        $country = $this->_post("country",1701);
        $wxno = $this->_post('wxno','');

        $map=array(
            'country'=>$country,
            'city'=>$city,
            'province'=>$province,
            'area'=>$area,
            'detailinfo'=>$detailinfo,
            'contactname'=>$contactname,
            'mobile'=>$mobile,
            'postal_code'=>$postal_code,
            'wxno'=>$wxno,
            'cityid'=>$cityid,
            'provinceid'=>$provinceid,
            'areaid'=>$areaid,
        );

        return $map;
    }


}