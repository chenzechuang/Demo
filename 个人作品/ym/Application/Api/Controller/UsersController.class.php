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
class UsersController extends Controller {
//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const API_URL_CALLBACK          =   'http://www.kmic168.com/';
    

    Public function _initialize()  
    {

    }
    public function modify() {
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->upDataInfo(I('post.'));  //更新用户信息
    }

    public function items() {  //麦币与金币
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->getCurrency(I('post.userid'));  //更新用户信息
    }   
    
    public function getbill() {  //用户麦币账单
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->getBill(I('post.userid'),I('post.range'));  //更新用户信息
    }     
    
    public function coinbill() {  //用户金币账单
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->getcoinbill(I('post.userid'),I('post.range'));  //更新用户信息
    }
    
    public function exchange() {  //提现
        \Think\Log::write(json_encode(I('post.'))."提现POST");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','exchange_id','zfb_id','phone_captcha'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->exchange(I('post.userid'),I('post.exchange_id'),I('post.zfb_id'),I('post.phone_captcha'));  //更新用户信息
    }
    
    public function exchangelist() {  //兑换列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->exchangelist(I('post.userid'),I('post.range'));  //更新用户信息
    }
    
    public function mzauth() {  //提交麦主申请
        \Think\Log::write(json_encode(I('post.')));
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','real_name','introduction','phone_num','phone_captcha','wx_id','voice_ids','pics','voice_url','voice_time'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->mzauth(I('post.'));  //
    }
    
    public function simple() {  //获取用户信息
//        var_dump($_SERVER["HTTP_USERID"]);
//        \Think\Log::write('HTTP_USERID:' . $_SERVER["HTTP_USERID"]);
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->userInfoSimple(I('post.userid'));  //
    }    

    public function batch() {  //获取用户信息
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userids'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->userInfoBatch(I('post.userids'));  //
    }    
    
    public function followAdd() {  //关注
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','mcid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->followAdd(I('post.userid'),I('post.mcid'));  //
    }   
    
    public function followDel() {  //取消关注
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','mcid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->followDel(I('post.userid'),I('post.mcid'));  //
    }

    public function attentionlist() {  //关注列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->attentionlist(I('post.userid'),I('post.range'));  //
    }   
    
    public function beattentionlist() {  //被关注列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->beattentionlist(I('post.userid'),I('post.range'));  //
    }    
    
    public function attentioncount() { //获取用户关注数和被关注数
        \Think\Log::write(json_encode(I('post.'))."attentioncount--Post");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->attentioncount(I('post.userid'));         
    }
    
    public function bdlist() { //榜单
        $controller = A('Api/App');
        $controller->encryptionAPI(array('type'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Users');
        echo $model->bdlist(I('post.type'));
    }
    
}

