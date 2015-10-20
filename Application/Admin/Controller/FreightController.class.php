<?php
namespace Admin\Controller;

use Admin\Api\FreightAddressApi;
use Think\Controller;
use Admin\Api\FreightTemplateApi;
use Tool\Api\CityApi;
use Tool\Api\ProvinceApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/13
 * Time: 15:37
 */

 class FreightController extends AdminController{


     /**
      * 测试查询
      */
     public function index(){
        // dump("????");
         $map=array(
             'name'=>array('like','%'.I('name').'%'),
             'uid'=>UID,
         );

         $result=apiCall(FreightTemplateApi::QUERY_WITH_COMPANY,array($map));

        //dump($result);

       $this -> assign('show', $result['info']['show']);
         $this -> assign('list', $result['info']['list']);

         $this->display();

         /* $map=array(
           'id'=>1
       );
        $list=apiCall(FreightTemplateApi::QUERY_WITH_ADDRESS,array($map));
        dump($list);*/
     }

     /**
      * 添加
      */
     public function add(){
         if(IS_GET){
            //查出所有省份
             $result=apiCall(ProvinceApi::QUERY_NO_PAGING);

             for($i=0;$i<count($result['info']);$i++){
                 $father=$result['info'][$i]["provinceid"];
                 $map=array(
                    'father'=> $father
                 );
                 $citys=apiCall(CityApi::QUERY_NO_PAGING,array($map));
                 $result['info'][$i]["citys"]=$citys['info'];

             }
             //dump($result);
            $this->assign("provinceList",$result['info']);

             $this->display();
         }else{
             $name=I('name');
             $company=I("company");
             $cityInfos=I("cityInfos");
             $type=I("type");
             $entity=array(
                 'name'=>$name,
                 'company'=> $company,
                 'type'=>$type,
                 'uid'=>UID,
                 'cityInfos'=>$this->getCityArray($cityInfos)
             );
            //dump($entity);
             $result=apiCall(FreightTemplateApi::ADD_WITH_ADDRESS,array($entity));
             //dump($result);
             if($result['status']){
                $this->success("模板添加成功！",U('Admin/Freight/index'));
             }else{
                 $this->error("模板添加失败！");
             }

         }
     }


     /**
      * 修改运费
      */
     public function edit(){
         if(IS_GET){
             $id=I("id",0);
             $map=array(
                 'id'=>$id,
             );
             $result=apiCall(FreightTemplateApi::QUERY_NO_PAGING,array($map));
             $this->assign('freight_template',$result['info'][0]);
             $map=array(
                 'template_id'=>$id
             );
             $result=apiCall(FreightAddressApi::QUERY_NO_PAGING,array($map));
             $this->assign('freight_address',$result['info']);

             $result=apiCall(ProvinceApi::QUERY_NO_PAGING);

             for($i=0;$i<count($result['info']);$i++){
                 $father=$result['info'][$i]["provinceid"];
                 $map=array(
                     'father'=> $father
                 );
                 $citys=apiCall(CityApi::QUERY_NO_PAGING,array($map));
                 $result['info'][$i]["citys"]=$citys['info'];

             }
             //dump($result);
             $this->assign("provinceList",$result['info']);

             $this->display();

         }else{
             $id=I("id",0);
             $name=I('name');
             $company=I("company");
             $type=I("type");
             $cityInfos=I("cityInfos");
             $entity=array(
                 'id'=>$id,
                 'name'=>$name,
                 'company'=> $company,
                 'type'=>$type,
                 'uid'=>UID,
                 'cityInfos'=>$this->getCityArray($cityInfos)
             );
             $result=apiCall(FreightTemplateApi::EDIT_WITH_ADDRESS,array($entity));
             if($result['status']){
                 $this->success("模板修改成功！",U('Admin/Freight/index'));
             }else{
                 $this->error("模板修改失败！");
             }

             // dump($id);
         }
     }

     /**
      * 切割字符串
      * @param $cityInfos
      * @return array
      */
     private function getCityArray($cityInfos){
         $cs=explode(";",$cityInfos);

         foreach($cs as $c){
             if($c!=""){
                 $info=explode(":",$c);
                 $array[]=array(
                     'addressids'=>$info[0],
                     'addresses'=>$info[1],
                     'firstpiece'=>$info[2],
                     'firstmoney'=>$info[3],
                     'replenishpiece'=>$info[4],
                     'replenishmoney'=>$info[5]
                 );
             }

         }
         return $array;
     }



     //根据省份ID查询城市
     public function toShowCity(){
         $provinceID=I('provinceID');
         //dump($provinceID);
         $map=array(
             'father'=>$provinceID
         );
         $result=apiCall(CityApi::QUERY_NO_PAGING,array($map));
         //echo json_encode($result['info']);
        $this->success($result['info']);
     }

     /**
      * 删除
      */
     public function delete(){
         $id=I("id",0);
         //根据模板ID删除两张表的数据，用事务关联
         $map=array(
           'id'=>  $id
         );
         $result=apiCall(FreightTemplateApi::DELETE_WITH_ADDRESS,array($map));
         if($result){
             $this->success("Delete Succeed",U("Admin/Freight/index"));
         }else{
             $this->error("删除失败");
         }
     }

     /**
      * 计算运费
      * @param $pid 商品ID
      * @param $addressid 收货地址ID
      * @param $count 购买数量
      */
     public function freight($pid=0,$addressid=false,$count=false){
        /* $notes = "应用" . $this->client_id . "，调用Banners不分页查询接口";
         addLog("Express/price", $_GET, $_POST, $notes);*/
        if($pid==0){
            $this->error("商品ID不能为空");
        }
         if($count==false){
             $this->error("缺少商品数量");
         }
         if($addressid==false){
             $this->error("缺少收货地址");
         }
         return 0;
     }

 }