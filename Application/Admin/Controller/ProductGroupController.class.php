<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

use Shop\Api\ProductGroupApi;

class ProductGroupController extends AdminController{
		
	public function add(){
		$product_id = I('post.product_id',0);
		$groups = I('post.groups',array());
		if($product_id == 0){
			$this->error("参数错误！");
		}
		
		if(count($groups) > 0){
			foreach($groups as $groupid){
				$entity  = array(
					'p_id'=>$product_id,
					'g_id'=>$groupid,
				);

				$result = apiCall(ProductGroupApi::GET_INFO, array($entity));
				if(!$result['status']){
					$this->error($result['info']);
				}

				if($groupid==5999){
					$entity['start_time']=strtotime(I('post.start_time_4'));
					$entity['end_time']=strtotime(I('post.end_time_4'));
					$entity['price']=(float)I('price');
				}
				//dump($result);
				if(is_null($result['info'])){
					$result1 = apiCall(ProductGroupApi::ADD, array($entity));
					if(!$result1['status']){
						$this->error($result1['info']);
					}				
				}else{
					$result1=apiCall(ProductGroupApi::SAVE_BY_ID,array($result['info']['id'],$entity));
					if(!$result1['status']){
						$this->error($result1['info']);
					}
				}
				
				
			}
			array_push($groups,"-1");
			$map = array('g_id'=>array('not in',$groups));
			$map['p_id'] = $product_id;
			$result = apiCall(ProductGroupApi::DELETE , array($map));
		
		}else{
			$result = array('status'=>true);
			$result = apiCall(ProductGroupApi::DELETE, array(array('p_id'=>$product_id)));
		}
		
		if($result['status']){
			$this->success("操作成功！");
		}else{
			$this->error($result['info']);
		}
	}
	
}
