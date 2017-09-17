<?php

function strToHex($string) {//字符串转十六进制
    $hex = "";
    for ($i = 0; $i < strlen($string); $i++)
        $hex .= dechex(ord($string[$i]));
    $hex = strtoupper($hex);
    return $hex;
}

function hexToStr($hex) {//十六进制转字符串
    $string = "";
    for ($i = 0; $i < strlen($hex) - 1; $i += 2)
        $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    return $string;
}

function authData($action, $data = array(), $url) {
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
    $result = $http->http($url, array('data' => $AES->encode(json_encode($data))));
    $arr = json_decode($AES->decode($result));

    if ($arr->code == 0) {
        if ($arr->data->time + 300 > time() && $arr->data->time - 300 < time()) {
//                echo '时间之内';
            return array('code' => '0', 'ismob' => $arr->data->ismob, 'isavailable' => $arr->data->isavailable);
        } else {
            return array('code' => 'timeout');
        }
    } else {
        return array('code' => $arr->code);
    }
//        print_r();
}

function decryptData($data) {
    $data = json_decode($data);
    if ($data->time + 300 > time() && $data->time - 300 < time()) {
        return $data;
    } else {
        return FALSE;
    }
}

function object_array($array) {
    if (is_object($array)) {
        $array = (array) $array;
    } if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

function downloadWeixinFile($url) {
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
function header_byte($type) {
    switch ($type) {
        case 'audio/x-speex-with-header-byte; rate=16000':
            $tp = "speex";
            break;

//其他文件格式自行添加

        default:
            $tp = "notype";
            break;
    }
    return $tp;
}

//存到自己服务器
function saveWeixinFile($filename, $filecontent) {
    $local_file = fopen($filename, 'w');
    if (false !== $local_file) {
        if (false !== fwrite($local_file, $filecontent)) {
            fclose($local_file);
        }
    }
}

function checkWX() {
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
        exit("非微信禁止访问");
    }
}

function isWX() {
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
        return FALSE;
    } else {
        return TRUE;
    }
}

function xml_to_array($xml) {
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        $arr = array();
        for ($i = 0; $i < $count; $i++) {
            $key = $matches[1][$i];
            $val = xml_to_array($matches[2][$i]);  // 递归
            if (array_key_exists($key, $arr)) {
                if (is_array($arr[$key])) {
                    if (!array_key_exists(0, $arr[$key])) {
                        $arr[$key] = array($arr[$key]);
                    }
                } else {
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $val;
            } else {
                $arr[$key] = $val;
            }
        }
        return $arr;
    } else {
        return $xml;
    }
}

function findConfWithKey($key) {
    $conf = M('config')->where('`key` = \'' . $key . '\'')->find();
    return $conf['value'];
}

function isMobile() {
    
    if (strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') != false ) {
        return false;
    } 
    
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|.∗?|', $useragent, $matches) > 0 ? $matches[0] : '';

    function CheckSubstrs($substrs, $text) {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr)) {
                return true;
            }
        return false;
    }

    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
            CheckSubstrs($mobile_token_list, $useragent);

    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}
function wxSign() {
    session('timestamp', time());
    session('nonceStr', createNonceStr());
//        echo S('jsapi_ticket')->ticket;
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $string = "jsapi_ticket=" . S('jsapi_ticket')->ticket . "&noncestr=" . session('nonceStr') . "&timestamp=" . session('timestamp') . "&url=" . $url;
//        exit(); 
    return sha1($string);
}

function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}
?>