<?php
namespace Api\Controller;
use Shop\Api\CategoryApi;
use Shop\Api\ProductApi;
use Shop\Api\ProductGroupApi;
use Shop\Api\ProductSkuApi;
use Shop\Api\SkuApi;
use Shop\Api\SkuvalueApi;
use Admin\Api\UserPictureApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/5
 * Time: 12:04
 */
class ProductController extends ApiController{

    protected  $allowType = array("json","rss","html");

    /**
     * 不分页查询
     * pname 商品名称(选填)
     * cate_id商品分类ID(选填)
     */
    public function queryNoPaging(){
        $notes = "应用".$this->client_id."，调用商品查询接口";
        addLog("Product/queryNoPaging",$_GET,$_POST,$notes);

        $map=array(
            'name'=>array('like','%'.I('pname','').'%'),
            'onshelf'=>1,   //已上架的产
        );
        $cate_id=I("cate_id",0);
        if($cate_id!=0){
            $map['cate_id']=$cate_id;
        }
        $result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
        for($i=0;$i<count($result['info']);$i++){
            $result['info'][$i]=$this->getImgUrl($result['info'][$i]);
        }
        if($result['status']){
            for($i=0;$i<count($result['info']['list']); $i++){
                $map=array(
                    'id'=>$result['info']['list'][$i]['cate_id'],
                );
                $cate=apiCall(CategoryApi::GET_INFO,array($map));
                $result['info']['list'][$i]['taxRate']=$cate['info']['taxrate'];
            }
            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr($result['info']);
        }
    }

    /**
     * 分页查询
     * pname:名称（模糊查询）(选填)
     * pageNo:当前页
     * pageSize:显示个数
     * cate_id商品分类ID(选填)
     */
    public function query(){
        $notes = "应用".$this->client_id."，调用商品分页查询接口";
        addLog("Product/query",$_GET,$_POST,$notes);

        $pname = $this->_post("pname","");
        $cate_id= $this->_post("cate_id",0);
        $pageNo = $this->_post("pageNo",0);
        $pageSize = $this->_post("pageSize",10);

        $map=array(
            'onshelf'=>1   //已上架的产品
        );
        if(!empty($pname)){
            $map['name'] = array('like','%'.$pname.'%');
        }

        if($cate_id!=0){
            $map['cate_id']=$cate_id;
        }

        $page = array('curpage'=>$pageNo,'size'=>$pageSize); //分页产品

        $result=apiCall(ProductApi::QUERY_WITH_COUNT,array($map,$page));
        //TODO: 需要优化

        for($i=0;$i<count($result['info']['list']);$i++){

           $result['info']['list'][$i]=$this->getImgUrl($result['info']['list'][$i]);

        }


        if($result['status']){
          for($i=0;$i<count($result['info']['list']); $i++){
              $map=array(
                  'id'=>$result['info']['list'][$i]['cate_id'],
              );
              $cate=apiCall(CategoryApi::GET_INFO,array($map));
              $result['info']['list'][$i]['taxRate']=$cate['info']['taxrate'];
          }

           $this->apiReturnSuc($result['info']);
       }else{
           $this->apiReturnErr("暂无商品");
       }
    }




