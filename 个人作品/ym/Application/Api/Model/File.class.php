<?php

namespace Api\Model;

require_once 'Public/vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Think\Model;

class File {

    /**
     * 
     * @return type
     */
    const API_URL_CALLBACK = 'http://www.kmic168.com/';
    const RESOURCE_RUL = 'http://resource.kmic168.com/';

    // 用于签名的公钥和私钥
    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';

    public function file_upload($filePath, $uid) {

        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传Token
        $token = $auth->uploadToken($this->bucket);

        // $filePath = $_FILES['file'];
        // 上传到七牛后保存的文件名
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();

        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath['tmp_name']);

        if ($err !== null) {
            //  var_dump($err);
            return json_encode(array("state" => 1, 'msg' => '上传失败', 'data' => $err));
        } else {
            $picId = M('file')->add(array('url' => 'http://resource.kmic168.com/' . $ret['key'], 'uid' => $uid, 'type' => '0'));
            header('Content-type: application/json');
            $file["file_url"] = 'http://resource.kmic168.com/' . $ret['key'];
            $file["pic_id"] = $picId;
            return json_encode(array("state" => 0, 'msg' => '上传成功', 'data' => $file));
        }
    }

    public function url_upload($filePath) {

        // $filePath = dirname(__FILE__)."/../../../Uploads/".$value;
        // 上传到七牛后保存的文件名
        $auth = new Auth($this->accessKey, $this->secretKey);
        $token = $auth->uploadToken($this->bucket);
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, null, $filePath);
        if ($err !== null) {
            $data['state'] = "error";
            $data['msg'] = $err;
        } else if ($ret['key'] != '') {
            $data['state'] = "0";
            $data['data']["url"] = self::RESOURCE_RUL . $ret['key'];
        }
        return $data;
    }

    public function FileSelect($uid, $range) {
        if (!$uid) {
            return json_encode(array("state" => 1, 'msg' => 'no uid'));
        }
        if ($range) {
            $data = M('file')->field('id as fileID,uid,url,sort')->where(array('uid' => $uid))->limit(0 * $range, 15)->order('id desc')->select();
        } else {
            $data = M('file')->field('id as fileID,uid,url,sort')->where(array('uid' => $uid))->order('id desc')->select();
        }

        return json_encode(array("state" => 0, 'msg' => '获取成功', 'data' => $data));
    }

    public function deleteFile($fileID) {
        if (!$fileID) {
            return json_encode(array("state" => 1, 'msg' => 'no file'));
        }
        
        $data = M('file')->where(array('id'=>$fileID))->delete();
        if($data){
            return json_encode(array("state" => 0, 'msg' => '删除成功'));
        }else{
            return json_encode(array("state" => 1, 'msg' => '删除失败'));
        }
    }

}
