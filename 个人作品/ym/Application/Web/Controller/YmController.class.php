<?php

namespace Web\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

//require_once 'Public/vendor/autoload.php';
//require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";


class YmController extends Controller {

    public function index() {
        if (IS_AJAX) {
            $start = I('get.start');
            $user_data = M('users_mcs')
                    ->limit($start,10)
                    ->order('sort desc')
                    ->select();

            foreach ($user_data as $key => $value) {
                $tag = json_decode($value["tag"]);
                $tagXqArry = explode(",", $tag->xq);
                $user_data[$key]['xq'] = $tagXqArry;
            }
            $json_data['lists'] = $user_data;
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

    public function article() {
        if (IS_AJAX) {
            $uid = I('get.uid');
            $start = I('get.start');
            $where['ywx_users_mcs.id'] = $uid;
            $aritcle_data = M('aritcle')->join('right join ywx_users_mcs ON ywx_users_mcs.id = ywx_aritcle.uid')
                    ->field('ywx_aritcle.*,ywx_users_mcs.name,name_user,ywx_users_mcs.headimgurl,ywx_users_mcs.headimgurl_user,ywx_users_mcs.audio,ywx_users_mcs.photo_album,ywx_users_mcs.fans_row,ywx_users_mcs.article_row,ywx_users_mcs.follow_row,ywx_users_mcs.tag,ywx_users_mcs.introduction')
                    ->where($where)
                    ->limit($start,10)
                    ->select();
            foreach ($aritcle_data as $key => $value) {
                $tag = json_decode($value["tag"]);
                $tagSyArry = explode(",", $tag->sy);
                $tagXqArry = explode(",", $tag->xq);
                $aritcle_data[$key]['xq'] = $tagXqArry;
                $aritcle_data[$key]['sy'] = $tagSyArry;       

            }
            $json_data["error"] = "0";
            $json_data['lists'] = $aritcle_data;
            $json_data["follow"] = M('follow')->where(array('uid' => I('get.openid'), 'mcs_id' => I('get.uid')))->count();
            echo json_encode($json_data);
            exit();

            $json_data['user'] = $user_data;
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

    public function praise() {
        if (IS_AJAX) {
            $start = I('get.start');
            $uid = I('get.openid');
            $where['ywx_users_mcs.id'] = array('egt', $start);
            $user_data = M('users_mcs') ->where($where)->order('sort desc')->limit(10)->select();
            

                        
            foreach ($user_data as $key => $value) {
                
                $condition['ywx_like.mcs_id'] = $value['id'];
                $condition['_string'] = '(ywx_users.headimgurl IS NOT NULL )  OR ( ywx_users.headimgurl_user IS NOT NULL)';
            
                $user_data[$key]['praiseer_type'] = M('like')->where(array('uid'=>$uid,'mcs_id'=>$value['id']))->count();
                $user_data[$key]['praiseer']= M('like')
                        ->join('ywx_users ON ywx_users.id = ywx_like.uid')
                        ->field('ywx_users.headimgurl,ywx_users.headimgurl_user')
                        ->where($condition)->order('ywx_like.id desc')->limit(3)->select();
            }
          
            $json_data['error'] = '0';
            $json_data['list'] = $user_data;
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
    
    public function praiseList() {
        if (IS_AJAX) {
            $mcs_id = I('get.mcsId');
            $like_dada["data"] = M('like')
                    ->join('left join  ywx_users ON ywx_users.id = ywx_like.uid')
                    ->field('ywx_users.name,ywx_users.name_user,ywx_users.headimgurl,ywx_users.headimgurl_user')
                    ->where(array('mcs_id'=>$mcs_id))->limit(3)->select();
            $like_dada["error"] = "0"; 
            echo json_encode($like_dada);
            exit();
        }
    }    
    
    public function like() {
        if (IS_AJAX) {
            $mcs_id = I('get.mcsId');
            $uid = I('get.openid');
            $likeWhere["mcs_id"] = $mcs_id;
            $likeWhere["uid"] = $uid;
            $row = M('like')->where($likeWhere)->count();
            if ($row > 0) {
              //  M('like')->where($likeWhere)->delete();
              //  M('users_mcs')->where(array('id' => $mcs_id))->setDec('like_row', 1);
                $data_row['row'] = "0";
                echo json_encode($data_row);
                exit();
            } else {
                $likeWhere["log_time"] = date("Y-m-d H:i:s");
                $likeId = M('like')->add($likeWhere);
                if ($likeId) {
                    M('users_mcs')->where(array('id' => $mcs_id))->setInc('like_row', 1);
                    $data_row['row'] = "1";
                    echo json_encode($data_row);
                    exit();
                }
            }
        }
    }
}
