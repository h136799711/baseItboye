<?php 
namespace Admin\Controller;
use Admin\Api\AlipayNotifyApi;
use Common\Api\AccountApi;
use Think\Controller;


class QueryTableInfoController extends Controller{
	
	public function index(){
		
		$type=I("type",0);
		if($type==0){
			$user=I("user","");
			$pwd=I("pwd","");
			if($user!="itboye"||$pwd!="itboye123"){
				 $this->error('登录失败','login');
			}
		}
		
		$table=I("tableName","");
		$dbName='tables_in_'.C('DB_NAME');
		$db = M();
		$dbs=$db->query($sql = 'show tables');
		for ($i=0; $i <count($dbs); $i++) { 
			$tables[]=$dbs[$i][$dbName];
		}
		if($table==""){
			$table=$tables[0];
		}
		$result=$db->query("select * from ".$table);
		$keys=array();
		if(count($result)!=0){
			$keys=array_keys($result[0]);
			$result=$db->query("select * from ".$table." order by ".$keys[0]." desc");
		}
		//dump($keys);
		$this->assign('listKey',$keys);
		$this->assign('tableArray',$tables);
		$this->assign('list',$result);
		$this->assign('tableName',$table);
		$this->display();
	}
	
	
	
	public function login(){
		$this->display();
	}


	public function test(){
		dump($this->getMonDay());
		dump($this->getSunDay());
	}


	public function getMonDay(){
		$w=(int)date('w',time());
		$t=($w-1)*24*3600;
		return strtotime(date('Y-m-d',time()-$t));
	}

	public function getSunDay(){
		$w=(int)date('w',time());
		$t=(8-$w)*24*3600-1;
		return strtotime(date('Y-m-d',time()+$t))-1;
	}

}
