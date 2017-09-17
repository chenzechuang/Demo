<?php

namespace Api\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

class FileController extends Controller {

    public function up() {
        \Think\Log::write($_SERVER['HTTP_USERID'].'  +++++++ '. json_encode($_SERVER));
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid'), I('post.token'), I('post.timestamp'));
        $model = new \Api\Model\File;

        $filePath = $_FILES['file'];
//        \Think\Log::write(json_encode(I('post.')) . "上传文件" . json_encode($_FILES));
        echo $model->file_upload($filePath,$_SERVER['HTTP_USERID']);
    }
    
    public function FileSelect(){
        $post = I('post.');
        $controller = A('Api/App');
        $controller->encryptionAPI(array('uid'), I('post.token'), I('post.timestamp'));  
        $fileModel = new \Api\Model\File();
        echo $fileModel->FileSelect($post['uid'],$post['range']);
    }

    public function deleteFile() {
        $post = I('post.');
        $controller = A('Api/App');
        $controller->encryptionAPI(array('file_id'), I('post.token'), I('post.timestamp'));  
        $fileModel = new \Api\Model\File();
        echo $fileModel->deleteFile($post['file_id']);       
    }

}
