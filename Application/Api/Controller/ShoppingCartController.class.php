<?php
namespace Api\Controller;
use Shop\Api\ProductApi;
use Shop\Api\ProductSkuApi;
use Shop\Api\ShoppingCartApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/21
 * Time: 8:51
 */

class ShoppingCartController extends ApiController{


    private function toAdd($uid,$store_id,$p_id,$sku_id,$sku_desc,$icon_url,$count,$name,$express,$price,$ori_price,$psku_id,$weight,$taxrate){
        if($count<1){
            $this->apiReturnErr("商品数量不能小于1");
        }
        $map=array(
            'uid'=>$uid,
            'p_id'=>$p_id,
            'psku_id'=>$psku_id,
        );
        $result= apiCall(ShoppingCartApi::GET_INFO,array($map));
        $error=0;
        //addLog("ShoppingCart/add", $_GET, "ceshi", "测试A");
        if($result['status']){
            //先判断购物车里有没有，有则累加数量
            if($result['info']!=NULL){
                //addLog("ShoppingCart/add", $_GET, $map, "测试B");
                if($psku_id==0){
                    $map=array(
                        'id'=>$p_id
                    );
                    $result1=apiCall(ProductApi::GET_INFO,array($map));
                }else{
                    $map=array(
                        'id'=>$psku_id
                    );
                    $result1=apiCall(ProductSkuApi::GET_INFO,array($map));
                }
                if($result1['status']){
                    if($result1['info']==NULL){
                        $error++;
                        $this->apiReturnErr("数据异常,不存在该规格的商品");
                    }
                    //商品库存
                    $pcount=(int)$result1['info']['quantity'];

                    //需要的数量
                    $mbcount=(int)$result['info']['count']+(int)$count;

                    if($mbcount>$pcount){
                        $mbcount=$pcount;
                    }
                    $map=array(
                        'count'=>$mbcount
                    );
                    //$this->apiReturnSuc($mbcount);
                    addLog("ShoppingCart/add", $_GET, $map, $mbcount);
                    $result=apiCall(ShoppingCartApi::SAVE_BY_ID,array($result['info']['id'],$map));

                    if($result['status']){

                    }else{
                        $error++;
                        $this->apiReturnErr("添加失败");
                    }

                }else{
                    $error++;
                    $this->apiReturnErr("添加失败");
                }
            }else{
                //addLog("ShoppingCart/add", $_GET, "ceshi", "测试C");
                $entity=array(
                    "uid"=>$uid,
                    "store_id"=>$store_id,
                    "p_id"=>$p_id,
                    "sku_id"=>$sku_id,
                    "sku_desc"=>$sku_desc,
                    "icon_url"=>$icon_url,
                    "count"=>$count,
                    "name"=>$name,
                    'express'=>$express,
                    'price'=>$price,
                    'ori_price'=>$ori_price,
                    'psku_id'=>$psku_id,
                    'weight'=>$weight,
                    'taxRate'=>$taxrate,
                );
               // addLog("ShoppingCart/add", $_GET, $entity, "测试B");
                $result=apiCall(ShoppingCartApi::ADD,array($entity));
                if($result['status']){
                    //$this->apiReturnSuc($result['info']);
                }else{
                    $error++;
                    $this->apiReturnErr("添加失败");
                }
            }
        }else{
            //addLog("ShoppingCart/add", $_GET, $_POST, "异常啦A");
            $error++;
            $this->apiReturnErr("发生异常，请稍候再试");
        }
        return $error;

    }



    /**
     * 添加到购物车
     * 如果购物车里已有，则修改数量
     */
    public function add(){
        $notes = "应用" . $this->client_id . "，调用购物车添加接口";
        addLog("ShoppingCart/add", $_GET, $_POST, $notes);
        $uid= $this->_post("uid");
        $store_id= $this->_post("store_id");
        $p_id= $this->_post("p_id");
        $sku_id= $this->_post("sku_id","");
        $sku_desc= $this->_post("sku_desc","");
        $icon_url= $this->_post("icon_url",0);
        $count= $this->_post("count",1);
        $name= $this->_post("name","");
        $express=$this->_post("express",0);
        $price=$this->_post("price",0);
        $ori_price=$this->_post("ori_price",0);
        $psku_id=$this->_post("psku_id",0);
        $weight=$this->_post('weight',0);
        $taxrate=$this->_post('taxrate',0);

        if(empty($uid)||empty($store_id)||empty($p_id)){
            $this->apiReturnErr("缺少参数"."uid:".$uid."store_id:".$store_id."pid:".$p_id);
            renturn;
        }

        $error=$this->toAdd($uid,$store_id,$p_id,$sku_id,$sku_desc,$icon_url,$count,$name,$express,$price,$ori_price,$psku_id,$weight,$taxrate);
        if($error==0){
            $this->apiReturnSuc("添加成功");
        }
    }

    /**
     * 删除购物车
     *
     */
    public function delete(){
        $notes = "应用" . $this->client_id . "，调用购物车删除接口";
        addLog("ShoppingCart/delete", $_GET, $_POST, $notes);
        $id=I('id',0);//获取购物车ID
        $map=array(
            'id'=>$id,
        );
        $result= apiCall(ShoppingCartApi::DELETE,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("删除失败");
        }
    }


