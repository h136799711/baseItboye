<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/7
 * Time: 10:10
 */
namespace Api\Controller;


use Api\Service\OAuth2Service;
use OAuth2\Request;
use OAuth2\Response;
use OAuth2Manage\Api\ClientsApi;
use Think\Controller\RestController;

/**
 * 授权控制器
 * 允许我的应用内的用户访问第三份应用.
 * allow for your users to authorize third party applications.
 * 相对于直接发放 访问令牌(Access Token)就如第一个token控制器例子中.
 *
 * 在这个示例中Authorize控制器,只发放一次令牌，一旦用户允许通过本次请求.
 * Instead of issuing an Access Token straightaway as happened in the first token controller example,
 * in this example an authorize controller is used to only issue a token once the user has authorized the request.
 *
 * Class AuthorizeController
 * @package Api\Controller
 */
class AuthorizeController extends RestController{



    protected $allowMethod = array('get','post');

    // REST允许请求的资源类型列表
    protected   $allowType      =   array('json');

    public function index(){
        $response_type = I('get.response_type','');
        $client_id = I('get.client_id','');
        $state = I('get.state','');
        $redirect_uri = I('get.redirect_uri','');
        $redirect_uri = urldecode($redirect_uri);
        //TODO: 加入调用次数限制

        if($response_type == "code"){
//            action_log("oauth2_authorization_code",null,null,$client_id);
            $this->authorization_code($client_id,$state,$redirect_uri);
        }elseif($response_type == "token"){
            //implicit模式下
//            action_log("oauth2_implicit",null,null,$client_id);
            $this->implicit();

        }else{
            $this->apiReturnErr("Access Deny");
        }


    }

    private function implicit(){
        $oauth2 = new OAuth2Service();
        $server = $oauth2->init(OAuth2Service::AUTHORIZATION_CODE);
        $server->setConfig("allow_implicit",true);
        $request = Request::createFromGlobals();
        $response = new Response();

        // validate the authorize request
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $params = ($response->getParameters());
            $this->apiReturnErr($params);
            die;
        }

        // display an authorization form
        if (empty($_POST)) {
            $clients = apiCall(ClientsApi::GET_INFO,array(array('client_id'=>$client_id)));
            if(!$clients['status']) {
                $this->apiReturnErr($clients['info']);
            }
            $client_name = $clients['info']['client_name'];

            $this->assign("client_name",$client_name);
            echo $this->fetch("partials/login");
            exit();
        }

        // print the authorization code if the user has authorized your client
        $is_authorized = ($_POST['authorized'] === 'yes');

        $server->handleAuthorizeRequest($request, $response, $is_authorized);
        if ($is_authorized) {
            // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
            $location = $response->getHttpHeader('Location');
            //access_token 就在url中的#access_token=2YotnFZFEjr1zCsicMWpAA&state=xyz&token_type=bearer&expires_in=3600

            redirect($location);
        }

    }

    private function authorization_code($client_id,$state,$redirect_uri){

        $oauth2 = new OAuth2Service();
        $server = $oauth2->init(OAuth2Service::AUTHORIZATION_CODE);

        $request = Request::createFromGlobals();
        $response = new Response();

        // validate the authorize request
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $params = ($response->getParameters());
            $this->apiReturnErr($params);
//            $response->send();
            die;
        }

        // display an authorization form
        if (empty($_POST)) {
            $clients = apiCall(ClientsApi::GET_INFO,array(array('client_id'=>$client_id)));
            if(!$clients['status']) {
                $this->apiReturnErr($clients['info']);
            }
            $client_name = $clients['info']['client_name'];

            $this->assign("client_name",$client_name);
            echo $this->fetch("partials/login");
            exit();
        }

        // print the authorization code if the user has authorized your client
        $is_authorized = ($_POST['authorized'] === 'yes');

        $server->handleAuthorizeRequest($request, $response, $is_authorized);
        if ($is_authorized) {
            // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

            redirect($redirect_uri.'?code='.$code.'&state='.$state);
        }

        $params = $response->getParameters();
        $this->apiReturnErr($params);
    }


    function getSupportMethod()
    {
        // TODO: Implement getSupportMethod() method.
    }
}