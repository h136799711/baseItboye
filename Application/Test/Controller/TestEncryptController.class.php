<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/8/31
 * Time: 21:53
 */

namespace Test\Controller;

use Api\Service\RSAService;
use Think\Controller;

class TestEncryptController extends  Controller {

    private $privateKey;
    private $publicKey;

    /**
     *
     */
    public function __construct(){

        $this->privateKey = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDArYDWTnG9zGQn/6YqJnNRAuUCV7GV5SoEgkk0rafBITBhexDY
KRWAsprTsbzsJsVjC9llv75aPMHSx9qPEu9u9wNrWjI5ij/32SWDDsSQaWPjCiDL
uiGqWauNH+U64Ffwv1lzcffsxg3LJ/mwiP51bfJEaohVH6m+bJjZIwVlywIDAQAB
AoGBALNl2LuxNj4d/HMxmSlNu7kGFOxlcje6s7CXDko6FiPAHyfkSf654gd+RKIu
r1TBlK9v7O7L5RRn1Z/H7TuhycPOLeuqFY0qcVwb61CikVmYsY2ik3+T3KS19eAq
X5zIIoMWolVVor5YcjJbBmpBnrUzRGEGbHgf8OhgUObsdvuBAkEA3mlp5wSS8gE6
vxUdOSDpCg/lsyqQcYqqXeSYUHjWal1SBt80q1fP+IFxwynglEd1fs3QYiGadyMx
XoMckY1kUQJBAN3GjXCwiCuKnQb4ISyKrrKADDlnssC+Xd1KlJ5J19V8G8D/GoLt
08HIPzn7gXPUbCYlKCcFYuxrMi2++uN4rVsCQB4pvasO+77GW+k+O9Bbnj83GLfL
tfswCxrgvadsO+gA7/bunn3+Ur4pD/yf2U1Cw7SGxRQJ6qDtrOxca6txuYECQEMl
8f9Tw/cDAiZxDIJS/zAWqxzac/n96aeuBC+lBg+igzi9RMatwbbjrZkduIVcpN4r
0+t0qw4QKuJE9+vVxw8CQHzVLfEEhH315hYayQ+IkTgNfwDLi48rl0g8gVClcbno
wuUk/LqWwWkCcLWJ6WrOEUvmfQmzGUgxk/4xiPm4Lbg=
-----END RSA PRIVATE KEY-----";

        $this->publicKey = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDArYDWTnG9zGQn/6YqJnNRAuUC
V7GV5SoEgkk0rafBITBhexDYKRWAsprTsbzsJsVjC9llv75aPMHSx9qPEu9u9wNr
WjI5ij/32SWDDsSQaWPjCiDLuiGqWauNH+U64Ffwv1lzcffsxg3LJ/mwiP51bfJE
aohVH6m+bJjZIwVlywIDAQAB
-----END PUBLIC KEY-----";

    }

    public function index(){

        echo "***************公钥加密\私钥解密：***************";
        $content = "我是加密内容12345789DF4A8S9FD49A84E98WT4W4EG65ASGDS我是加密内容";
        echo "<br/>****明文： <br/>";
        dump($content);

        try{
        $encrypt = RSAService::encryptWithPublicKey($content,$this->publicKey);
        echo "<br/>****加密后： <br/>";
        dump($encrypt);

        $decrypt = RSAService::decryptWithPrivateKey($encrypt,$this->privateKey);

        echo "<br/>****解密后： <br/>";
        dump($decrypt);
        echo "***************私钥加密\公钥解密：***************";

        echo "<br/>****明文： <br/>";
        dump($content);
        $encrypt = RSAService::encryptWithPrivateKey($content,$this->privateKey);
        echo "<br/>****加密后： <br/>";
        dump($encrypt);

        $decrypt = RSAService::decryptWithPublicKey($encrypt,$this->publicKey);

        echo "<br/>****解密后： <br/>";
        dump($decrypt);
        }catch (\Exception $e){
            echo $e->getMessage();
        }
        exit();
    }

}