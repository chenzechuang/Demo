<?php

namespace Center\Controller;

use Think\Controller;

header("Content-Type: text/html; charset=UTF-8");
use \Think\WxpayApp\lib\WxPayConfig;
use \Think\WxpayApp\lib\WxPayApiApp;
require_once 'Public/vendor/autoload.php';

header("Content-Type: text/html; charset=UTF-8");
Vendor('WxpayApp.WxPayJsApiPay');
//require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";

class AdminController extends Controller {

    Public function _initialize() {
        if(I('get.api') != "1"){
            if (session('id') == "") {
                $this->redirect('Login/index');
            } else {
                $jurisdiction = session('jurisdiction');
                $jurisdictionData = explode(",", $jurisdiction);
                $jurisdictionData = array_filter($jurisdictionData);  //拿出session 的权限 ID 把空值删除

                $menu = new \Center\Model\AdminMenuModel;
                $menu = $menu->selectALL();
                foreach ($menu as $key => $value) {
                    $data[] = $value['id'];
                }
                $data_diff = array_intersect($data, $jurisdictionData);  //拿出数据库 menu的ID 与用户的权限ID数组对比，拿出相同ID

                foreach ($menu as $m) {   //拿出相同ID的数据
                    foreach ($data_diff as $k => $value) {
                        if ($m['id'] == $value) {
                            if ($m['topid'] != "") {
                                $newMenu['son'][] = $m;
                            } else {
                                $newMenu['father'][] = $m;
                            }
                        }
                    }
                }
                foreach ($newMenu['son'] as $value) {
                    if (I('get.menuid') == $value['topid']) {
                        $sonMenu[] = $value;
                    }
                }

                $this->assign('menuFather', $newMenu['father']);
                $this->assign('menuSon', $sonMenu);
            }
        }
    }

    public function main() {

        $this->display();
    }

    /* 说 */

    public function aritcleChannel() {
        $this->display();
    }

    public function aritcleChannelListData() {
        $data = M('aritcle_channel')->order('id desc')->select();
        echo json_encode($data);
        exit();
    }

    public function aritcleChannelAdd() {

        if (IS_POST) {
            $POST = I('post.');
            if (isset($_FILES)) { //查看是否有上传图片
                $upfile = $this->upload();
                if ($upfile == 1) {  //上传出错
                    echo "Error";
                    exit();
                }

                $POST['img_url'] = "./Uploads/" . $upfile['pic']["savepath"] . $upfile['pic']["savename"];
                $POST['log_time'] = date('Y-m-d H:i:s');
            }
            M('aritcle_channel')->add($POST);
        }

        $this->display();
    }

    /* 情感 */

    public function aritcleList() {
        if (I('get.sort')) {  // 提升功能（修改排序）
            $id = I('get.sort'); //文章ID
            $ModelAritcleList = D('Center/Aritcle');
            $result = $ModelAritcleList->sort($id);
            if ($result == "1") {
                $this->success('提升成功！');
                exit();
            }
        }

        if (I('get.audio')) {  // 转换音频
            $id = I('get.audio'); //文章ID
            $ModelAritcleList = D('Center/Aritcle');
            $resultJson = $ModelAritcleList->audioT($id);
            \Think\Log::write("audio" . $resultJson);
            $result = json_decode($resultJson, TRUE);
            if ($result["data"]["error"] == "0") {
                $ModelAritcleList->updataData($id, array('transformation' => '2'));
                $this->success('成功提交转码操作！');
            } else if ($result["data"]["error"] == "2") {
                $this->success('还有音频还在转码！');
            } else if ($result["data"]["error"] == "3") {
                $this->success('没音频！');
            } else if ($result["data"]["error"] == "4") {
                $this->success('不是音频动态！');
            } else {
                echo $resultJson;
                // $this->success('提交转码操作失败！');
            }
            exit();
        }

        if (I('edition') != "") {
            $array = array('edition' => I('get.edition'));
            $Aritcle = D('Center/Aritcle'); //文章内容
            $Aritcle->updataData($AritcleId, $array);
            $this->success('更新成功！');
            exit();
        }

        if (I('get.edit')) { //修改
            $AritcleId = I('get.edit');
            $Aritcle = D('Center/Aritcle'); //文章内容
            $AritcleData = $Aritcle->acticleData($AritcleId);

            $Clannel = D('Center/AritcleChannel'); //栏目列表
            $ClannelData = $Clannel->selectALL();

            if (IS_POST) {
                $AritcleUpdata = $Aritcle->updataData($AritcleId, I('post.'));
                if ($AritcleUpdata) {
                    $this->success('更新成功！');
                    exit();
                }
            }

            $this->assign('Clannel', $ClannelData);
            $this->assign('data', $AritcleData);
            $this->display('aritcleEdit');
            exit();
        }

        $this->display();
    }

