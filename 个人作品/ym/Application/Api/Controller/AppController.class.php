<?php

namespace Api\Controller;

use Think\Controller;

class AppController extends Controller {

    const API_URL = 'http://app.yuemai168.com/';
    const APITOKEN = 'ymisthebest'; //验证api接口的 token

    public function index() {
//        echo md5(base64_encode('identityuserid150112135741cccac3bad8b5cb7c192bc2de68758b'));
    }
    public function appEdition() {
        $SappEdition = S("AppEdition");
        if (empty($SappEdition)) {
            $config = D("Api/Config");
            $data = $config->getConfig("14");
            $SappEdition = S("AppEdition", $data, 3600);
            echo $data;
        } else {
            echo $SappEdition;
        }
    }

    public function encryptionAPI($array = null, $token = null, $timestamp = null) {
        if($_SERVER['HTTP_USERID']){ 
            $userid = "userid".$_SERVER['HTTP_USERID'];
            if(!S($userid)){   
                    S($userid,$_SERVER['HTTP_USERID'],3600);
                    M('users')->where(array('id'=>$_SERVER['HTTP_USERID']))->save(array('logintime'=> time()));
                   //echo  M('users')->getLastSql();
                }
        }
        if (isMobile() == FALSE) {
            return TRUE;
        }
        sort($array);
        $str = implode('', $array);
        $tokenSQL = M('token', 'center_')->field('token')->where(array('uid' => $_SERVER['HTTP_USERID']))->cache('key' . $_SERVER['HTTP_USERID'], 1)->find();
        if (md5(base64_encode($str . $timestamp . $tokenSQL['token'])) == $token) {
            return TRUE;
        } else {
            \Think\Log::write('未加密:'.$str . $timestamp . $tokenSQL['token'] . ' 头信息id:' . $_SERVER['HTTP_USERID'] . ' 服务器加密token:' . md5(base64_encode($str . $timestamp . $tokenSQL['token'])) . " 小龙给的token:" . $token);
            $ip = $_SERVER["REMOTE_ADDR"];
            M('syslogs', 'center_')->add(array('msg' => $ip . '验证失败'));
            exit(json_encode(array('state' => 999, 'msg' => '验证失败')));
        }
    }

    public function token($userid) {
        //token
        $tokenStr = md5($userid . time());
        $token = M('token', 'center_')->where(array('uid' => $userid))->find();
        if (!$token) {
            $token = M('token', 'center_')->add(array('uid' => $userid, 'token' => $tokenStr));
        } else {
            M('token', 'center_')->where(array('uid' => $userid))->save(array('token' => $tokenStr));
        }
        return $tokenStr;
    }

}
