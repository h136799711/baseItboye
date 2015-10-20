<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/8/31
 * Time: 21:41
 */

namespace Api\Service;

class RSAService {

    public static function isSupportRSA(){
        if (!function_exists("openssl_pkey_get_private") || !function_exists("openssl_pkey_get_public")){
            exit("需要openssl支持!");
        }
    }

    /**
     * 通过私钥加密
     * @param $content 明文
     * @param $privateKey 私钥
     * @return string base64_encode后的密文
     * @throws \Exception
     */
    public static function  encryptWithPrivateKey($content,$privateKey){
        self::isSupportRSA();
        $pi_key =  openssl_pkey_get_private($privateKey);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        if(empty($pi_key)){
            throw new \Exception("私钥不可用!");
        }
        $encrypted = "";
        openssl_private_encrypt($content,$encrypted,$pi_key);//私钥加密
        return base64_encode($encrypted);
    }


    /**
     * 通过公钥加密内容
     * @param $content 明文
     * @param $publicKey  公钥
     * @return string base64_encode后的密文
     * @throws \Exception
     */
    public static function  encryptWithPublicKey($content,$publicKey){

        self::isSupportRSA();
        $pu_key = openssl_pkey_get_public($publicKey);//这个函数可用来判断公钥是否是可用的

        if(empty($pu_key)){
            throw new \Exception("公钥不可用!");
        }
        openssl_public_encrypt($content,$encrypted,$pu_key,OPENSSL_PKCS1_PADDING);

        return base64_encode($encrypted);
    }

    /**
     * 使用私钥解密
     * @param $content
     * @param $privateKey
     * @return string
     * @throws \Exception
     */
    public static function  decryptWithPrivateKey($content,$privateKey){

        self::isSupportRSA();
        $pi_key =  openssl_pkey_get_private($privateKey);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id

        if(empty($pi_key)){
            throw new \Exception("私钥不可用!");
        }
        $decrypted = "";
        openssl_private_decrypt(base64_decode($content),$decrypted,$pi_key,OPENSSL_PKCS1_PADDING);//私钥解密

        return $decrypted;
    }

    /**
     * 使用公钥解密
     * @param $content
     * @param $publicKey
     * @return string
     * @throws \Exception
     */
    public static function  decryptWithPublicKey($content,$publicKey){
        self::isSupportRSA();
        $pu_key = openssl_pkey_get_public($publicKey);//这个函数可用来判断公钥是否是可用的
        if(empty($pu_key)){
            throw new \Exception("公钥不可用!");
        }
        $decrypted = "";
        openssl_public_decrypt(base64_decode($content),$decrypted,$pu_key);
        return $decrypted;
    }

}