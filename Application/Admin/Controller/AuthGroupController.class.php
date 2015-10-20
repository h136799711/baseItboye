<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Controller;

class AuthGroupController extends AdminController{
	
	public function index(){
		$map = null;
		$page = array('curpage'=>I('get.p'),'size'=>C('LIST_ROW'));
		$order = " id asc ";
		
		$result = apiCall("Admin/AuthGroup/query", array($map,$page,$order));
				
		if($result['status']){
			$this->assign("show",$result['info']['show']);
			$this->assign("list",$result['info']['list']);
			$this->display();
		}else{
			$this->error($result['info']);
		}
	}
		
	public function add(){
		if(IS_POST){
			$entity = array(
				'title'=>I('post.title','','trim'),
				'notes'=>I('post.notes','','trim')
			);
			parent::addTo($entity);
		}else{
			$this->display();
		}
		
	}
	
	public function save(){
		$id = I('post.id',0);
		if(IS_POST){
			$entity = array(
				'notes'=>I('post.notes','','trim')
			);
			$result = apiCall("Admin/AuthGroup/saveByID", array($id,$entity));
			if($result['status']){
				$this->success("操作成功~页面将自动跳转",U('Admin/AuthGroup/index'));
			}else{
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
		}
	}
	
	public function writeRules(){
		$groupid = I('post.groupid',-1);
		$modulename = I('post.modulename','');
		$map = array();
		
		$rules = I('post.rules','');
		if(is_array($rules)){
			$rules = implode(",", $rules);
			$rules = $rules.',';
		}
		$result = apiCall('Admin/AuthGroup/writeRules',array($groupid,$rules));
		if($result['status']){
			$this->success("操作成功~页面将自动跳转");
		}else{
			LogRecord($result['info'], __FILE__.__LINE__);
			$this->error($result['info']);
		}
	}	
	
	public function writeMenuList(){
		
		$groupid = I('post.groupid',-1);
		$menulist = I('post.menulist','');
		if($menulist == ","){
			$menulist = "";
		}
		$result = apiCall('Admin/AuthGroup/writeMenuList',array($groupid,$menulist));
		
		if($result['status']){
			$this->success("操作成功~页面将自动跳转");
		}else{
			LogRecord($result['info'], __FILE__.__LINE__);
			$this->error($result['info']);
		}
	}
	
	
		
}