    public function aritcleAdd() {
        if (IS_POST) {

            $post = I('post.');
            $pics[0]["pic"] = $post["audio"];
            $pics[0]["voice_time"] = $post["voice_time"];
            $post["pics"] = json_encode($pics);
            $post["content"] = base64_encode($post["content"]);
            //     $post["audio"] = "./Uploads/".$upfile['audio']["savepath"].$upfile['audio']["savename"];
            $post["cover"] = $post["pic"];
            $post["log_time"] = date("Y-m-d H:i:s");
            $article_id = M('aritcle')->add($post);
            if ($article_id) {
                $where["id"] = $post["channel"];
                M('aritcle_channel')->where($where)->setInc('article_row', 1);
                M('users')->where(array('id' => $post["uid"]))->setInc('article_row', 1);
            }
        }
        $channel = M('aritcle_channel')->select();
        $this->assign("channel", $channel);
        $this->display();
    }

    public function aritcleListData() {
        $get = I('get.');
        $data = new \Center\Model\AritcleModel;
        $jsonData = $data->getActicleData($get);
        echo json_encode($jsonData);
        exit();
    }

    /* 会员 */

    public function user() {
        $this->display();
    }

    public function lock() {
        $uid = I('get.edit');
        $status = I('get.status');
        M('users')->where(array('id' => $uid))->save(array('locked' => $status));
        $this->success("操作成功！");
    }

    public function userAdd() {
        if (IS_POST) {
            $post = I('post.');

            if (isset($_FILES)) { //查看是否有上传图片
                $post['pics'] = '';
                $Filemodel = new \Api\Model\File;
                foreach ($_FILES as $fileKey => $value) {
                    if ($value["tmp_name"] != "") {
                        $fileJson = $Filemodel->file_upload($value);
                        $fileData = json_decode($fileJson, TRUE);
                        $pics[$fileKey] = $fileData["data"]["file_url"];
                        if ($fileKey == "headimgurl_user") {
                            $post['headimgurl_user'] = $pics[$fileKey];
                            unset($pics[$fileKey]);
                        } else {
                            $post['pics'][] = $fileData["data"]["file_url"];
                        }
                    }
                }
                $post['pics'] = json_encode($post['pics']);
            }

            $post['name_user'] = base64_encode($post['name_user']);
            $post['signature'] = base64_encode($post['signature']);
            $post['introduction'] = base64_encode($post['introduction']);

            $post['addtime'] = time();
            $post['logintime'] = time();

            M('users')->add($post);
        }
        $this->display();
    }

    public function userListData() {
        $get = I('get.');
        if ($get["sex"]) {
            $where["sex"] = $get["sex"];
        }
        if ($get["role_id"] != "") {
            $where["role_id"] = $get["role_id"];
        }
        if ($get["userid"] != "") {
            $where["ywx_users.id"] = $get["userid"];
        }

        $userModel = new \Center\Logic\User();
        $userListData = $userModel->userListData($where);


//            foreach ($userListData as $value) {
//                if($value["deposit"]==""){
//                    $a["deposit"] =0;
//                    $a["coin"] =0;
//                    $a["uid"] =$value["id"];
//                    M("user_bank")->add($a);
//                }
//            }
        if ($get["name"]) {

            foreach ($userListData as $k => $value) {
                if (strstr($value['name'], $get["name"])) {

                    $sarch[] = $value;
                }
            }
            unset($userListData);
            $userListData = $sarch;
        }
        echo json_encode($userListData);
        exit();
    }

    public function userUpdata() {
        $post = I('post.');
        $userid = session('id');
        $userLogic = new \Center\Logic\User();
        echo $userLogic->changeMz($userid, $post);
    }

    public function delete($table, $did) {
        $table = I('get.table');
        $did = I('get.did');

        if ($table == "aritcle") {
            $aritcleData = M($table)->where(array('id' => $did))->find();
            M("aritcle_channel")->where(array('id' => $aritcleData["channel"]))->setDec("article_row", 1);
        }
        M($table)->where(array('id' => $did))->delete();
        $this->success('删除成功！');
    }

