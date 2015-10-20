<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */

namespace Admin\Controller;
use Shop\Api\FinAccountBalanceHisApi;

class IndexController extends AdminController {

	//首页
    public function index(){
    		
        $this->display();
    }

    /**
     * 交易首页
     */
    public function tradeIndex(){
    	$map=array('status'=>2,'dtree_type'=>3);
    	$result=apiCall(FinAccountBalanceHisApi::QUERY_NO_PAGING,array($map));
		$this->assign('count',count($result['info']));
		$this->display();
    }

    /**
     * 分销首页
     */
    public function distribution(){
       $this->display();
    }
}