<?php
namespace Think;

class Http {
    /**
 * 发送HTTP请求方法
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
    public function http($url, $params, $method = 'POST', $header = array(), $multi = false){
        $opts = array(
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        
        return  $data;
    }

    public function httpGet($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_TIMEOUT,60);   //只需要设置一个秒的数量就可以

        curl_setopt($oCurl, CURLOPT_URL, $url);

        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );

        $sContent = curl_exec($oCurl);

        $aStatus = curl_getinfo($oCurl);

        curl_close($oCurl);

        if(intval($aStatus["http_code"])==200){
            return  $sContent;
        }else{
             return false;
        }
            // 将获取到的access_token作为文本信息返回
    }
    
    public function httpGetDownload($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_TIMEOUT,60);   //只需要设置一个秒的数量就可以

        curl_setopt($oCurl, CURLOPT_URL, $url);

        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );

        $sContent = curl_exec($oCurl);

        $aStatus = curl_getinfo($oCurl);

        curl_close($oCurl);

        if(intval($aStatus["http_code"])==200){
            return array_merge(array('body'=>$sContent),array('header'=>$aStatus));
        }else{
            return false;
        }
            // 将获取到的access_token作为文本信息返回
    }
    
    public function httpPost($url, $jsonData){
//        echo $jsonData;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);
        return $tmpInfo;
    }
}
