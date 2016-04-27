<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 16/4/22
 * Time: 14:08
 */

namespace Api\Vendor\crypt;

/**
 * DES
 * Class DesCrypt
 * @package app\vendor\Crypt
 */
class DesCrypt{



    /**
     * 对明文信息进行加密
     * @param 内容|$content
     * @param 密钥|$key
     * @param string $salt
     * @return 内容
     */
    static public function encode($content,$key) {

        $td = \mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        $size=mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量

        $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
//        echo "<br/>".$ks;
        $key = substr($key,0,$ks);
//        echo "<br/>".$key;
        mcrypt_generic_init($td, $key, $iv); //初始处理
        //要保存到明文
        //加密
        $encode = mcrypt_generic($td, $content);
        //结束处理
        mcrypt_generic_deinit($td);
        return $encode;
    }

    /**
     * 对密文进行解密
     * @param $encode_content
     * @param $key string 密钥
     * @return string
     * @internal param $content
     */
    static public function decode($encode_content,$key) {

        $td = mcrypt_module_open(MCRYPT_DES,'','ecb',''); //使用MCRYPT_DES算法,ecb模式
        $size=mcrypt_enc_get_iv_size($td);//设置初始向量的大小
        $iv = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量

        $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）

        $key = substr($key,0,$ks);
        //初始解密处理
        mcrypt_generic_init($td, $key, $iv);
        //解密
        $decrypted = mdecrypt_generic($td, $encode_content);
        //解密后,可能会有后续的\0,需去掉
        $decrypted=trim($decrypted);
        //结束
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $decrypted;
    }


}