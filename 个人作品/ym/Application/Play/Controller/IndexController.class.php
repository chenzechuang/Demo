<?php

namespace Play\Controller;

use Think\Controller;

require_once 'Public/vendor/autoload.php';
header("Content-Type: text/html; charset=UTF-8");

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;

class IndexController extends Controller {

    const WEBSITE_URL = 'ym.yuemai168.com';
    const RESOURCE_RUL = 'http://resource.kmic168.com/';
    const WEB_URL = 'http://ym.yuemai168.com/?m=Play&c=Index&a=';

    /*
      const WEBSITE_URL = '1c7240m284.iok.la';
      const RESOURCE_RUL = 'http://resource.kmic168.com/';
      const WEB_URL = 'http://1c7240m284.iok.la/?m=Play&c=Index&a=';
     */

    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';

    public function pub() {
        $post = I('post.');
        if ($post["action"] == "getTheme") {
            $themeData = new \Play\Logic\Theme();
            if($post["theme"]){
                $theme = $post["theme"];
            }
            $themeList = $themeData->ThemeListCache($theme);
            if ($themeList) {
                $out["code"] = '0';
                $out["data"] = $themeList;
            } else {
                $out["code"] = '1';
            }
            
            if(I('get.openid')){
                $user = new \Play\Model\UsersModel;
                $out["user_info"] = $user->findData(array('id'=>I('get.openid')));
            }
            
            echo json_encode($out);
            exit();
        }
   
        if ($post["action"] == "WxUpload") {
            
            $playMsg = new \Play\Model\PlayMsgModel;
            
            $addData["audio"] = $post['link'];
            $addData["confirm_id"] = $post['id'];
            $addData["pub"] = $post['pub'];
            $addData["theme"] = $post['theme'];
            $addData["son"] = $post['son'];
            $addData["create_time"] = $post['create_time'];
            $addData["audio_time"] = $post['audio_time'];
        
            $playMsgId = $playMsg->addData($addData);
            $post['id'] = $playMsgId;
            $asyncAction = new \Play\Logic\Qiniu();
            echo json_encode(array('code' => '0'));
            $upload = $asyncAction->asyncAction($post, self::WEBSITE_URL);
            exit();
//            if ($upload) {
//                $out["code"] = '0';
//            } else {
//                $out["code"] = '1';
//            }
//            echo json_encode($out);
//            直接写死

            
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

    public function qiniuUpload() {
        ignore_user_abort(true);
        set_time_limit(0);
//        $files = explode('[-]', I('post.link'));
//        $files = array_filter($files);
//        $fileNameArr = array();
//        \Think\Log::write("FILEHAHA=" . json_encode($files), "wxv");
//
//        foreach ($files as $value) {
//            $value = str_replace("&amp;amp;", "&", $value);
//            \Think\Log::write("adfdf=" . json_encode($value), "wxv");
//            $fileInfo = downloadWeixinFile($value);
//            $type = header_byte($fileInfo['header']['content_type']);
//            $filename = "record_" . time() . rand(1111, 9999) . ".amr";
////            $filename = "record_" . time() . rand(1111, 9999) . ".".$type;
//            saveWeixinFile("./Uploads/" . $filename, $fileInfo['body']);
//            $fileNameArr[] = $filename;
//        }
//        exit();
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传Token
//        \Think\Log::write("七牛" . json_encode($auth) . $fileNameArr[0], "xwToken");
        \Think\Log::write("七牛ARR" . json_encode(I('post.')), "xwToken");
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        $pipeline = 'kmicMedia';
        //
        $encodedUrlN = array();
        //
        $key = array();
        //如果只有一个就直接转mp3
        /////////正常使用  上传时 立刻转mp3
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/Uploads/" . I('post.localname');
        \Think\Log::write("七牛ARR" . $filePath);
        $pfop = "avthumb/mp3/ab/64k";

        //转码完成后回调到业务服务器。（公网可以访问，并相应200 OK） 
        $notifyUrl = self::WEB_URL . 'Notifi&playMsgId=' . I('post.id');

        //独立的转码队列：https://portal.qiniu.com/mps/cpipeline
        $pipeline = 'kmicMedia';

        $policy = array(
            'persistentOps' => $pfop,
            'persistentNotifyUrl' => $notifyUrl,
            'persistentPipeline' => $pipeline
        );
        $token = $auth->uploadToken($this->bucket, null, 3600, $policy);
        \Think\Log::write("七牛" . json_encode($token) . "hehe" . json_encode($policy), "wx");
        list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath);
        if ($err !== null) {
            
        } else if ($ret['key'] != '') {
            unlink($filePath);
            S($ret['key'], I('post.id'));
        }
        exit();
    }

    public function Notifi() {
        $notifyBody = file_get_contents('php://input');
        \Think\Log::write('Notifi:' . $notifyBody);
        $json = \Qiniu\json_decode($notifyBody);
        $getData = I('get.');
        \Think\Log::write('Notifi:' . json_encode($addData));
        if ($json->items[0]->key) {
            $addData = array();
            $addData["audio"] = self::RESOURCE_RUL . $json->items[0]->key;
            $playMsg = new \Play\Model\PlayMsgModel;
            $playMsg->saveData($getData['playMsgId'],$addData);
        }
    }

    function deleteQiNiuFile($inputKey) {
        //删 7牛的数据
        //初始化Auth状态：
        $auth = new Auth($this->accessKey, $this->secretKey);

        //初始化BucketManager
        $bucketMgr = new BucketManager($auth);

        //你要测试的空间， 并且这个key在你空间中存在
        $bucket = $this->bucket;
        $key = $inputKey;

        //删除$bucket 中的文件 $key
        $err = $bucketMgr->delete($bucket, $key);
        if ($err !== null) {
            var_dump($err);
        } else {
            echo "Success!";
        }
    }

    
    function checkAduio() {
        $getData = I('get.');
        $themeData = new \Play\Logic\Share();
        $data = $themeData->userData($getData);
        if (count($data) > 0) {
            if ($getData["son"] != 0) {
                $out["code"] = "0";
                echo json_encode($out);
                exit();
            }
            $centerCrl = A('Center/Index');
            $centerCrl->getJsapi_ticket();
            $this->assign('signature', wxSign());
            $this->assign('data', $data);
            $this->display('share');
        } else {
            echo "音频转码失败！";
        }
    }

    function themeMsg() {

        if (IS_AJAX) {
            $getData = I('get.');
            $msg = new \Play\Logic\Msg;
            $list = $msg->msgList($getData['openid'], $getData['msgid'], $getData['themeid'], $getData['page']);
            if (count($list) > 0) {
                $json["code"] = "0";
                $json["data"] = $list;
            } else {
                $json["code"] = "1";
            }
            echo json_encode($json);
            exit();
        }
        $user = new \Play\Model\UsersModel;
        $where['id']=I('get.openid');
        $userData = $user->findData($where);
       
        
        $centerCrl = A('Center/Index');
        if (!isset($_GET['openid'])) {
            $centerCrl->weixinGetOpenid();
            exit();
        }
        $centerCrl->getJsapi_ticket();
        $this->assign('data', $userData);
        $this->assign('signature', wxSign());
        $this->display();
    }

}