    /**
     * 更新购物车
     *
     */
    public function update(){
        $notes = "应用" . $this->client_id . "，调用购物车修改接口";
        addLog("ShoppingCart/update", $_GET, $_POST, $notes);
        $id=$this->_post("id",0);//获取购物车ID
        $count= $this->_post("count",0); //数量
        $express=$this->_post("express",0);//运费
        $psku_id=$this->_post("psku_id",0); //规格id
        $p_id=$this->_post("p_id",0);
        if($count==0){
            $this->apiReturnErr("数量必须大于1");
        }

        //如果没规格，判断为商品的库存,如果有，按规格库存来
        if( $psku_id==0){
            $map=array(
                'id'=>$p_id
            );
            $result=apiCall(ProductApi::GET_INFO,array($map));
        }else{
            $map=array(
                'id'=>$psku_id
            );
            $result=apiCall(ProductSkuApi::GET_INFO,array($map));
        }
        if($result['status']){
            if($result['info']['quantity']<$count){
                $this->apiReturnErr("超出商品库存");
            }
        }else{
            $this->apiReturnErr("发生异常，稍候再试");
        }
        $map=array(
            'count'=>$count,
            'express'=>$express,
        );
        $result=apiCall(ShoppingCartApi::SAVE_BY_ID,array($id,$map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("修改失败");
        }
    }

    /**
     * 查询购物车
     */
    public function query(){
        $notes = "应用" . $this->client_id . "，调用购物车分页查询接口";
        addLog("ShoppingCart/query", $_GET, $_POST, $notes);
        //获取用户ID
        $uid=I('uid',0);
        $map=array(
          'uid'=>  $uid
        );
        $page = array('curpage'=>I('pageNo',0),'size'=>I('pageSize',10)); //分页查询
        $order="store_id desc,update_time desc";
        $result=apiCall(ShoppingCartApi::QUERY,array($map,$page,$order));
        if($result['status']){
            $this->apiReturnSuc($result['info']['list']);
        }else{
            $this->apiReturnErr("您的购物车暂时为空");
        }
    }


    /**
     * 查询单个购物车
     */
    public function getInfo(){
        $notes = "应用" . $this->client_id . "，调用单个购物车查询接口";
        addLog("ShoppingCart/getInfo", $_GET, $_POST, $notes);
        //获取用户ID
        $id=I('id',0);
        $map=array(
            'id'=>  $id
        );
        $result=apiCall(ShoppingCartApi::GET_INFO,array($map));
        if($result['status']){
            if($result['info']!=null){
                $this->apiReturnSuc($result['info']);
            }else{
                $this->apiReturnErr("查询失败");
            }

        }else{
            $this->apiReturnErr("查询失败");
        }
    }





    /**
     * 购物车对应商品库存
     * 如果购物车里已有，则修改数量
     */
    public function count(){
        $notes = "应用" . $this->client_id . "，调用购物车对应商品库存接口";
        addLog("ShoppingCart/count", $_GET, $_POST, $notes);
        $p_id= $this->_post("p_id",0);
        $psku_id=$this->_post("psku_id",0);

        if(empty($p_id)){
            $this->apiReturnErr("缺少参数p_id=".$p_id.";psku_id=".$psku_id);
            renturn;
        }


       if($psku_id==0){
          $map=array(
              'id'=>$p_id
          );
          $result=apiCall(ProductApi::GET_INFO,array($map));
       }else{
           $map=array(
               'id'=>$psku_id
            );
           $result=apiCall(ProductSkuApi::GET_INFO,array($map));
        }
        if($result['status']){
            $pcount=(int)$result['info']['quantity'];
            $this->apiReturnSuc($pcount);
        }else{
            $this->apiReturnErr("发生异常，请稍候再试");
        }
    }




    /**
     * 添加到购物车
     * 如果购物车里已有，则修改数量
     */
    public function addArray(){
        $notes = "应用" . $this->client_id . "，调用购物车数组添加接口";
        addLog("ShoppingCart/addArray", $_GET, $_POST, $notes);
        $uid= $this->_post("uid");
        $store_id= $this->_post("store_id");
        $p_id= $this->_post("p_id");
        $sku_id= $this->_post("sku_id",-1);
        $sku_desc= $this->_post("sku_desc",-1);
        $icon_url= $this->_post("icon_url",-1);
        $count= $this->_post("count",-1);
        $name= $this->_post("name",-1);
        $express=$this->_post("express",-1);
        $price=$this->_post("price",-1);
        $ori_price=$this->_post("ori_price",-1);
        $psku_id=$this->_post("psku_id",-1);
        $weight=$this->_post("weight",-1);
        $taxrate=$this->_post("taxrate",-1);
        if(empty($uid)){
            $this->apiReturnErr("缺少参数uid");
            renturn;
        }

        $this->isError($sku_id,"sku_id");
        $this->isError($sku_desc,"sku_desc");
        $this->isError($icon_url,"icon_url");
        $this->isError($count,"count");
        $this->isError($name,"name");
        $this->isError($express,"express");
        $this->isError($price,"price");
        $this->isError($ori_price,"ori_price");
        $this->isError($psku_id,"psku_id");
        $this->isError($weight,"weight");
        $this->isError($taxrate,"taxrate");
        $counts="";
        foreach($count as $c){
            $counts.=$c.";";
        }
        $error=0;
        for($i=0;$i<count($store_id);$i++) {
            if (empty($store_id[$i]) || empty($p_id[$i])) {
                $this->apiReturnErr("有缺少的参数store_id:" . $store_id . "pid:" . $p_id);
                renturn;
            }
            $this->toAdd($uid,$store_id[$i], $p_id[$i], $sku_id[$i], $sku_desc[$i], $icon_url[$i], $count[$i], $name[$i], $express[$i], $price[$i], $ori_price[$i], $psku_id[$i],$weight[$i],$taxrate[$i]);
        }
        if($error==0){
            $this->apiReturnSuc("添加成功".$counts);
        }
    }





    function isError($info,$name){
        if($info==-1){
            $this->apiReturnErr("缺少参数".$name);
        }
    }

    function getSupportMethod(){

    }
}