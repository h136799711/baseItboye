<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Think\Controller;
use Shop\Api\ProductApi;
use Shop\Api\ShoppingCartApi;
use Shop\Api\CategoryPropApi;
use Shop\Api\CategoryApi;
use Shop\Api\ProductGroupApi;
class ShopCartController extends ShopController {
	/*
	 * 购物车
	 * */
    public function shopcart(){
    	$user=session('user');
    	$pros=cookie('shopcat');
//		dump($pros);
		if($user==null){
			for($i=0;$i<count($pros);$i++){
			$a= explode(',',$pros[$i]);
			$a=$a[0];
			$b= explode(',',$pros[$i]);
			$b=$b[1];
			$c= explode(',',$pros[$i]);
			$c=$c[2];
			$d= explode(',',$pros[$i]);
			$d=$d[3];
			$ct=substr($pros[$i], strpos($pros[$i],',')+1);
			$map=array('id'=>$a);
			$results[$i]=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
			$mapss=array(
			'p_id'=>$a,
			'g_id'=>getDatatree('FLASH_SALE'),
			'start_time'=>array(
					'LT',time()
				),
			'end_time'=>array(
					'GT',time()
				),
			);
		 	$resultw=apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($mapss));
			$results[$i]=$results[$i]['info'][0];
			if($resultw['info']==null){
				
			}else{
				$results[$i]['price']=$resultw['info'][0]['price'];
			}
			if($result['info'][$i]['sku_desc']==''){
				if($c!=null){
					$results[$i]['has_sku']=1;
					$results[$i]['count']=$b;
					$results[$i]['skuprice']=$c;
					$results[$i]['skuvalue']=$d;
				}else{
					$results[$i]['has_sku']=0;
//					$map=array('id'=>$a);
//					$results[$i]=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
//					$results[$i]=$results[$i]['info'][0];
					$results[$i]['count']=$b;
				}
			}else{
				$results[$i]['has_sku']=0;
//				$map=array('id'=>$a);
//				$results[$i]=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
//				$results[$i]=$results[$i]['info'][0];
				$results[$i]['count']=$b;
				$results[$i]['skuprice']=$result['info'][$i]['price'];
				$results[$i]['skuvalue']=$result['info'][$i]['sku_desc'];
				
			}
//			dump($results);
		}
		}else{
			$maps=array('uid'=>$user['id']);
			$result=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($maps));
			for($i=0;$i<count($pros);$i++){
				$as= explode(',',$pros[$i]); 
				$ass=$as[0];
				$maps=array(
					'uid'=>$user['id'],
					'p_id'=>$ass,
				);
				$map=array(
					'id'=>$ass,
				);
				$result=apiCall(ShoppingCartApi::GET_INFO,array($maps));
				$producta=apiCall(ProductApi::GET_INFO,array($map));
//				dump($as);
				$mapss=array(
				'p_id'=>$ass,
				'g_id'=>getDatatree('FLASH_SALE'),
				'start_time'=>array(
						'LT',time()
					),
				'end_time'=>array(
						'GT',time()
					),
				);
			 	$resultw=apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($mapss));
				if($as[2]!=''){
					$price=$as[2];
				}
				if($resultw['info']!=NULL){
					$price=$resultw['info'][0]['price'];
				}
				if($as[2]=='' && $resultw['info']==NULL){
					$price=$producta['info']['price'];
				}
				if($result['info']==NULL){
					//插入
					$entity=array(
						'uid'=>$user['id'],
						'create_time'=>time(),
						'update_time'=>time(),
						'store_id'=>$producta['info']['storeid'],
						'p_id'=>$ass,
						'sku_desc'=>$as[3],
						'icon_url'=>$producta['info']['main_img'],
						'count'=>$as[1],
						'name'=>$producta['info']['name'],
						'price'=>$price,
						'ori_price'=>$producta['info']['ori_price'],
					);
					$resultsss=apiCall(ShoppingCartApi::ADD,array($entity));
					
				}else{
					$id=$result['info']['id'];
					$entity=array('count'=>$result['info']['count']+$as[1]);
					$resultsss=apiCall(ShoppingCartApi::SAVE_BY_ID,array($id,$entity));
				}
			}
			cookie('shopcat',null);
			$maps=array('uid'=>$user['id']);
			$results=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($maps));
			$results=$results['info'];
		}
		
		
		$this->assign('products',$results);
		$keys=array();
//		$this->arraySortByKey($results, 'storeid',true,&$keys);''
//		$results[]=array_values(array_unique($keys));
		session('gouwuche',$results);
		
//		dump($results);
		$index=A('Index');
		$index->countcookie();
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
		$this->assign('user',$user);
        $this->theme($this->themeType)->display();
    }
	
	function arraySortByKey(array $array, $key, $asc = true,$keys) {
	  $result = array();
	  // 整理出准备排序的数组
	  foreach ( $array as $k => &$v ) {
	    $values[$k] = isset($v[$key]) ? $v[$key] : '';
	  }
	  unset($v);
	  // 对需要排序键值进行排序
	  $asc ? asort($values) : arsort($values);
	  // 重新排列原有数组
	 
	  foreach ( $values as $k => $v ) {
	  	 $keys[]=$v;
	    $result[$k] = $array[$k];
	  }
	  
	  return $result;
}
	
	
	public function delcookie(){
		$user=session('user');
		$pros=cookie('shopcat'); 
		$a=I('id','');
		$map=array('p_id'=>$a);
		
		if($user!=null){
			$result=apiCall(ShoppingCartApi::DELETE,array($map));
			if($result['status']){
				$this->ajaxReturn(1,'json');
			}
		}else{
			for ($i=0; $i <count($pros) ; $i++) {
				$as= explode(',',$pros[$i]); 
				$as=$as[0];
				if($a==$as){
					array_splice($pros,$i,1); 
				}
			}
			cookie('shopcat',$pros,24*3600);
			$this->ajaxReturn(1,'json');
		}
			
			
//		}
		
	}
	
	
}