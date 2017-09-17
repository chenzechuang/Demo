<?php

namespace Web\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

class articleController extends Controller {
    public function index() {
        $centerCrl = A('Center/Index');
        if (IS_AJAX) {
            $data = M('aritcle_channel')->select();
            $json_data["data"] = $data;
            $json_data["error"] = "0";
            echo json_encode($json_data);
            exit();
        } else {
            if (!isset($_GET['openid'])) {

                $centerCrl->weixinGetOpenid();

                exit();
            }
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

    public function article() {
        $centerCrl = A('Center/Index');
        if (IS_AJAX) {
            $model = D('Web/Aritcle');
            echo $model->articleList(I('start'), I('get.channel'), I('get.openid'));
            exit();
        }
        if (!isset($_GET['openid'])) {
            $centerCrl->weixinGetOpenid();
            exit();
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

    public function articleContent() {
        $centerCrl = A('Center/Index');
        if (IS_AJAX) {
            $channel_data = M('aritcle_channel')->where(array('id' => I('get.channel')))->find();
            $aritcle_data = M('aritcle')->join('ywx_users_mcs ON ywx_users_mcs.id = ywx_aritcle.uid')
                    ->field('ywx_aritcle.*,ywx_users_mcs.name,name_user,ywx_users_mcs.headimgurl,ywx_users_mcs.headimgurl_user')
                    ->where(array('ywx_aritcle.id' => I('get.articleId')))
                    ->find();
            $aritcle_data["error"] = "0";
            $aritcle_data["follow"] = M('follow')->where(array('uid' => I('get.openid'), 'mcs_id' => I('get.mcsId')))->count();
            $aritcle_data["topImg"] = $channel_data["img_url"];

            echo json_encode($aritcle_data);
            exit();
        }
        if (!isset($_GET['openid'])) {
            $centerCrl->weixinGetOpenid();
            exit();
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

    public function articleMessageList() {
        if (IS_AJAX) {
            $aritcle_data["lists"] = M('message')->join('ywx_users ON ywx_message.uid = ywx_users.id')
                    ->field('ywx_message.content,ywx_message.log_time,ywx_users.name,ywx_users.name_user,ywx_users.headimgurl,ywx_users.headimgurl_user')
                    ->where(array('ywx_message.article_id' => I('get.articleId')))
                    ->limit(I('get.start'), 10)
                    ->order('ywx_message.id desc')
                    ->select();
            echo json_encode($aritcle_data);
            exit();
        }
    }

    public function articleFollow() {
        if (IS_AJAX) {
            $mcs_id = I('get.mcsId');
            $uid = I('get.openid');
            $followWhere["mcs_id"] = $mcs_id;
            $followWhere["uid"] = $uid;
            $row = M('follow')->where($followWhere)->count();
            if ($row > 0) {
                M('follow')->where($followWhere)->delete();
                M('users_mcs')->where(array('id' => $mcs_id))->setDec('follow_row', 1);
                $data_row['row'] = "0";
                echo json_encode($data_row);
                exit();
            } else {
                $followWhere["log_time"] = date("Y-m-d H:i:s");
                $followId = M('follow')->add($followWhere);
                if ($followId) {
                    M('users_mcs')->where(array('id' => $mcs_id))->setInc('follow_row', 1);
                    $data_row['row'] = "1";
                    echo json_encode($data_row);
                    exit();
                }
            }
        }
    }

    public function articlePraise() {
        if (IS_AJAX) {
            $aritcle_id = I('get.articleId');
            $uid = I('get.openid');
            $praiseWhere["aritcle_id"] = $aritcle_id;
            $praiseWhere["uid"] = $uid;
            $row = M('praise')->where($praiseWhere)->count();
            if ($row > 0) {
                $data_row['row'] = "0";
                echo json_encode($data_row);
                exit();
            } else {
                $praiseWhere["log_time"] = date("Y-m-d H:i:s");
                $fansId = M('praise')->add($praiseWhere);
                if ($fansId) {
                    M('aritcle')->where(array('id' => $aritcle_id))->setInc('praise', 1);
                    $data_row['row'] = "1";
                    echo json_encode($data_row);
                    exit();
                }
            }
        }
    }

    public function articleSendMessage() {
        if (IS_AJAX) {
            $add = I('post.');
            $add['log_time'] = date("Y-m-d H:i:s");
            $msgid = M('message')->add($add);
            if ($msgid) {
                M('aritcle')->where(array('id' => I('post.article_id')))->setInc('comments', 1);
                $user_data = M('users')->where(array('id' => I('post.uid')))->find();
                echo json_encode($user_data);
                exit();
            }
        }
    }

    public function articleListen() {
        if (IS_AJAX) {
            $add = I('post.');
            $add['log_time'] = date("Y-m-d H:i:s");
            M('aritcle')->where(array('id' => I('post.article_id')))->setInc('listen', 1);
            echo "1";
            exit();
        }
    }
    
    public function test() {
        $centerCrl = A('Center/Index');
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

}