    function upload() {
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 5145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'mp3', 'mp4'); // 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录 
        $upload->savePath = ''; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            echo $this->error($upload->getError());
            return 1;
        } else {
            return $info;
        }
    }

    public function audioCallback() {
        \Think\Log::write("callback_json". json_encode(I('post.')));
        if (IS_POST) {
            $post = I('post.');
            $user = new \Center\Model\AritcleModel;
            $user->updataData($post['aid'], array('transformation' => "1", 'audio_lite' => $post["lite"], "audio_full" => $post["full"]));
        }
    }

    public function logout() {
        session('login', FALSE);
        session('id', NULL);
        $this->redirect('index');
    }

    public function AppConfig() {
        $user = new \Center\Model\ConfigModel;
        $data = $user->getAppConfig();
        $post = I('post.');
        if (IS_POST) {
            $post = I('post.');
            $key = array_keys($post);
            $key = $key[0];
            $where["name"] = $key;
            $upData[$key] = $post[$key];
            $updata["val"] = $upData[$key];
            $user->UpdataAppConfig($updata, $where);
            $this->success("更新成功");
            exit();
        }
        foreach ($data as $key => $value) {
            $nData[$value["name"]] = $value["val"];
        }
        $this->assign("data", $nData);
        $this->display();
    }

    public function gift() {
        if (IS_AJAX) {
            $RechargeData = new \Center\Model\Recharge;
            $data = $RechargeData->Config(array('type' => '1'));
            echo json_encode($data);
            exit();
        }
        $this->display();
    }

    public function orderList() {
        $this->display();
    }

    public function orderListData() {
        $get = I('get.');
        $orderModel = new \Center\Logic\Order();
        $orderData = $orderModel->orderList($get);
        echo json_encode($orderData);
        exit();
    }

    public function upLoadQn() {
        if (isset($_FILES)) { //查看是否有上传图片
            $upfile = $this->upload();
            if ($upfile == 1) {  //上传出错
                echo "Error";
                exit();
            }
            $upImg = "./Uploads/" . $upfile['avatar_file']["savepath"] . $upfile['avatar_file']["savename"];
            $postImgInfo = json_decode($_POST['avatar_data'], TRUE);
            //$saveCatImg = "./Uploads/" . $upfile['avatar_file']["savepath"]."cat/".$upfile['avatar_file']["savename"];
            $image = new \Think\Image();
            $image->open($upImg);
            $catImg = $image->crop($postImgInfo["width"], $postImgInfo["height"], $postImgInfo["x"], $postImgInfo["y"])->save($upImg);

            $FileModel = new \Api\Model\File();
            $result = $FileModel->url_upload($upImg);
            echo json_encode($result);
        }
        exit();
    }

    public function setUserBank() {
        $post = I('post.');
        if (IS_AJAX) {
            $where["id"] = $post["userid"];
            $userModel = new \Center\Model\UsersModel();
            $data = $userModel->userFind($where);
            if (count($data) > 0) {
                $data["name"] = base64_decode($data["name"]);
            }

            echo json_encode($data);
            exit();
        }

        if (IS_POST) {
            $where["uid"] = $post["userid"];
            $array["coin"] = $post["coin"];
            $array["deposit"] = $post["deposit"];
            if ($post["deposit"]) {
                $usrFind["id"] = session('id');
                $usrFind["password"] = sha1(md5($post["pwd"]));
                $userModel = new \Center\Model\AdminModel();
                $userData = $userModel->findData($usrFind);
                if (count($userData) > 0) {
                    $userBankModel = new \Center\Model\UserBankModel();
                    $userBankModel->setIncBankData($where, $array);
                    $this->success("设置成功!");
                } else {
                    $this->success("密码出错!");
                }
            }

            exit();
        }

        $this->display();
    }

    public function Message() {
        if (IS_AJAX) {
            $pullMessageModel = new \Center\Model\PullMessageModel();
            if (I('post.action') == "addData") {
                $post = I('post.');
                $addData['uid'] = $post["userId"];
                $addData['content'] = $post["content"];
                $addData['log_time'] = date("Y-m-d H:i:s");

                $userModel = new \Center\Model\UsersModel();
                $userDate = $userModel->userFind(array('id' => $post['userId']));

                if (count($userDate) == 0) {
                    echo json_encode(array('state' => 1, 'data' => '没这个用户！'));
                    exit();
                }

                $apiCrl = A('Api/Push');
                $pullMessage = $apiCrl->pushMsg($post["userId"], $post["content"]); //userid , 内容  , 事件 ,参数
                $pullResult = json_decode($pullMessage, TRUE);

                if ($pullResult['status'] == 0) {
                    $pullMessageModel->adddData($addData);
                    echo json_encode(array('state' => 0, 'data' => '发送成功！'));
                } else {
                    echo json_encode(array('state' => 1, 'data' => '发送失败！'));
                }
                exit();
            }
            $pullMsessageData = $pullMessageModel->selectALL();
            echo json_encode($pullMsessageData);
            exit();
        }
        $this->display();
    }

    public function banner() {
        if (IS_AJAX) {
            $bannerModel = new \Center\Model\BannerModel();
            $bannerData = $bannerModel->selectALL();
            echo json_encode($bannerData);
            exit();
        }
        $this->display();
    }

    public function bannerADD() {
        if (IS_POST) {
            $post = I('post.');
            $bannerModel = new \Center\Model\BannerModel();
            $bannerModel->AddData($post);
            $this->success("添加成功！");
            exit();
        }
        $this->display();
    }

    public function praise() {
        if (IS_POST) {
            $post = I('post.');
            $articleModel = new \Center\Model\AritcleModel();
            $where["id"] = $post["articeID"];
            $row = rand(3, 10);
            $result = $articleModel->praise($where, $row);
            if ($result) {
                $listen_row = $row * 2;
                $articleModel->listen($where, $listen_row);
                $data["state"] = "0";
                $data["msg"] = "成功点赞" . $row . "次！";
            } else {
                $data["state"] = "1";
                $data["msg"] = "失败";
            }
            echo json_encode($data);
            exit();
        }
    }

    public function commentMessage() {
        $MessageModel = new \Center\Model\MessageModel();
        if (IS_POST) {
            $str = "";
            foreach (I('post.id') as $value) {
             $str .= ','.$value;
            }
            $idStr =  substr($str, 1);
            $where["id"] = array('in',$idStr);
            $MessageModel->deleteData($where);
            $this->success("删除成功！");
            exit();
        }
        if (IS_AJAX) {
            $data = $MessageModel->selectALL();
            foreach ($data as $key => $value) {
                $data[$key]["a_content"] = base64_decode($value["a_content"]);
                $data[$key]["content"] = base64_decode($value["content"]);
                $data[$key]["name"] = base64_decode($value["name"]);
            }
            echo json_encode($data);
            exit();
        }

        $this->display();
    }

    
    public function MzcommentMessage() {

        $MessageModel = new \Center\Model\MzCommentModel();

        if (IS_POST) {
            $str = "";
            foreach (I('post.id') as $value) {
             $str .= ','.$value;
            }
            $idStr =  substr($str, 1);
            $where["id"] = array('in',$idStr);
            $MessageModel->deleteData($where);
            $this->success("删除成功！");
            exit();
        }

        if (IS_AJAX) {
            $data = $MessageModel->selectALL();
            foreach ($data as $key => $value) {
                $data[$key]["comment_content"] = base64_decode($value["comment_content"]);
                $data[$key]["name"] = base64_decode($value["name"]);
                $data[$key]["comment_time"] = date("Y-m-d H:i:s",$value["comment_time"]);
                
            }
            echo json_encode($data);
            exit();
        }

        $this->display();
    }

    public function checkOrder() {
        $orderModel = new \Center\Logic\Order();
        $orderData = $orderModel->findData(array('id'=>I('post.id')));
       
     //   $order = $model->findLastOrderIDByUser(I('post.uid'));        
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($orderData['orderid']);
        $array = \WxPayApiApp::orderQuery($input);
        \Think\Log::write("ORDER".json_encode($array));
        if ($array['trade_state']=='SUCCESS'){
            //用户支付了
            $model = D('Api/Wx');
            $order = $model->saveMoney($array);
            $json_data["state"] = 0;
            $json_data['msg'] = '用户已付款！'; 

        }else{
            if($array["trade_state_desc"]){
               $orderModel->updataData(array('id'=>I('post.id')),array('desc'=>$array["trade_state_desc"],'paid'=>'2'));
            }
            $json_data["state"] = 0;
            $json_data['msg'] = $array["trade_state_desc"];            
        }
        echo json_encode($json_data);
        exit();
    }
    
    public function autioAudioT() {
        $ModelAritcleList = D('Center/Aritcle');
        $resultJson = $ModelAritcleList->audioT("-1");
        $data = json_decode($resultJson,TRUE);
        $ModelAritcleList->updataData($data["id"], array('transformation' => '2'));
        \Think\Log::write("audioPost 自动".json_encode($resultJson));
    }
    
    public function UserGiftList() {
      if(IS_AJAX){
        $userGiftModel =  new \Center\Model\UserGiftModel();
        $userGiftData = $userGiftModel->userGiftList();
        echo json_encode($userGiftData);
        exit();
      }
      $this->display();
    }
}
