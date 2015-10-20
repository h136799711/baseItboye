<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/8/31
 * Time: 21:25
 */

if (!function_exists("mcrypt_encrypt")){
    exit("需要mcrypt支持!");
}


$private_key = '-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----';

$public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDArYDWTnG9zGQn/6YqJnNRAuUC
V7GV5SoEgkk0rafBITBhexDYKRWAsprTsbzsJsVjC9llv75aPMHSx9qPEu9u9wNr
WjI5ij/32SWDDsSQaWPjCiDLuiGqWauNH+U64Ffwv1lzcffsxg3LJ/mwiP51bfJE
aohVH6m+bJjZIwVlywIDAQAB
-----END PUBLIC KEY-----';

echo "-----------私钥加密\公钥解密-----------------------<BR/>";
$pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
$pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
print_r($pi_key);echo "\<BR\/\>";
print_r($pu_key);echo "\<BR/\>";


$data = "原始数据hebidu123大使56afd";//原始数据
$encrypted = "";
$decrypted = "";

echo "原始数据:",$data,"<BR/>";

echo "私钥加密后:<BR/>";

openssl_private_encrypt($data,$encrypted,$pi_key);//私钥加密
$encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
echo $encrypted,"<BR/>";

echo "公钥解密<BR/>";

openssl_public_decrypt(base64_decode($encrypted),$decrypted,$pu_key);//私钥加密的内容通过公钥可用解密出来
echo $decrypted,"<BR/>";

echo "-----------公钥加密\私钥解密-----------------------<BR/>";
echo "public key encrypt:<BR/>";

openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密
$encrypted = base64_encode($encrypted);
echo $encrypted,"<BR/>";

echo "私钥解密:<BR/>";
openssl_private_decrypt(base64_decode($encrypted),$decrypted,$pi_key);//私钥解密
echo $decrypted;