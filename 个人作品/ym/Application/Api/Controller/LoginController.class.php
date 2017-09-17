<?php

namespace Api\Controller;

use Think\Controller;
use Think\WxpayApp\lib\WxPayConfig;
use Think\WxpayApp\lib\WxPayApiApp;
use Think\WxpayApp\lib\Log;
use Think\WxpayApp\lib\CLogFileHandler;

//use Api\Model\Users;
//require_once 'Public/vendor/autoload.php';
//use Qiniu\Auth;
//use Qiniu\Storage\UploadManager;
header("Content-Type: text/html; charset=UTF-8");
Vendor('WxpayApp.WxPayJsApiPay');

class LoginController extends Controller {

//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const API_URL_CALLBACK = 'http://www.kmic168.com/';

    Public function _initialize() {
        
    }

    public function index() {
        
    }

    public function captcha() {
        $controller = A('Api/App');
        $controller->encryptionAPI(array('phonenum'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Login');
        $phonenum =preg_replace('/[ ]/', '', I('post.phonenum'));
        echo $model->captcha($phonenum);
    }

    public function login() {
//        echo base64_encode('userid149853233434ee39212927b3f8567ea7c82e5430e8');
//        $controller = A('Api/App');
//        $controller->encryptionAPI(array('phonenum', 'phone_captcha'), I('post.token'), I('post.timestamp'));
        \Think\Log::write("手机登录".json_encode(I('post.')));
        $model = D('Api/Login');
        $phonenum =preg_replace('/[ ]/', '', I('post.phonenum'));
        echo $model->verify($phonenum, I('post.phone_captcha'));
    }

    public function wxlogin() {
        \Think\Log::write("微信登录".json_encode(I('post.')));
        $controller = A('Api/App');
        $controller->encryptionAPI(array('code'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Login');
        echo $model->loginWx(I('post.code'));
    }

}
