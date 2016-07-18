<?php

$url=$_GET['url'];

function curlGet($url,$data) {
    $url .= '?';
    foreach($data as $key=>$vo){
        $url .= $key.'='.$vo.'&';
    }
    $url = rtrim($url,'&');
    $ch = curl_init();
    $header = array('Accept-Charset'=>"utf-8","Content-type"=>"application/x-www-form-urlencoded");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.64 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    $error = curl_errno($ch);

    var_dump($tmpInfo);
    var_dump($error);

}


if($url){
    curlGet($url,array());
    exit;
}

/*检测并清除BOM*/
if(isset($_GET['dir'])){
    $basedir=$_GET['dir'];
}else{
    $basedir = '.';
}
$auto = 1;
checkdir($basedir);
function checkdir($basedir){
    if($dh = opendir($basedir)){
        while(($file = readdir($dh)) !== false){
            if($file != '.' && $file != '..'){
                if(!is_dir($basedir."/".$file)){
                    echo "filename: $basedir/$file ".checkBOM("$basedir/$file")." <br>";
                }else{
                    $dirname = $basedir."/".$file;
                    checkdir($dirname);
                }
            }
        }//end while
        closedir($dh);
    }//end if($dh
}//end function 
function checkBOM($filename){
    global $auto;
    $contents = file_get_contents($filename);
    $charset[1] = substr($contents, 0, 1);
    $charset[2] = substr($contents, 1, 1);
    $charset[3] = substr($contents, 2, 1);
    if(ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191){
        if($auto == 1){
            $rest = substr($contents, 3);
            rewrite ($filename, $rest);
            return "<font color=red>BOM found, automatically removed.</font>";
        }else{
            return ("<font color=red>BOM found.</font>");
        }
    }
    else return ("BOM Not Found.");
}//end function 
function rewrite($filename, $data){
    $filenum = fopen($filename, "w");
    flock($filenum, LOCK_EX);
    fwrite($filenum, $data);
    fclose($filenum);
}//end function 
