<?php
namespace Shop\Controller;
use Admin\Api\DatatreeApi;
use Admin\Api\PostApi;
use Shop\Api\CategoryApi;

/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/29
 * Time: 15:04
 */
 class ArticleController extends   ShopController{
     public function index(){
		$user=session('user');
        $map=array(
            'id'=>I('id',1)
        );
        $result=apiCall(PostApi::GET_INFO,array($map));
        $this->assign("article",$result['info']);
		$index=A('Index');
		$index->countcookie();
        $this->assign('user',$user);
        $array=$this->articletitles();
        $this->assign("articletitles",$array);$map=array('id'=>140);
		$resultw=apiCall(CategoryApi::QUERY_NO_PAGING,array($map));
		$this->assign('group',$resultw['info']);
        $this->theme($this->themeType)->display();
     }


     /**
      * ��ȡ����
      * @return mixed
      */
     public function articletitles(){
         $map=array(

         );
         $order=" post_category asc";
         $result=apiCall(PostApi::QUERY_NO_PAGING,array($map,$order));

         $result2=apiCall(DatatreeApi::QUERY_NO_PAGING,array(array('parentid'=>21)));

         $i=0;
         foreach($result2['info'] as $r2){
             $array[$i]['name']= $r2['name'];
             $j=0;
             foreach($result['info'] as $r){
                 // dump($r['post_category']);
                 //dump($r2['id']);
                 if($r['post_category']==$r2['id']){
                     $array[$i]['list'][$j]['id']= $r['id'];
                     $array[$i]['list'][$j]['title']= $r['post_title'];
                 }
                 $j++;
             }
             $i=$i+1;

         }
         return $array;
     }
 }