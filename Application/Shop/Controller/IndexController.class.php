<?php namespace Shop\Controller;
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 青 <99701759@qq.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


use Common\Api\AccountApi;
use Shop\Api\BannersApi;
use Uclient\Model\OAuth2TypeModel;
use Shop\Api\CategoryPropApi;
use Shop\Api\CategoryApi;
use Shop\Api\ProductApi;
use Shop\Api\ProductGroupApi;
use Shop\Api\CategoryPropvalueApi;
use Shop\Api\ShoppingCartApi;
use Shop\Api\ProductSkuApi;
use Shop\Api\SkuApi;
use Shop\Api\SkuvalueApi;
use Admin\Api\UserPictureApi;
use Admin\Model\PictureModel;


class IndexController extends ShopController{
	/*
	 * 首页
	 * */
    public function index(){
    	$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$ss=array('onshelf'=>1);
		$orders = " createtime desc ";
		$page = array('curpage'=>I('post.p',0),'size'=>4);
		$result_new=apiCall(ProductApi::QUERY,array($ss,$page,$orders));
		//$this->assign('biggroup',$result['info']);
		$this->assign('group',$result1['info']);
		$this->assign('new',$result_new['info']['list']);
		$user=session('user');
		$this->assign('user',$user);
		$banners=apiCall(BannersApi::QUERY_NO_PAING);
		for ($i=0; $i <count($banners['info']) ; $i++) { 
			$id=array('id'=>$banners['info'][$i]['img']);
			$resultw=apiCall(UserPictureApi::QUERY_NO_PAGING,array($id));
			$baba[$i]['path']=$resultw['info'][0]['path'];
			$baba[$i]['url']=$banners['info'][0]['url'];
		}
		$this->assign('banners',$baba);
		$map= array(
			'g_id'=>getDatatree('FLASH_SALE'),
			'start_time'=>array(
				'LT',time()
			),
			'end_time'=>array(
				'GT',time()
			),
		);
		$result = apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($map));
		if(!$result['status']){
			$this->error($result['info']);
		}
		$hotmap= array(
			'g_id'=>15,
		);
		$results = apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($hotmap));
		$this->countcookie();
		$this->assign('flash_sales',$result['info']);
		$this->assign('hot',$results['info']);
		$this->theme($this->themeType)->display();
    }
	/*
	 * 商品详情
	 * */
	 public function spxq(){
	 	$user=session('user');
		$this->assign('user',$user);
	 	$map=array(
			'p_id'=>I('id',''),
			'g_id'=>getDatatree('FLASH_SALE'),
			'start_time'=>array(
				'LT',time()
			),
			'end_time'=>array(
				'GT',time()
			),
		);
		 $result=apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($map));
		
		//
		// dump( $result['info'][0]['price']);
		if($result['info']==NULL) {
			$map=array('id'=>I('id',''));
			$result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
			$result=$result['info'][0];
			$this->assign('isFlashSale',0);
		}else{
			$this->assign('saleprice',$result['info'][0]['price']);
			$this->assign('endtime',$result['info'][0]['end_time']);

			$result=$result['info'][0]['product'];
			$this->assign('isFlashSale',1);

		}
		$imgs=$result['img'];
		$detail=explode(',',$imgs); //分割字符串成数组
		
		if($detail[0]==""){
			$detail[0]=$result['main_img'];
		}
		while(count($detail)<5){
			$detail[]=$result['main_img'];
		}
	
		//dump($detail);
		//array_pop($detail);//删除最后一个空元素
//		$skus=explode(';',$result['properties']);
//		for ($i=0; $i <count($skus) ; $i++) { 
//			$skuss[]=explode(',',$skus[$i]);
//		}
		$map=array();
		$map['product_id']=I('id','');
		$results = apiCall(ProductSkuApi::QUERY_NO_PAGING,array($map));
		$skuList=$results[info];
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
		
		$result_sku=apiCall(CategoryPropApi::QUERY_NO_PAGING);
		$result_value=apiCall(CategoryPropvalueApi::QUERY_NO_PAGING);
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
		$this->assign('info1',$result_sku['info']);
		$this->assign('info2',$result_value['info']);
		$this->assign('detailimg',$detail);
		$this->assign('product',$result);
		 $cate_id=$result['cate_id'];
		 $cate=apiCall(CategoryApi::GET_INFO,array('id'=>$cate_id));
		 //dump($cate);
		 $this->assign('cate',$cate['info']);
		$this->countcookie();
