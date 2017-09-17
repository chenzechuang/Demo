<?php

function strToHex($string)//字符串转十六进制
{ 
    $hex="";
    for($i=0;$i<strlen($string);$i++)
    $hex.=dechex(ord($string[$i]));
    $hex=strtoupper($hex);
    return $hex;
}   
 
function hexToStr($hex)//十六进制转字符串
{   
    $string=""; 
    for($i=0;$i<strlen($hex)-1;$i+=2)
    $string.=chr(hexdec($hex[$i].$hex[$i+1]));
    return  $string;
}

function authData($action,$data=array(),$url){
//    echo $url;
        $http = new \Think\Http();
        $AES = new \Think\AES();
        $url = $url;
        switch ($action) {
            case 1:
                $url = $url;
                break;
            default:
                break;
        }
        $result = $http->http($url, array('data'=>$AES->encode(json_encode($data))));
        $arr = json_decode($AES->decode($result));
//        var_dump($arr->data->wx_info);
        
        if ($arr->code==0) {
            if ($arr->data->time + 300 > time()&&$arr->data->time - 300 < time()) {
//                echo '时间之内';
//                print_r(json_decode($arr->data->wx_info));
                return array('code'=>'0','data'=>json_decode(stripslashes($arr->data->wx_info)),'ismob'=>$arr->data->ismob,'isavailable'=>$arr->data->isavailable);
            }else{
                return array('code'=>'timeout');
            }
        }else{
            return array('code'=>$arr->code);
        }
//        print_r();
}
    
function decryptData($data){
    $data=json_decode($data);
    if ($data->time + 300 > time()&&$data->time - 300 < time()) {
        return $data;
    }else{
        return FALSE;
    }
}

function object_array($array) {  
    if(is_object($array)) {  
        $array = (array)$array;  
     } if(is_array($array)) {  
         foreach($array as $key=>$value) {  
             $array[$key] = object_array($value);  
             }  
     }  
     return $array;  
} 


function downloadWeixinFile($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $package = curl_exec($ch);
    $httpinfo = curl_getinfo($ch);
    curl_close($ch);
    $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package));
    return $imageAll;
}
//文件格式
function header_byte($type){
    switch ($type){
        case 'audio/amr':
            $tp = "amr";
            break;

//其他文件格式自行添加

        default:
            $tp = "notype";
            break;

    }
    return $tp;
}
//存到自己服务器
function saveWeixinFile($filename, $filecontent){
    $local_file = fopen($filename, 'w');
    if (false !== $local_file){
        if (false !== fwrite($local_file, $filecontent)) {
            fclose($local_file);
        }
    }
}

function checkWX(){
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']); 
    if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){ 
        exit("非微信禁止访问"); 
    }
}

function isWX(){
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']); 
    if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){ 
        return FALSE;
    }else{
        return TRUE;
    }
}

function DeleteHtml($str) 
{ 
    $str = trim($str); 
    $str = strip_tags($str,""); 
    $str = ereg_replace("\t","",$str); 
    $str = ereg_replace("\r\n","",$str); 
    $str = ereg_replace("\r","",$str); 
    $str = ereg_replace("\n","",$str); 
    $str = ereg_replace(" "," ",$str); 
    return trim($str); 
}


?>