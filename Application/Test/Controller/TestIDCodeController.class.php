<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/9/11
 * Time: 17:53
 */

namespace Test\Controller;


class TestIDCodeController extends  TestController{

    public function index(){
        srand(time());

        $array = array("13484379290","15858199064");
        for($i=0;$i<20;$i++){

            $ele = rand(10000000000,99999999999);
            array_push($array,$ele);
        }

        array_unique($array);
//        $i = 0;
//        foreach($array as $vo){
//            $idcode = getIDCode($vo);
//            echo $idcode."&nbsp;&nbsp;&nbsp;&nbsp;";
//            if((($i++) % 8) ==0 ){
//                echo "<br/>";
//            }
//
//            $entity = array(
//                'IDCode'=>$idcode,
//                'uid'=>100000,
//                'phone_validate'=>0,
//                'email_validate'=>0,
//            );
//
//            apiCall(MemberConfigApi::ADD,array($entity));
//
//        }

    }

}