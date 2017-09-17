<?php
namespace Center\Controller;
use Think\Controller;


header("Content-Type: text/html; charset=UTF-8");


class LoginController extends Controller {


    public function index() {
        if (isset($_POST['action']) && I('post.action') == 'login') {
            $data = M('admin')->where(array('username' => I('post.username'), 'password' => sha1(md5(I('post.password')))))->find();
            if ($data) {
                session('id', $data['id']);
                session('jurisdiction', $data['jurisdiction']);
                exit(json_encode(array('error' => 0)));
            } else {
                exit(json_encode(array('error' => 1, 'msg' => '登录失败')));
            }
        }
        $this->display();
    }
    
    
    public function logout() {
        session('id',NULL);
        session('jurisdiction',NULL);
        $this->success('登出成功！');
    }    
}

