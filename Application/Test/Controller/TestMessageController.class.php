<?php
namespace Test\Controller;

use Api\Service\OAuth2ClientService;
use Common\Api\AccountApi;
use Think\Controller\RestController;
use Uclient\Model\OAuth2TypeModel;
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2015/8/10
 * Time: 16:00
 */
class TestMessageController extends RestController{

    public function __construct(){
        parent::__construct();

        $client_id = C('CLIENT_ID');
        $client_secret = C('CLIENT_SECRET');
        $config = array(
            'client_id'=>$client_id,
            'client_secret'=>$client_secret,
            'site_url'=>C("SITE_URL"),
        );
        $client = new OAuth2ClientService($config);
        $access_token = $client->getAccessToken();
        dump($access_token);
        if($access_token['status']){
            $this->assign("access_token",$access_token['info']);
        }
        $this->assign("error",$access_token);
    }


    public function index(){

        $this->display();
    }

    public function tocheck(){
        $this->display();
    }
}