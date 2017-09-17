<?php

namespace Web\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

class userController extends Controller {

    public function index() {
        $openid = I('get.openid');
        $where['id'] = $openid;
        $user_data = M('users')
                ->where($where)
                ->find();
        //  $json_data['error'] = "0";
        //   $json_data['data'] = $user_data;
        //echo json_encode($json_data);

        if ($user_data["headimgurl_user"] != "") {
            $data["headimgurl"] = $user_data["headimgurl_user"];
        } else {
            $data["headimgurl"] = $user_data["headimgurl"];
        }

        if ($user_data["name_user"] != "") {
            $data["name"] = $user_data["name_user"];
        } else {
            $data["name"] = $user_data["name"];
        }
        $data["name"] = base64_decode($data["name"]);
        $this->assign("user", $data);
        $centerCrl = A('Center/Index');
        if (!isset($_GET['openid'])) {
            $centerCrl->weixinGetOpenid();
            exit();
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();

    }

    public function article() {
        $this->display();
    }

    public function reply() {
        if (IS_AJAX) {
            $type = I('get.type');
            $start = I('get.start');
            $uid = I('get.openid');
            if ($type == "follow") {
                $json_data["lists"] = M('follow')
                                ->join('ywx_users_mcs ON ywx_users_mcs.id = ywx_follow.mcs_id')
                                ->field('ywx_users_mcs.introduction,ywx_users_mcs.headimgurl,ywx_users_mcs.headimgurl_user,ywx_users_mcs.name_user,ywx_users_mcs.name,ywx_users_mcs.id,ywx_follow.log_time')
                                ->where(array('ywx_follow.uid' => $uid))
                                ->limit($start, 10)->select();
            } elseif ($type == "fans") {
                $json_data["lists"] = M('follow')
                                ->join('ywx_users_mcs ON ywx_users_mcs.id = ywx_follow.mcs_id')
                                ->field('ywx_users_mcs.introduction,ywx_users_mcs.headimgurl,ywx_users_mcs.headimgurl_user,ywx_users_mcs.name_user,ywx_users_mcs.name,ywx_users_mcs.id,ywx_follow.log_time')
                                ->where(array('ywx_follow.mcs_id' => $uid))
                                ->limit($start, 10)->select();
            }

            echo json_encode($json_data);
            exit();
        }
        $centerCrl = A('Center/Index');
        if (!isset($_GET['openid'])) {
            $centerCrl->weixinGetOpenid();
            exit();
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

}