//		dump($skuList);
//		dump($skuInfo);
		$this->assign('skuInfo',$skuInfo);
		$this->assign('skuList',$skuList);
		$details=htmlspecialchars_decode($result['detail']);
		$details=json_decode($details);
		for($i=0;$i<count($details);$i++){
			$details[$i]=(array)$details[$i];
			$detailw[]=$details[$i]['ct'];
			$id=array('id'=>$detailw[$i]);
			$resultc=apiCall(UserPictureApi::QUERY_NO_PAGING,array($id));
			$resulta=$resultc['info'][0]['path'];
			$aa[]=$resulta;
		}
		$this->assign('details',$aa);
	 	$this->theme($this->themeType)->display();

	 }

	/**
	 * 手机注册
	 */
	public function phoneregister(){
		if(IS_GET){
			$map=array('id'=>140);
			$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
			$this->assign('group',$result1['info']);
			$this->countcookie();
			$this->theme($this->themeType)->display();
		}else{
			$phone=I('phone','');
			$type='M';
			$idcode=getIDCode($phone,$type);
			$entity=array(
				'username'=>"M".$phone,
				'password'=>I('pwd',''),
				'mobile'=>I('phone',''),
				'email'=>I('email',''),
				'reg_time'=>time(),
				'from'=>OAuth2TypeModel::SELF,
				'realname'=>'',
				'birthday'=>'',
				'nickname'=>I('uname',''),
				'idcode'=>$idcode,
			);
			$result=apiCall(AccountApi::REGISTER,array($entity));
			if($result['status']){
				$id=$result['info'];
				$user=apiCall(AccountApi::GET_INFO,array($id));
				session('user_show',$user['info']);
//				dump($user);
				$this->success('注册成功!',U('Shop/Index/succshow'));
			}else{
				$this->error($result['info']);
			}
		}
		
	}
	 /*
	  * 加入购物车
	  * */
	public function savecookie(){
//		cookie('shopcat',null);
		$user=session('user');
		$id=I('pid',0);
		$count=I('counts',1);
		$skuvalue=I('skuvalue','');
		$skuprice=I('skuprice',0);
		$qgprice=I('qgprice',0);
		$ids=array('p_id'=>$id,'uid'=>$user['id'],'sku_desc'=>$skuvalue);
		$result=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($ids));
		if($user!=null){
			if($result['info']==null){
				$map=array('id'=>$id);
				$result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
				if($skuprice==0 &&$qgprice==0){
					$price=$result['info'][0]['price'];
					
				}
				if($skuprice!=0){
					$price=$skuprice;
				}
				if($qgprice!=0){
					$price=$qgprice;
				}
					
					
				
//				dump($result);
				$entity=array(
					'uid'=>$user['id'],
					'create_time'=>time(),
					'update_time'=>time(),
					'store_id'=>$result['info'][0]['storeid'],
					'sku_desc'=>$skuvalue,
					'p_id'=>$id,
					'icon_url'=>$result['info'][0]['main_img'],
					'count'=>$count,
					'name'=>$result['info'][0]['name'],
					'price'=>$price,
					'ori_price'=>$result['info'][0]['ori_price'],
				);
//				dump($entity);
				$resulta=apiCall(ShoppingCartApi::ADD,array($entity));
				if($resulta['status']){
					$this->success('成功添加购物车!');
				}else{
					$this->error('添加购物车失败!');
				}
			}else{
				$id=$result['info'][0]['id'];
				$entity=array('count'=>$result['info'][0]['count']+$count);
				$resulta=apiCall(ShoppingCartApi::SAVE_BY_ID,array($id,$entity));
				$this->success('成功添加购物车!');
			}
		}else{
			$ck=cookie('shopcat');
			if($ck==null){
				$ck[]=$id.",".$count.",".$skuprice.",".$skuvalue;
				cookie('shopcat',$ck,24*3600);
				if($ck!=null){
					$this->success('成功添加购物车!');
				}else{
					$this->error('添加购物车失败!');
				}
			}else{
				for ($i=0; $i <count($ck) ; $i++) {
					$a= explode(',',$ck[$i]);
					$a= $a[0];
					if($id==$a){
						$this->success('成功添加购物车!');
					}
				}
				$ck[]=$id.",".$count.",".$skuprice.",".$skuvalue;
				//dump($ck);
				cookie('shopcat',$ck,24*3600);
				$this->success('成功添加购物车!');
				
			}
			
		}
		
		
	}
	/*
	 * 商品分类
	 * */
	public function lists(){
		$user=session('user');
		$this->assign('user',$user);
		$id=array('cate_id'=>I('id',''),'onshelf'=>1);
		$page = array('curpage'=>I('post.p',0));
		$result=apiCall(ProductApi::QUERY,array($id,$page));
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
//		dump($result);
		$this->assign('lists',$result['info']['list']);
		$this->countcookie();
		$this->theme($this->themeType)->display();
	}
	/*
	 * 搜索
	 * */
	public function sousuo(){
		
		$user=session('user');
		$this->assign('user',$user);
		$name=I('name','');
		$map['name'] = array('like','%'. $name . '%','onshelf'=>1);
		$result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
//		dump($result);
		$this->assign('lists',$result['info']);
		$this->countcookie();
		$this->theme($this->themeType)->display('Index/lists');
	}
	
	/*
	 * 注册
	 * TODO:短信注册
	 * */
	public function register(){
		if(IS_GET){
			$map=array('id'=>140);
			$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
			$this->assign('group',$result1['info']);
			$this->countcookie();
			$this->theme($this->themeType)->display();
		}else{
			$phone=I('phone','');
			$type='M';
			$idcode=getIDCode($phone,$type);
			$entity=array(
				'username'=>I('uname',''),
				'password'=>I('pwd',''),
				'mobile'=>I('phone',''),
				'email'=>I('email',''),
				'reg_time'=>time(),
				'from'=>OAuth2TypeModel::SELF,
				'realname'=>'',
				'birthday'=>'',
				'nickname'=>I('uname',''),
				'idcode'=>$idcode,
			);
			$result=apiCall(AccountApi::REGISTER,array($entity));
			if($result['status']){
				$id=$result['info'];
				$user=apiCall(AccountApi::GET_INFO,array($id));
				session('user_show',$user['info']);
//				dump($user);
				$this->success('注册成功!',U('Shop/Index/succshow'));
			}else{
				$this->error($result['info']);
			}
//			dump($result);
		}
	}
	/**
	 * 意见反馈
	 */
	public function yjfk(){
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
		
		$this->countcookie();
		
		$this->theme($this->themeType)->display();
	}
	/**
	 * 注册成功跳转
	 */	
	public function succshow(){
		$map=array('id'=>140);
		$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$result1['info']);
		$this->countcookie();
		$usershow=session('user_show');
		$this->assign('users',$usershow);
		$this->theme($this->themeType)->display();
	}
	/*
	 * 登录
	 * TODO：第三方登录
	 * */
	public function login(){
		if(IS_GET){
//			dump("dsafasdfasd");
			$map=array('id'=>140);
			$result1=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
			$this->assign('group',$result1['info']);
			$this->countcookie();
			$this->theme($this->themeType)->display();
		}else{
			$username=I('uname','');
			$password=I('pwd','');
			$type=1;
			$from=OAuth2TypeModel::SELF;
//			dump("jinlaidasfasdf");
			$result=apiCall(AccountApi::LOGIN,array($username,$password,$type,$from));
			if($result['status']){
				$id=$result['info'];
				$user=apiCall(AccountApi::GET_INFO,array($id));
				session('user',$user['info']);
				$this->success('登陆成功!正在跳转登录页面',U('Shop/Index/index'));
			}else{
				$username=I('uname','');
				$password=I('pwd','');
				$type=3;
				$from=OAuth2TypeModel::SELF;
	//			dump("jinlaidasfasdf");
				$result=apiCall(AccountApi::LOGIN,array($username,$password,$type,$from));
				if($result['status']){
					$id=$result['info'];
					$user=apiCall(AccountApi::GET_INFO,array($id));
					session('user',$user['info']);
					$this->success('登陆成功!正在跳转登录页面',U('Shop/Index/index'));
				}else{
					$this->error($result['info']);
				}
				
			}
		}
	}
	
	public function distributor(){
		
    	if(IS_GET){
    		$referrer=I('referrer');
			$map=array(
				'id'=>$referrer
			);
			$result=apiCall(WxuserApi::QUERY_NO_PAGING ,array($map));
			$this->assign('referrer',$result[info][0]);
			$this->countcookie();
       	 	$this->theme($this->themeType)->display();
		}else if(IS_AJAX){
			$map=array(
				'id'=>$this->userinfo['id'],
			);
			$userInfo= apiCall(WxuserApi::QUERY_NO_PAGING,array($map));
			$userInfo['info']['groupid']=13;
			$result=apiCall(WxuserApi::SAVE_BY_ID,array($this->userinfo['id'],$userInfo['info']));
			$entity=array(
				'name'=>I("name"),
				'phone'=>I("phone"),
				'address'=>I("address"),
				'create_time'=>time(),
				'uid'=>$userInfo['info'][0]['uid'],
				'wxaccount_id'=>1
			);
			$result=apiCall(DistributorInfoApi::ADD,array($entity));
			session('[destroy]'); // 删除session
			$this->success('操作成功',U('Shop/Index/index'));
		}
    }
	/**
	 * 手机app
	 */
	public function phoneapp(){
		$this->theme($this->themeType)->display();
	}
	
	
	/*
	 * 立即购买
	 * */
	public function buynow(){
		$user=session('user');
		$id=I('pid',0);
		$count=I('counts',1);
		$skuvalue=I('skuvalue','');
		$skuprice=I('skuprice',0);
		$ids=array('p_id'=>$id,'uid'=>$user['id']);
		$result=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($ids));
		if($user!=null){
//			dump($result);
			if($result['info']==null){
				$map=array('id'=>$id);
				$result=apiCall(ProductApi::QUERY_NO_PAGING,array($map));
//				dump($result);
				$mapss=array(
				'p_id'=>$id,
				'g_id'=>getDatatree('FLASH_SALE'),
				'start_time'=>array(
						'LT',time()
					),
				'end_time'=>array(
						'GT',time()
					),
				);
			 	$resultw=apiCall(ProductGroupApi::QUERY_WITH_PRODUCT,array($mapss));
				
				if($resultw['info']!=NULL){
					$price=$resultw['info'][0]['price'];
				}
				if($resultw['info']==NULL){
					$price=$result['info'][0]['price'];
				}
				$entity=array(
					'uid'=>$user['id'],
					'create_time'=>time(),
					'update_time'=>time(),
					'store_id'=>$result['info'][0]['storeid'],
					'sku_desc'=>$skuvalue,
					'p_id'=>$id,
					'icon_url'=>$result['info'][0]['main_img'],
					'count'=>$count,
					'name'=>$result['info'][0]['name'],
					'price'=>$price,
					'taxRate'=>I('shuilv','0.1'),
					'ori_price'=>$result['info'][0]['ori_price'],
				);
//				dump($entity);
				$resulta=apiCall(ShoppingCartApi::ADD,array($entity));
				if($resulta['status']){
					$this->success('',U('Shop/ShopCart/shopcart'));
				}else{
					$this->error('失败了，请稍后再试!');
				}
			}else{
				$this->success('',U('Shop/ShopCart/shopcart'));
			}
		}else{
			$ck=cookie('shopcat');
			if($ck==null){
				$ck[]=$id.",".$count.",".$skuprice.",".$skuvalue;
				cookie('shopcat',$ck,24*3600);
				if($ck!=null){
					$this->success('',U('Shop/ShopCart/shopcart'));
				}else{
					$this->error('失败了，请稍后再试!');
				}
			}else{
				for ($i=0; $i <count($ck) ; $i++) {
					$a= explode(',',$ck[$i]);
					$a= $a[0];
					if($id==$a){
						$this->success('',U('Shop/ShopCart/shopcart'));
					}
				}
				$ck[]=$id.",".$count.",".$skuprice.",".$skuvalue;
				//dump($ck);
				cookie('shopcat',$ck,24*3600);
				$this->success('',U('Shop/ShopCart/shopcart'));
				
			}
			
		}
