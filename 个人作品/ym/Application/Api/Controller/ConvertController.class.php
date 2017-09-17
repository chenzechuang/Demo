<?php

namespace Api\Controller;

use Think\Controller;

class ConvertController extends Controller {

    
    public function index() {
//        var_dump(I('post.aid'));
//        var_dump(urlencode(I('post.url')));
//        exit();
        if (IS_POST && isset($_POST['aid']) && isset($_POST['url']) && isset($_POST['callback'])) {
            system("py/convertsh.sh ".I('post.aid')." ".I('post.url')." ".I('post.callback')." $s > /dev/null 2>&1 &", $ret);
//            system("py/convertsh.sh ".I('post.aid')." ".urlencode(I('post.url'))." ".urlencode(I('post.callback'))." $s > /dev/null 2>&1 &", $ret);
            echo json_encode(array('error'=>0,'msg'=>'转换中,等待回调'));
        }else{
            echo json_encode(array('error'=>1,'msg'=>'缺少参数'));
        }
    }
    public function callback() {
        var_dump($_REQUEST);
    }
}
