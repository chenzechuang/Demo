<?php

namespace Api\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

class chatController extends Controller {

    public function create() {
        \Think\Log::write("开启通话".json_encode(I('post.')));
        $controller = A('Api/App');
        $controller->encryptionAPI(array('channel_id', 'callee_uid', 'caller_uid', 'type'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Chat');
        echo $model->createRoom(I('post.channel_id'), I('post.callee_uid'),I('post.caller_uid'), I('post.type'));
    }

    public function heartbeat() {
        $controller = A('Api/App');
        $controller->encryptionAPI(array('channel_id', 'userid','identity'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Chat');
        echo $model->heartbeat(I('post.channel_id'), I('post.userid'), I('post.identity'));
    }

    public function close() {
        $controller = A('Api/App');
        $controller->encryptionAPI(array('channel_id'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Chat');
        echo $model->close(I('post.channel_id'));
    }
    public function checkRoomStatus() {
        ///
        $model = D('Api/Chat');
        echo $model->checkRoomStatus();
    }
}