//				
	}
	/*
	 * 退出登录
	 * */
	public function loginout(){
		session('user',null); // 删除name;
		$this->success('退出成功！',U('Shop/Index/index'));
	}
	protected function _initialize(){
		parent::_initialize();
		$showStartPage = true;
		$last_entry_time = cookie("last_entry_time");
		if(empty($last_entry_time)){
			//一小时过期
			cookie("last_entry_time",time(),3600);
			$last_entry_time = time();			
		}elseif(time() - $last_entry_time < 20*60){
			$showStartPage = false;
		}else{
			//一小时过期
			cookie("last_entry_time",time(),3600);
		}
		
		$this->assign("showstartpage",$showStartPage);

	}
	/*
	 * 用户协议
	 * */
	public function xieyi(){
		$this->theme($this->themeType)->display();
	}
	/*
	 * 购物车统计
	 * */
	public function countcookie(){
		$user=session('user');
		$jl=0;
		if($user!=null){
			$map=array('uid'=>$user['id']);
			$result=apiCall(ShoppingCartApi::QUERY_NO_PAGING,array($map));
//			dump($result);
			$jl=count($result['info']);
			$ckcount=cookie('shopcat');
			$c=count($ckcount);
			$this->assign('count',$c+$jl);
		}else{
			$ckcount=cookie('shopcat');
			$c=count($ckcount);
			$this->assign('count',$c);
		}
		
		
	}
	 /*
	  * 上传头像
	  * */
	public function uploadPicture(){
		if(IS_POST){
	        /* 返回标准数据 */
	        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
	
	        /* 调用文件上传组件上传文件 */
	        $Picture = new PictureModel();
	        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
	        $info = $Picture->upload(
	            $_FILES,
	            C('PICTURE_UPLOAD'),
	            C('PICTURE_UPLOAD_DRIVER'),
	            C("UPLOAD_{$pic_driver}_CONFIG")
	        ); //TODO:上传到远程服务器
	
	        /* 记录图片信息 */
	        if($info){
	            $return['status'] = 1;
	            $return = array_merge($info['download'], $return);
	        } else {
	            $return['status'] = 0;
	            $return['info']   = $Picture->getError();
	        }
//			dump($return);
	        /* 返回JSON数据 */
	        $this->ajaxReturn($return);
		}
		
	}
	
}

