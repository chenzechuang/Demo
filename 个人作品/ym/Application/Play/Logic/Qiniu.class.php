<?php

namespace Play\Logic;

use Think\Model;

class Qiniu extends Model {

    /**
     * 
     * @return type
     */
    public function asyncAction($arr, $host) {
        \Think\Log::write("python py/qnhttp.py " . urlencode($arr['link']) . " " . $arr['id'] . " " . $arr['pub'] . " " . $arr['theme'] . " " . $arr['son'] . " " . $arr['create_time'] . " " . $arr['audio_time'] . " >> py/qnhttp.txt 2>&1");
        $cmd = system("python py/qnhttp.py " . urlencode($arr['link']) . " " . $arr['id'] . " " . $arr['pub'] . " " . $arr['theme'] . " " . $arr['son'] . " " . $arr['create_time'] . " " . $arr['audio_time'] . " >> py/qnhttp.txt 2>&1", $ret);
        //改成用python 来搞了
        exit();
        \Think\Log::write(json_encode($arr));
        $data = array();
        $data["link"] = $arr['link'];
        $data["id"] = $arr['id'];
        $data["pub"] = $arr['pub'];
        $data["theme"] = $arr['theme'];
        $data["son"] = $arr['son'];
        $data["create_time"] = $arr['create_time'];
        $data["audio_time"] = $arr['audio_time'];
        $post = http_build_query($data);
        $len = strlen($post);
        //发送
        $path = "/?m=Play&c=index&a=qiniuUpload";
        $fp = fsockopen($host, 80, $errno, $errstr, 30);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {

            $out = "POST $path HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n";
            $out .= "\r\n";
            $out .= $post . "\r\n";
            fwrite($fp, $out);


            fclose($fp);
        }
        \Think\Log::write("访问上传" . $errstr . $errno . "返回信息" . json_encode($out) . "FP返回:" . json_encode($fp));
        return $out;
        //exit(json_encode(array("error"=>0)));
    }

}
