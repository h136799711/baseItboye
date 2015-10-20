<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/6
 * Time: 15:10
 */
namespace Api\Service;


use OAuth2\Autoloader;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\UserCredentials;
use OAuth2\OpenID\GrantType\AuthorizationCode;
use OAuth2\Server;
use OAuth2\Storage\MyPdo;
use OAuth2\Storage\Pdo;

class OAuth2Service {

    private $dsn;
    private $uname;
    private $pwd ;

    private $server;

    const ALL = "ALL";
    /**
     * 隐式模式
     * http://www.dannysite.com/blog/178/
     * http://bshaffer.github.io/oauth2-server-php-docs/grant-types/implicit/
     */
    const IMPLICIT = "implicit";

    /**
     * 客服端授权模式
     * http://www.dannysite.com/blog/181/
     * http://bshaffer.github.io/oauth2-server-php-docs/grant-types/client-credentials/
     */
    const CLIENT_CREDENTIALS = "client_credentials";

    /**
     * http://www.dannysite.com/blog/177/
     * 授权码（Authorization Code）模式
     * http://bshaffer.github.io/oauth2-server-php-docs/grant-types/authorization-code/
     */
    const AUTHORIZATION_CODE = "authorization_code";

    /**
     *
     * password模式
     */
    const PASSWORD = "password";

    public function __construct(){
        $dbname = C('DB_NAME');
        $host = C('DB_HOST');
        $this->dsn = 'mysql:dbname='.$dbname.';host='.$host;
        $this->uname = C('DB_USER');//"root";
        $this->pwd = C('DB_PWD');//"1";
    }

    public function getMysqlStorage(){
        $storage = new MyPdo(array('dsn' => $this->dsn, 'username' => $this->uname, 'password' => $this->pwd),array('user_table'=>C('DB_PREFIX').'ucenter_member'));
        return $storage;
    }

    public function init($type){

        ini_set('display_errors',1);
        error_reporting(E_ALL);

        vendor("OAuth2.Autoloader");
        $loader = new Autoloader();
        $loader::register();

        //数据库存储
        $storage = $this->getMysqlStorage();

        // Pass a storage object or array of storage objects to the OAuth2 server class
        if($type == self::IMPLICIT){
            $server = new Server($storage,array('allow_implicit' => true));
        }else{
            $server = new Server($storage,array('allow_implicit' => false));
        }

        if($type == self::CLIENT_CREDENTIALS){
            // 客户端授权类型
            $server->addGrantType(new ClientCredentials($storage,array(
                // this request will only allow authorization via the Authorize HTTP Header (Http Basic)
                'allow_credentials_in_request_body => true'
            )));
        }elseif($type == self::AUTHORIZATION_CODE){
            // 增加授权码模式授权类型
            $server->addGrantType(new AuthorizationCode($storage));
        }elseif($type == self::PASSWORD){
            // 增加password模式授权类型
            $server->addGrantType(new UserCredentials($storage));
        }else{
            // 增加password模式授权类型
            $server->addGrantType(new UserCredentials($storage));
            // 增加授权码模式授权类型
            $server->addGrantType(new AuthorizationCode($storage));
            // 客户端授权类型
            $server->addGrantType(new ClientCredentials($storage));
        }

        return $server;
    }


}