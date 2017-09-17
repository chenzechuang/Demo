<?php
namespace Api\Controller;
use Think\Controller;
use Think\WxpayApp\lib\WxPayConfig;
use Think\WxpayApp\lib\WxPayApiApp;
use Think\WxpayApp\lib\Log;
use Think\WxpayApp\lib\CLogFileHandler;

require_once 'Public/vendor/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
header("Content-Type: text/html; charset=UTF-8");
Vendor('WxpayApp.WxPayJsApiPay');
class WxController extends Controller {
        public function sendWxSMS(){
                $indexA = A('Center/Index');
                $http = new \Think\Http;
                echo $indexA->getToken(TRUE);
                $px = $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
                    "touser":"oph7UwFoEjKD4jzUtMDhUbBShihA",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"11111111"
                    }
                }');
                print_r($px);
     }
}
