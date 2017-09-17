<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        \/?m=Play&c=index&a=qiniuUpload HTTP\/1.1\r\nHost: ym.yuemai168.com\r\nContent-type: application\/x-www-form-urlencoded\r\nConnection: Close\r\nContent-Length: 383\r\n\r\nlink=http%3A%2F%2Ffile.api.weixin.qq.com%2Fcgi-bin%2Fmedia%2Fget%3Faccess_token%3DWmjoDqZb6djbwGtAApp6raKNP3OJorvI8DfM00iNfxn3P0nQILRo1KD9rAs5c1u9jmLiBVHjCrqUForQV9hdQhcHc7XC_g-bcoxW_IIrV7MFxJfFvVh_o6eNmanqZzygXAUiAAAORL%26amp%3Bmedia_id%3D0ySU8v-zRzjedQFToM3ikBHwPy6mwG0Z5AdL3T_TYjkg2ejzwmUvAfqlUxew4gv8%5B-%5D&id=34&pub=1&theme=3&son=0&create_time=1497355399&audio_time=4%26quot%3B\r\n"

        <?php
        
        $data["link"]='http://www.baidu.com';
        $data["id"]='11';
        $data["pub"]='123';
        $data["theme"]='1';
        $data["son"]='123';
        $data["create_time"]='123';
        $data["audio_time"]='123';
        $post = http_build_query($data);
        $len = strlen($post);
        //发送
        $host = 'ym.yuemai168.com';
        $path = "/?m=Play&c=index&a=qiniuUpload";
        $fp = fsockopen( $host , 80, $errno, $errstr, 30);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            
            $out = "POST $path HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n";
            $out .= "\r\n";
            $out .= $post."\r\n";
            fwrite($fp, $out);

            
        fclose($fp);
        }
        exit();
        
        $url ='http%3A%2F%2Ffile.api.weixin.qq.com%2Fcgi-bin%2Fmedia%2Fget%3Faccess_token%3DWmjoDqZb6djbwGtAApp6raKNP3OJorvI8DfM00iNfxn3P0nQILRo1KD9rAs5c1u9jmLiBVHjCrqUForQV9hdQhcHc7XC_g-bcoxW_IIrV7MFxJfFvVh_o6eNmanqZzygXAUiAAAORL%26amp%3Bmedia_id%3D0ySU8v-zRzjedQFToM3ikBHwPy6mwG0Z5AdL3T_TYjkg2ejzwmUvAfqlUxew4gv8%5B-%5D';
        echo urldecode($url);
        exit();
       echo sprintf("%02d", 134%100);
        exit();
       $url = "http://127.0.0.1/ym/index.php?m=api&c=app&a=signup";
                $data["headimgurl"] = "headimgurl";
                $data["name"]       = "name";
                $data["sex"]             = "sex";
                $data["platform"]        = "WX";
                $data["province"]        = "province";
                $data["city"]            = "city";
                $data["openid"]          = "openid";
                $data["unionid"]         = "unionid";
                $data["info"]         = "info";
             $data["type"]  = "wx"; 
            
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        print_r($output);
            ?>
    </body>
</html>