    /**
     * 商品详情展示
     * pid 商品ID
     */
    public function detail(){
        $notes = "应用".$this->client_id."，调用商品详情接口";
        addLog("Product/detail",$_GET,$_POST,$notes);
        $pid=I('pid',0);
        if($pid==0){
            $this->apiReturnErr("请通过正常途径访问商品详情！");
        }
        $map=array(
            'id'=>$pid,
        );
        $result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));

        /*$imgUrl=explode(',',$result['info'][0]['img']);
        array_pop($imgUrl);
        $imurl="";
        foreach($imgUrl as $im){
            $imurl.=C('SITE_URL').'/index.php/Api/Picture/index?id='.$im.",";
        }
        $imurl=substr($imurl,0,strlen($imurl)-1);

        $result['info'][0]['img']=$imurl;
        $result['info'][0]['main_img']=C('SITE_URL').'/index.php/Api/Picture/index?id='.$result['info'][0]['main_img'];*/
        $result['info'][0]=$this->getImgUrl($result['info'][0]);
        $map=array(
            'id'=>$result['info'][0]['cate_id'],
        );
        $cate=apiCall(CategoryApi::GET_INFO,array($map));
        $result['info'][0]['taxRate']=$cate['info']['taxrate'];



        if($result['info'][0]['has_sku']!=0){
            $map=array(
                'product_id'=>$pid,
            );
            $result1=apiCall(ProductSkuApi::QUERY_NO_PAGING,array($map));
            $skuList=$result1['info'];



            $skuInfo=array();
            foreach($skuList as $key=> $skus){
                $skuIds=explode(';',$skus['sku_id']);
                array_pop($skuIds);
                foreach($skuIds as $sku){
                    $skuId=explode(':',$sku);
                    $map=array();
                    $map['id']=$skuId[0];
                    $resulta = apiCall(SkuApi::QUERY_NO_PAGING,array($map));
                    $skuInfo[$resulta['info'][0]['id']]['name']=$resulta['info'][0]['name'];
                    $map=array();
                    $map['id']=$skuId[1];
                    $result2 = apiCall(SkuvalueApi::QUERY_NO_PAGING,array($map));
                    $skus['sku']=$skus['sku'].$resulta['info'][0]['name'].':'.$result2['info'][0]['name'].';';
                    $skuInfo[$resulta['info'][0]['id']]['key']=$resulta['info'][0]['id'];
                    if(!in_array($result2['info'][0]['name'], $skuInfo[$resulta['info'][0]['id']]['value'])){

                        $skuInfo[$resulta['info'][0]['id']]['value'][$result2['info'][0]['id']]=array(
                            'key'=>$result2['info'][0]['id'],
                            'value'=>$result2['info'][0]['name'],
                        );
                    }

                }
                $skuList[$key]=$skus;
            }

        }
        $result['info'][0]['skuList']=$skuList;
        $result['info'][0]['skuInfo']=$skuInfo;
        if($result['status']){
            $this->apiReturnSuc($result['info'][0]);
        }else{
            $this->apiReturnErr("不存在的商品ID");
        }

    }


    private function getImgUrl($result){
        $imgUrl=explode(',',$result['img']);
        array_pop($imgUrl);
        $imurl="";
        $indexPHP = "index.php/";
        if(C('URL_MODEL') == 2) {
            $indexPHP = "";
        }
        foreach($imgUrl as $im){

            $imurl.=C('SITE_URL').'/'.$indexPHP.'Api/Picture/index?id='.$im.",";

        }
        $imurl=substr($imurl,0,strlen($imurl)-1);

        $result['img']=$imurl;

        $result['main_img']=C('SITE_URL').'/'.$indexPHP.'Api/Picture/index?id='.$result['main_img'];
        $result['img_poster']=C('SITE_URL').'/'.$indexPHP.'Api/Picture/index?id='.$result['img_poster'];

        $details=htmlspecialchars_decode($result['detail']);
        $details=json_decode($details);
        for($i=0;$i<count($details);$i++){
            $details[$i]=(array)$details[$i];
            $detailw[]=$details[$i]['ct'];
            $id=array('id'=>$detailw[$i]);
            $resultc=apiCall(UserPictureApi::QUERY_NO_PAGING,array($id));
            $resulta=C('SITE_URL').$resultc['info'][0]['path'];
            $aa[]=$resulta;
        }
        $result['detail']=$aa;
        return $result;
    }


    /**
     * 分页查询
     * pname:名称（模糊查询）(选填)
     * pageNo:当前页
     * pageSize:显示个数
     * cate_id商品分类ID(选填)
     */
    public function appIndex(){
        $notes = "应用".$this->client_id."，调用商品分页查询接口";
        addLog("Product/query",$_GET,$_POST,$notes);
        $map=array(
            'name'=>array('like','%'.I('pname','').'%'),   //模糊查询
            'onshelf'=>1   //已上架的产品
        );
        $cate_id=I("cate_id",0);
        if($cate_id!=0){
            $map['cate_id']=$cate_id;
        }
        $page = array('curpage'=>I('pageNo',0),'size'=>I('pageSize',10)); //分页产品

        $result=apiCall(ProductApi::QUERY_WITH_COUNT,array($map,$page));


        //dump($result);
        for($i=0;$i<count($result['info']['list']);$i++){
            //dump($result);
            $result['info']['list'][$i]=$this->getImgUrl($result['info']['list'][$i]);
        }
        if($result['status']){
            for($i=0;$i<count($result['info']['list']); $i++){
                $map=array(
                    'id'=>$result['info']['list'][$i]['cate_id'],
                );
                $cate=apiCall(CategoryApi::GET_INFO,array($map));
                $result['info']['list'][$i]['taxRate']=$cate['info']['taxrate'];
            }

            $this->apiReturnSuc($result['info']);
        }else{
            $this->apiReturnErr("暂无商品");
        }
    }


    function getSupportMethod()
    {
     /*   return array(
            'item'=>array(
                'param'=>'access_token|username|password',
                'return'=>'array(\"status\"=>返回状态,\"info\"=>\"信息\")',
                'author'=>'hebidu [hebiduhebi@163.com]',
                'version'=>'1.0.0',
                'description'=>'用户登录验证',
                'demo_url'=>'http://manual.itboye.com#',
            ),
        );*/

    }
}