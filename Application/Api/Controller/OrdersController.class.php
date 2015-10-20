<?php
namespace Api\Controller;

use Shop\Api\OrdersApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\ProductApi;
use Shop\Api\ShoppingCartApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/5
 * Time: 16:45
 */

class OrdersController extends ApiController{
    /**
     * 添加订单
     */
    public function add(){



        $notes = "应用".$this->client_id.",调用添加订单接口";
        addLog("Orders/add",$_GET,$_POST,$notes);
        $uid=$this->_post("uid",0);
        if($uid==0){
            $this->apiReturnErr("缺少用户ID");
        }
        $orderid = getOrderid($uid);
        $this->apiReturnSuc($orderid);



        //购物车ID,优惠码，留言，收货地址，from
        // $prices=$this->_post("prices",0); //总价
        $post_prices=$this->_post("post_prices",0); //运费
        $note=$this->_post("note",""); //备注
        //$storeid=$this->_post("storeid",1);//店铺ID
        $cartids=$this->_post("cartids",-1);
        $cartidsArray=explode(",",$cartids);
        foreach($cartidsArray as $ca){
            $map=array(
                'id'=>$ca,
            );
            $result=apiCall(ShoppingCartApi::GET_INFO,array($map));
            if($result['status']){
                //TODO:继续写
            }
        }


        $contactname=$this->_post("contactname","");//联系人姓名
        $mobile=$this->_post("mobile","");//联系人电话
        $country=$this->_post("country",0);//国家
        $province=$this->_post("province",0);//省份
        $city=$this->_post("city",0);//市
        $area=$this->_post("area",0);//区域
        $wxno=$this->_post("wxno","");//微信号
        $detailinfo=$this->_post("detailinfo","");//详细
        //TODO:items,商品集合怎么传过来？可以考虑换种思维,
        //item包含：has_sku,name,img,price,ori_price,post_price,sku_id,sku_desc,count,order_code,createtime,p_id,pay_status

        $name=$this->_post("name");
        $has_sku=$this->_post('has_sku' );
        $img=$this->_post("img");
        $price=$this->_post('price');
        $post_price=$this->_post("post_price");
        $ori_price=$this->_post("ori_price");
        $count=$this->_post('count');
        $p_id=$this->_post("p_id");
        $sku_id=$this->_post('sku_id');
        $sku_desc=$this->_post('sku_desc');

        //dump($has_sku);
        for($i=0;$i<count($name);$i++){
            $products[$i]['name']=$name[$i];
            $products[$i]['has_sku']=$has_sku[$i];
            $products[$i]['main_img']=$img[$i];
            $products[$i]['price']=$price[$i];
            $products[$i]['post_price']=$post_price[$i];
            $products[$i]['ori_price']=$ori_price[$i];
            $products[$i]['count']=$count[$i];
            $products[$i]['id']=$p_id[$i];
            $products[$i]['sku_id']=$sku_id[$i];
            $products[$i]['sku_desc']=$sku_desc[$i];
        }
        //dump($products);
        /*$ids_counts=$this->_post("ids_counts","");//商品ID和数量字符串,以冒号和英文逗号隔开 例1:3;2:4;
        $ids_countsArray=explode(';',$ids_counts);
        for($i=0;$i<count($ids_countsArray);$i++){
            $id_count=explode(':',$ids_countsArray);
            $map=array(
              'id'=>$id_count[0],
            );
            $products[]=apiCall(ProductApi::GET_INFO,array($map));
            $products[]['count']=$id_count[1];
        }*/
        $entity=array(
            'uid'=>$uid,
            'price'=>$prices,
            'order_code'=>getOrderid($uid),
            'post_price'=>$post_prices,
            'note'=>$note,
            'storeid'=>$storeid,
            'contactname'=>$contactname,
            'mobile'=>$mobile,
            'country'=>$country,
            'province'=>$province,
            'city'=>$city,
            'area'=>$area,
            'wxno'=>$wxno,
            'detailinfo'=>$detailinfo,
            'items'=>$products
        );
        $result=apiCall(OrdersApi::ADD_ORDER,array($entity));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("订单失败,未知的错误");
        }
    }



    /**
     * 获取订单编号
     */
    function getOrderid($uid){



        list($usec, $sec) = explode(" ", microtime());
        $usec = str_pad(floor($usec*1000000), 6,STR_PAD_RIGHT);

        srand($usec);

        $rand = rand(100, 999);

        $orderID = date("yzHis",$sec).$usec;

        return $orderID.$rand.get_36HEX($uid);

    }


    /**
     * 查看订单列表
     */
    public function query(){
        $notes = "应用".$this->client_id.",调用查询订单接口";
        addLog("Orders/query",$_GET,$_POST,$notes);
        //首先接收用户ID，根据用户ID去查询他的所有订单，当然要分页
        $uid=I("uid",0);
        if($uid==0){
            $this->apiReturnErr("缺少用户ID");
        }
        $map=array(
            'uid'=>$uid,
        );
        $page = array('curpage'=>I('pageNo',0),'size'=>I('pageSize',10)); //分页
        $result=apiCall(OrdersInfoViewApi::QUERY,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']['list']);
        }else{
            $this->apiReturnErr("您还没有订单，快去购物吧");
        }
    }

    /**
     * 查看订单详情
     */
    public function detail(){
        $notes = "应用".$this->client_id.",调用查询订单详情接口";
        addLog("Orders/detail",$_GET,$_POST,$notes);
        $id=I('id',0); //订单ID
        if($id==0){
            $this->apiReturnErr("缺少订单ID");
        }
        $map=array(
            'id'=>$id,
        );
        $result=apiCall(OrdersInfoViewApi::GET_INFO,array($map));
        if($result['status']){
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("查询中发生错误，请联系相关人员");
        }
    }


    public function order(){
        header('Content-Type:text/html; charset=utf-8');
        $this->display();
        //echo '<b>123456</b>';
        exit();
    }



    function getSupportMethod(){

    }
}

