<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 09:45
 */

namespace Test\Controller;

use Api\Service\OAuth2ClientService;
use Common\Api\AccountApi;
use Think\Controller\RestController;
use Uclient\Model\OAuth2TypeModel;

/**
 *
 *
 * @Test AccountApi
 * Class TestAccountApiController
 * @package Test\Controller
 *
 */
class TestAccountApiController extends RestController {

    public function __construct(){
        parent::__construct();

        $client_id = C('CLIENT_ID');
        $client_secret = C('CLIENT_SECRET');
        $config = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
        );
        $client = new OAuth2ClientService($config);
        $access_token = $client->getAccessToken();
//        dump($access_token);
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }

        $this->assign("error",$access_token);
    }


    /**
     *
     */
    public function testLogin(){
        $this->display();
    }


    public function testRegister(){
        var_dump(md5(sha1("1") . UC_AUTH_KEY));
        $this->display();
    }


    /**
     *
     */
    public function testUpdate(){
        $this->display();
    }


    public function testIsExists(){
        $this->display();
    }

    public function testUpdatePsw(){

        $this->display();

    }

    public function testGetInfo(){
        if(IS_POST){
            $id = I('post.id',0);

            $userinfo = apiCall(AccountApi::GET_INFO,array($id));
            dump($userinfo);
        }else{
            $this->display();
        }
    }


    /**
     *
     */
    public  function index(){
        import("Org.String");

        $username = \String::randString(9,0);

        $password = \String::randString(6);

        $entity = [
            'username'=>$username,
            'password'=>$password,
            'from'=>OAuth2TypeModel::OTHER_APP,
            'email'=>'',
            'phone'=>'',
        ];

//        $result =  AccountApi::REGISTER($entity);
        
//        $this->ajaxReturn($result,"xml");
    }

}