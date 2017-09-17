<?php
namespace Api\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");

class InitController extends Controller {
    
    public function config(){
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Config');
        echo $model->getAppConfig();  //        
    }
    
    public function commodity(){
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Config');
        echo $model->commodity();  //        
    }
    
    public function exchangelist() {  //兑换列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Config');
        echo $model->exchangelist(I('post.userid'),I('post.exchange_id'),I('post.phone_captcha'));  //更新用户信息
    }    
    
  
    public function getGift() { //礼物列表 
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Config');
        echo  $model->getGift(); 
    }

}
