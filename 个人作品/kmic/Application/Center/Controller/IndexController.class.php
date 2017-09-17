<?php

namespace Center\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";

use LeanCloud\Object;
use LeanCloud\User;
use LeanCloud\CloudException;
use LeanCloud\GeoPoint;
use LeanCloud\Client;
use LeanCloud\Relation;
use LeanCloud\Storage\SessionStorage;
use LeanCloud\Query;

header("Content-Type: text/html; charset=UTF-8");

class IndexController extends Controller {

//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const API_URL = 'http://www.kmic168.com/';
    const TOKEN = 'sadgk329euisjdlf23'; //验证wxapi接口的 token

    Public function _initialize() {
        \LeanCloud\Client::initialize("29f9bXMjtcmOkhtzRCWtVlgM-gzGzoHsz", "FweIIrWPbdjifiD0GwQSEMna", "H9Hn8jcQ7dD95iTlq5AAe46s");
    }

    public function index() {
//        $aes = new \Think\AES;
//        echo base64_encode($aes->encode('abcd123!@#/34504'));
//        print_r($_REQUEST);
//        $a = M('users','center_')->select();
//        var_dump($a);
        //没有wx_info 的
//        $wxinfoCheck = M('users')->where(array('wx_info'=>array('EXP','IS NULL')))->select();
//        echo count($wxinfoCheck);
//        foreach ($wxinfoCheck as $key => $value) {
//            $centerUserData = M('users','center_')->where(array('openid'=>$value['openid']))->find();
//            $json = json_decode(stripslashes($centerUserData['userinfo']));
////            var_dump($centerUserData);
//                    M('users')->where(array('id'=>$value['id']))->save(array('wx_info'=>$centerUserData['userinfo'],
//                    'name'=>$json->nickname,'sex'=>$json->sex,'addtime'=>time(),
//                        'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
//        }
//        echo 'done';
        //删了重复的user数据
//         set_time_limit(0);
//        $wxinfoCheck = M('users')->group('openid')->select();
//        foreach($wxinfoCheck as $k => $v){
////            echo $v['openid'];
//            $wxinfoCheckOpenid = M('users')->where(array('openid'=>$v['openid']))->select();
//            if (count($wxinfoCheckOpenid)>1) {
//                var_dump($wxinfoCheckOpenid);
//                    for($i = 1;$i<count($wxinfoCheckOpenid);$i++){
//                        echo $wxinfoCheckOpenid[$i]['id'].'|||||';
//                        M('users')->where(array('id'=>$wxinfoCheckOpenid[$i]['id']))->delete();
//                    }
//                
//                
//                }
//                
//        }
//        echo count($wxinfoCheck);
    }

    public function createMenu() {
        $http = new \Think\Http;
        $token = $this->getToken();
        $array = array('button' => array(
//            array('type'=>'click',
//            'name'=>'微商城',
//            'key'=>'WESHOP'),
                //
//            array('type'=>'click',
//            'name'=>'通告中心',
//            'key'=>'ANNOUNCEMENT'),
                array('type' => 'view',
                    'name' => '接通告',
                    'url' => 'http://www.kmic168.com/?m=web'
                ),
                array('type' => 'view',
                    'name' => '找主持',
                    'url' => 'http://www.kmic168.com/?m=web&a=artist'
                ),
                array('name' => '更多',
                    'sub_button' => array(
                        array('type' => 'view',
                            'name' => '卖闲置',
                            'url' => 'http://www.kmic168.com/?m=web&a=goods'),
                        array('type' => 'view',
                            'name' => '发通告',
                            'url' => 'http://www.kmic168.com/?m=web&a=announcement'),
                        array('type' => 'view',
                            'name' => '主持稿',
                            'url' => 'http://www.kmic168.com/?m=web&c=school'),
                        array('type' => 'view',
                            'name' => '消息通知',
                            'url' => 'http://www.kmic168.com/?m=Center&c=Profile&a=message'),
                        array('type' => 'view',
                            'name' => 'VIP会员续费',
                            'url' => 'http://www.kmic168.com/?m=Center&c=Profile&a=member')
                    )),
//            array('type'=>'view',
//            'name'=>'用户中心',
//            'url'=>'http://www.kmic168.com/?m=Center&c=Profile'),
//            //
            )
        );
//        echo ;
        $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $token, json_encode($array, JSON_UNESCAPED_UNICODE));
        var_dump($post);
    }

    public function deposit() {
        $user = M('users', 'center_')->where('`openid` =  \'' . session('openid') . '\' and `level` =1')->find();
//        var_dump($user);
        if (IS_POST) {
            if (I('post.money') < 20 || I('post.money') > 200 || I('post.money') > $user['deposit']) {
                echo json_encode(array('result' => FALSE));
            } else {
                if (true) {
                    $dec = M('users', 'center_')->where('`openid` =  \'' . session('openid') . '\' and `level` =1')->setDec('deposit', I('post.money'));
                    if ($dec) {
                        $userafter = M('users', 'center_')->where('`openid` =  \'' . session('openid') . '\' and `level` =1')->find();
                        $dec = M('deposit_order')->add(array('uid' => $user['id'], 'timeline' => time(), 'money' => I('post.money'), 'before' => $user['deposit'], 'after' => $userafter['deposit']));
                        echo json_encode(array('result' => TRUE));
                    } else {
                        echo json_encode(array('result' => FALSE));
                    }
                }
            }
        }
//        var_dump($_REQUEST);
    }

    public function help() {
        $this->display();
    }

    public function getQRcode() {
        echo $this->findQRcode();
    }

    function userDetail($openid) {
        return M('users')->where(array('openid' => $openid))->find();
    }

    function userMessage($openid) {
        $list = M('users')->where(array('openid' => $openid))->find();
        return M('user_detail')->where(array('uid' => $list['id']))->find();
    }

    function findQRcode() {
        $path = './QRcode/';
        $file = $path . I('get.openid') . '.jpg';
        $file_url = I('get.qrcode');

//        echo S('wx_info'.session('openid'))['headimgurl'];
//        return $this->ticket($file);
        return $this->ticket($file, $file_url);
    }

    function face() {
        
    }

    function ticket($file, $file_url) {
        $ticket = '';
        $http = new \Think\Http;
        $token = $this->getToken();

        define('UPLOAD_DIR', './QRcode/');
        $file_url = str_replace('data:image/png;base64,', '', $file_url);
        $file_url = str_replace(' ', '+', $file_url);
        $data = base64_decode($file_url);
        $file1 = UPLOAD_DIR . I('get.openid') . '.png';
        $success = file_put_contents($file1, $data);

//         $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token, '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $data['id'] . '}}}');
//         $json = json_decode($post);
//         $ticket = $json->ticket;
//         echo $ticket;
// //        exit();
//         $get = $http->httpGetDownload('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket));
//         $filename = $file;
//         $local_file = fopen($filename, 'w');
//         if (FALSE !== $local_file) {
//             if (FALSE !== fwrite($local_file, $get['body'])) {
//                 fclose($local_file);
//             }
//         }
//         //
//         //
        $t = $http->httpGetDownload($this->userDetail(I('get.openid'))['headimgurl']);
        //print_r($t['header']['content_type']);


        $tfilename;
        if ($t['header']['content_type'] == 'image/jpg') {
            $tfilename = './QRcode/' . I('get.openid') . '_f.jpg';
        } else if ($t['header']['content_type'] == 'image/jpeg') {
            $tfilename = './QRcode/' . I('get.openid') . '_f.jpg';
        } else if ($t['header']['content_type'] == 'image/png') {
            $tfilename = './QRcode/' . I('get.openid') . '_f.png';
        } else if ($t['header']['content_type'] == 'image/gif') {
            $tfilename = './QRcode/' . I('get.openid') . '_f.gif';
        }

// //        echo 'abc';
//        exit();
//        $tfilename = './QRcode/'.session('openid').'_f.png';
        $local_file = fopen($tfilename, 'w');
        if (FALSE !== $local_file) {
            if (FALSE !== fwrite($local_file, $t['body'])) {
                fclose($local_file);
            }
        }
        //
        //
        
//        print_r(pathinfo($tfilename));
//        exit();
        //文件名如a.php，本例适应显示方式动态合并，须GD库支持
        $dest = imagecreatefrompng("./QRcode/qrbg2.png");    //底图
        $src = imagecreatefrompng($file1); //透明图
        $face = imagecreatefromjpeg($tfilename);
        // $extension = pathinfo($tfilename)['extension'];
        // $face;
        // if ($extension == 'jpg' || $extension == 'jpeg') {
        //     $face = imagecreatefromjpeg($tfilename);
        //     //echo $face;
        // } else if ($extension == 'png') {
        //     $face = imagecreatefrompng($tfilename);
        // } else if ($extension == 'gif') {
        //     $face = imagecreatefromgif($tfilename);
        // }

        $im = imagecreate(365, 120);
        $bg = imagecolorallocate($im, 255, 204, 3);
        $textcolor = imagecolorallocate($im, 255, 255, 255);
        imagecolortransparent($im, $bg);

        $im1 = imagecreate(620, 80);
        $bg1 = imagecolorallocate($im1, 255, 204, 3);
        $textcolor1 = imagecolorallocate($im1, 255, 255, 255);
        imagecolortransparent($im1, $bg1);

        $im2 = imagecreate(620, 80);
        $bg2 = imagecolorallocate($im2, 255, 204, 3);
        $textcolor2 = imagecolorallocate($im2, 255, 255, 255);
        imagecolortransparent($im2, $bg2);

        $fontSize = 60;
        $fontSize1 = 48;
        $font = "./Public/fonts/fzqkbysjw.TTF";
        $str1 = "我在" . $this->userMessage(I('get.openid'))['city'];
        $str = $this->userDetail(I('get.openid'))['name'];
        $str2 = "期望酬劳：" . intval($this->userMessage(I('get.openid'))['price']) . "/场";
        // imagestring($im,60,0,0,$str,$textcolor);

        imagettftext($im, $fontSize, 0, 50, 100, $textcolor, $font, $str);
        imagettftext($im1, $fontSize1, 0, 0, 70, $textcolor1, $font, $str1);
        imagettftext($im2, $fontSize1, 0, 0, 70, $textcolor2, $font, $str2);
        //字体设置部分linux和windows的路径可能不同
        // header("Content-type:image/png; UTF-8");
        //缩小二维码
        $qrimg = imagecreate(258, 258);
        imagecopyresampled($qrimg, $src, 0, 0, 0, 0, 258, 258, imagesx($src), imagesy($src)); //关键函数，后面解释 
        // echo $qrimg;

        $fimg_big = imagecreatetruecolor(900, 900);
        imagecopyresampled($fimg_big, $face, 0, 0, 0, 0, 900, 900, imagesx($face), imagesy($face));

        //缩小头像
        $fimg = imagecreate(80, 80);
        imagecopyresampled($fimg, $face, 0, 0, 0, 0, 80, 80, imagesx($face), imagesy($face));
        // echo $fimg;

        imagecopy($qrimg, $fimg, 258 / 2 - 40, 258 / 2 - 40, 0, 0, 80, 80);    //合并，注意大小和座标
        //
        imagecopy($dest, $qrimg, 880, 1650, 0, 0, 258, 258);    //合并，注意大小和座标

        imagecopy($dest, $fimg_big, 170, 550, 0, 0, 900, 900);

        imagecopy($dest, $im, 500, 410, 0, 0, 365, 125);
        imagecopy($dest, $im1, 100, 1620, 0, 0, 620, 80);
        imagecopy($dest, $im2, 100, 1780, 0, 0, 620, 80);

        header('Content-Type:image/jpeg');    //声明格式
        imagejpeg($dest, "./QRcode/" . I('get.openid') . "_p.jpg");
        //输出图片，如果需要保存的话，imagepng($dest, $file); 
        @imagedestroy($dest);    //释放内存
        @imagedestroy($src);    //释放内存
        @imagedestroy($im);     //释放内存
        @imagedestroy($im1);     //释放内存
        @imagedestroy($im2);     //释放内存
        //
        return json_encode(array("msg" => "./QRcode/" . I('get.openid') . "_p.jpg"));
//        echo '<img src="'.$get['body'].'"/>';
    }

    function unsubscribe($openid) {
        \Think\Log::write('unsubscribeAction:' . $openid);
        M('users', 'center_')->where('`openid` = \'' . $openid . '\'')->save(array('issubscribe' => 0, 'unsubscribedata' => time()));

        //Leand cloud
        //
        $url = "http://www.kmic168.com/?m=center&a=unsubscribeHttp";

        $param['userid'] = strval($openid);

        $param = http_build_query($param);
        $sockopen = fsockopen('www.kmic168.com', 80, $errno, $errstr, 30);
        if (!$sockopen) {
            echo "error!$errstr ($errno)<br />\n";
        }

        $sendm = "POST " . $url . " HTTP/1.1\r\n";
        $sendm .= "Host:www.kmic168.com\r\n";
        $sendm .= "Content-type:application/x-www-form-urlencoded\r\n";
        $sendm .= "Content-length:" . strlen($param) . "\r\n";
        $sendm .= "Connection:close\r\n\r\n";
        $sendm .= $param;
        fwrite($sockopen, $sendm);
        fclose($sockopen);
    }

    public function unsubscribeHttp() {
        ignore_user_abort(true);
        set_time_limit(0);

        \Think\Log::write('unsubscribeHttp ' . $_POST['userid']);
        User::logIn($_POST['userid'], "abc");
        $currentUser = User::getCurrentUser();
        $currentUser->set("unsubscribedata", strval(time()));
        $currentUser->set("issubscribe", '0');

        try {
            $currentUser->save();
            \Think\Log::write('LC unsubscribeHttp 成功');
        } catch (CloudException $ex) {
            \Think\Log::write('LC unsubscribeHttp ' . $ex);
        }
    }

    //S('token')
//    var $tryTime = 0;

    function saveUserInfoWhenSubscribe($openid, $agent = null) {

        $info = $this->getUserInfoFromWC($openid);
        $infoArr = json_decode($info);

//        if (array_key_exists('errcode', $infoArr) && $tryTime < 3) {
//            S('token', NULL);
//            saveUserInfoWhenSubscribe();
//
//            \Think\Log::write('出错了,重试' . $info);
//            $tryTime++;
//            exit();
//        } else if ($tryTime >= 3) {
//            \Think\Log::write('出错了,重试了3次' . $info);
//            $tryTime = 0;
//            exit();
//        } else {
//            \Think\Log::write('关注成功');
//            $tryTime = 0;
//        }


        $http = new \Think\Http;
        $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getToken(TRUE) . '&openid=' . $openid . '&lang=zh_CN');
        $arr = object_array(json_decode($respond));
        \Think\Log::write('关注成功 : ' . $respond);
        //union
        if (!array_key_exists('errcode', $arr)) {
            $uniondata = M('union_wx')->where(array('unionid' => $arr['unionid']))->find();
            if (!$uniondata) {
                M('union_wx')->add(array('subscribe' => $arr['subscribe'],
                    'openid' => $arr['openid'],
                    'nickname' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($arr['nickname'])),
                    'sex' => $arr['sex'],
                    'city' => $arr['city'],
                    'country' => $arr['country'],
                    'province' => $arr['province'],
                    'language' => $arr['language'],
                    'headimgurl' => $arr['headimgurl'],
                    'subscribe_time' => $arr['subscribe_time'],
                    'unionid' => $arr['unionid'],
                    'remark' => $arr['remark'],
                    'groupid' => $arr['groupid'],
                    'tagid_list' => $arr['tagid_list']));
            }


            M('users')->where(array('openid' => $openid))->save(array('unionid' => $arr['unionid']));
        }

        $userdata = M('users')->where('`openid` = \'' . $openid . '\'')->find();
//        //wx_info
        if (!$userdata) {
//            $arr['openid'] = $infoArr->openid;
//            $arr['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',addslashes($info));
//            $arr['subscribeData'] = time();
//            $arr['platform'] = 'wechat';
//            $arr['issubscribe'] = '1';
//            if ($agent != null && strlen($agent)>0) {
//                $agentExp = explode('_', $agent)[1];
//                $arr['agent'] = $agentExp;
//                M('users','center_')->where('id='.$agentExp)->setInc('point',5);
//            }
//            M('users','center_')->add($arr);



            $newmember = M('users')->add(array('openid' => $arr['openid'], 'addtime' => time(), 'logintime' => time(),
//                    'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($info)),
                'name' => $arr['nickname'],
                'sex' => $arr['sex'],
                'country' => $arr['country'],
                'province' => $arr['province'],
                'city' => $arr['city'],
                'unionid' => $arr['unionid']));
        } else {
//            M('users','center_')->where('`openid` = \''.$openid.'\'')->save(array('userinfo'=>  preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($info)),'issubscribe'=>1));
            M('users')->where('`openid` = \'' . $openid . '\'')->save(array('wx_info' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($info)), 'unionid' => $arr['unionid']));
        }
    }

    //
    public function saveUserInfoFromWeibo() {
//        $info = $this->getUserInfoFromWC(I('post.openid'));
//        $info = json_decode(I('post.data'));
//        var_dump($_POST);
        $userdata = M('users', 'center_')->where('`openid` = \'' . $_POST['access_token'] . '\'')->find();
        if (!$userdata) {
            $arr['openid'] = $_POST['access_token'];
            $arr['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes(json_encode($_POST)));
            $arr['subscribeData'] = time();
            $arr['platform'] = 'weibo';
            if (isset($_POST['agent'])) {
                $arr['agent'] = explode('_', I('post.agent'))[1];
            }
            M('users', 'center_')->add($arr);
        } else {
            M('users', 'center_')->where('`openid` = \'' . $info['access_token'] . '\'')->save(array('updataTime' => time()));
        }
    }

    //
    function getUserInfoFromWC($openid) {
        $token = $this->getToken();
        $http = new \Think\Http;
        $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN');
        return $json;
    }

    function levelTitle() {
        return array(0 => '普通会员', 1 => '高级会员');
    }

//    public function getUserInfo(){
//        $levelTitle = $this->levelTitle();
//        
//        $AES = new \Think\AES();
//        $arr = $AES->decode($_REQUEST['data']);
//        $data = decryptData($arr);
//        //
//        $userdata = M('users','center_')->where('`openid` = \''.$data->openid.'\'')->find();
//        $userInfo = '';
//        $arrJson = array();
//        if ($userdata) {
//            $userInfo = $userdata['userinfo'];
//            $arrJson['code'] = 0;
//            $arrJson['data'] = array('wx_info'=>$userInfo,'level'=>$userdata['level'],'isban'=>$userdata['isban'],'levelTitle'=>$levelTitle[$userdata['level']],'time'=>  time(),'ismob'=>TRUE);
//            //
//            
//        }else{
////            print_r('sbv');
//            $userInfo = $this->getUserInfoFromWC($data->openid);
//            $json = json_decode($userInfo);
//            
//        
//            if (array_key_exists('errcode',$json) && $tryTime < 3) {
//                S('token',NULL);
//                $this->getUserInfo();
//
//                \Think\Log::write('已关注 出错了,重试'.$info);
//                $tryTime++;
//                exit();
//            }else if($tryTime >=3){
//                \Think\Log::write('已关注 出错了,重试了3次'.$info);
//                $tryTime = 0;
//                exit();
//            }else{
//                \Think\Log::write('关注成功');
//                $tryTime = 0;
//            }
//        
//        
//            if (array_key_exists("errcode",$json)) {
//                \Think\Log::write('这里走了就出错了');
//                $arrJson['code'] = $userInfo;
//            }else{
//                \Think\Log::write('这里走了就是一个新关注');
//                M('users','center_')->add(array('openid'=>$data->openid,'userinfo'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),'updataTime'=>time(),'platform'=>'wechat','subscribeData'=>  time()));
//                $arrJson['code'] = 0;
//                $arrJson['data'] = array('wx_info'=>addslashes($userInfo),'level'=>0,'isban'=>0,'levelTitle'=>$levelTitle[0],'time'=>  time(),'ismob'=>TRUE);
//                //
//                //
//                $newmember = M('users')->add(array('openid'=>$data->openid,'addtime'=>  time(),'logintime'=>  time(),
//                    'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),
//                    'name'=>$json->nickname,'sex'=>$json->sex,'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
//                
//                
//                
//            }
//            
//        }
//        //
//        
////        $arrJson = array('code'=>0,'data'=>array('wx_info'=>$userInfo,'time'=>  time(),'ismob'=>TRUE));
//        $arrJson = json_encode($arrJson);
//        echo $AES->encode($arrJson) ;
//        
//    }
//    public function leanCloudSignUpHttp() {
//        ignore_user_abort(true);
//        set_time_limit(0);
//        //
//        //lc会员
//        $isMember = false;
//        try {
//            User::logIn($_POST['userid'], "abc");
//            \Think\Log::write('已经是会员');
//            $isMember = true;
//        } catch (CloudException $ex) {
////            var_dump($ex);
//            \Think\Log::write('不是会员 '.$ex);
//            $isMember = false;
//        } 
//        if ($isMember == false) {
//            $userLC = new User();              // 新建 User 对象实例
//            $userLC->setUsername($_POST['userid']);           // 设置用户名
//            $userLC->setPassword('abc');     // 设置密码
//            $userLC->set("name", $_POST['nickname']);
//            $userLC->set("sex", $_POST['sex']);
//            $userLC->set("platform", $_POST['platform']);
//            $userLC->set("country", $_POST['country']);
//            $userLC->set("province", $_POST['province']);
//            $userLC->set("city", $_POST['city']);
//            $userLC->set("headimgurl", $_POST['headimgurl']);
//            $userLC->set("lastupdate", strval(time()));
//            $userLC->set("issubscribe", strval($_POST['issubscribe']));
//            if ($_POST['subscribeData'] != null) {
//                $userLC->set("subscribeData", strval($_POST['subscribeData']));
//            }
//            \Think\Log::write('LC SignUp :'.  implode('---', $array));
//    //                
//            try {
//                $userLC->signUp();
//                \Think\Log::write('LC SignUp 成功');
//            } catch (CloudException $ex) {
//    //            var_dump($ex);
//                \Think\Log::write('LC reg '.$ex);
//            }  
//        }else{
//            $currentUser = User::getCurrentUser();
//            $currentUser->set("issubscribe", '1');
//
//            try {
//                $currentUser->save();
//                \Think\Log::write('LC unsubscribeHttp 成功');
//            } catch (CloudException $ex) {
//                \Think\Log::write('LC unsubscribeHttp '.$ex);
//            } 
//        }
//         
//    }
//    function leanCloudSignUp($array){
////        \Think\Log::write('LC array '.  implode('--', $array));
//        /*
//        $url = "http://www.kmic168.com/?m=center&a=leanCloudSignUpHttp";
//
//        $param = $array;
//        $param['userid'] = strval($array['openid']);
//        
//        $param = http_build_query($param);
//        $sockopen = fsockopen('www.kmic168.com',80,$errno,$errstr,30);
//        if(!$sockopen)
//        {
//                echo "error!$errstr ($errno)<br />\n";
//        }
//
//        $sendm  = "POST ".$url." HTTP/1.1\r\n";
//        $sendm .= "Host:www.kmic168.com\r\n";
//        $sendm .= "Content-type:application/x-www-form-urlencoded\r\n";
//        $sendm .= "Content-length:".strlen($param)."\r\n";
//        $sendm .= "Connection:close\r\n\r\n";
//        $sendm .= $param;
//        fwrite($sockopen,$sendm);
//        fclose($sockopen);
////        var_dump($array);
//          */
//    }


    public function getUserInfoInSite() {
//        session('openid',NULL);
        if (session('openid') == null) {
            session('openid', I('get.openid'));
        }
        $openid = session('openid') == null ? I('get.openid') : session('openid');
        //已经有
        $levelTitle = $this->levelTitle();
//        S('wx_info'.I('get.openid'),null);
//        var_dump(S('wx_info'.$openid));
        if (S('wx_info' . $openid) != NULL) {
//            return object_array(json_decode(S('wx_info')));
        }
//        $userdata = M('users','center_')->where('`openid` = \''.$openid.'\'')->find();
//        
//        $arr = object_array(json_decode(stripslashes($userdata['userinfo'])));
//            $arr['level']=$userdata['level'];
//            $arr['id']=$userdata['id'];
//            $arr['levelTitle']=  $levelTitle[$userdata['level']];
//            S('wx_info'.$openid,$arr,3600);
    }

    public function checkOrder() {

        $AES = new \Think\AES();
        $arr = $AES->decode($_REQUEST['data']);

        $data = decryptData($arr);
//        print_r($data);
//        exit();
        if ($data == FALSE) {
            $this->error('超时', '/pay', 5);
            exit();
        }
        $sql = M('orders', 'center_')->where(array('orderid' => $data->paid))->find();
//        print_r($sql);
        if ($sql['paid'] == 1) {
            $array = array('code' => 0, 'data' => array('paid' => TRUE, 'paidtime' => $sql['paidtime'], 'time' => time()));
        } else {
            $array = array('code' => 0, 'data' => array('paid' => FALSE, 'paidtime' => $sql['paidtime'], 'time' => time()));
        }

        $arrJson = json_encode($array);
        echo $AES->encode($arrJson);
    }

    public function pay() {
        $AES = new \Think\AES();

        $arr = $AES->decode(I('get.data'));
        $data = decryptData($arr);
//        var_dump($arr);
//        exit();
        if ($data == FALSE) {
            $this->error('超时', '/pay', 5);
            exit();
        }

        $sqlData = M('orders', 'center_')->where(array('orderid' => $data->orderid, 'paid' => 0))->find();
        if ($sqlData) {
            $orderid = $sqlData['id'];
            $arr = array();
            $arr['addtime'] = time();
            $arr['price'] = $data->price;
            if (array_key_exists('level', $data)) {
                $arr['level'] = $data->level;
            }
            M('orders', 'center_')->where(array('orderid' => $data->orderid))->save($arr);
        } else {
            $arr = array();
            $arr['iid'] = $data->iid;
            $arr['openid'] = $data->openid;
            $arr['price'] = $data->price;
            $arr['title'] = $data->title;
            $arr['type'] = $data->type;
            $arr['addtime'] = time();
            $arr['returnUrl'] = $data->returnUrl;
            if (array_key_exists('level', $data)) {
                $arr['level'] = $data->level;
            }
            $saveData = M('orders', 'center_')->add($arr);
            $orderid = $saveData;
        }
        $this->redirect('pay_wx', array('orderid' => $orderid));
        //支付
//        require_once "WxPay.JsApiPay.php";
//        require_once 'log.php';
    }

    public function pay_wx() {
        ini_set('date.timezone', 'Asia/Shanghai');

        $tools = new \Think\Wxpay\lib\JsApiPay();
        $openId = $tools->GetOpenid(I('get.orderid'));
        //
        $sqlData = M('orders', 'center_')->where(array('id' => I('get.orderid')))->find();
//        $this->display();
//        print_r($sqlData);
//        exit();
        //下单
        $input = new \Think\Wxpay\lib\WxPayUnifiedOrder();
        $input->SetBody($sqlData['title']);
        $input->SetAttach($sqlData['openid']);
//        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetOut_trade_no(date("YmdHi") . $sqlData['id']);
        $input->SetTotal_fee($sqlData['price'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3600));
        $input->SetGoods_tag(I('get.orderid'));
        $input->SetNotify_url(self::API_URL . "index.php/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
//        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //print_r($order);
        exit();
        $jsApiParameters = $tools->GetJsApiParameters($order);

//        print_r($sqlData);
        $this->assign('sqlData', $sqlData);
        $this->assign('jsApiParameters', $jsApiParameters);
        $this->display();
    }

    function notify() {
        //初始化日志

        $notify = new \Think\Wxpay\lib\Notify();
        $notify->Handle(false);
    }

    function getToken($getJsapi_ticket = FALSE) {
        
//        $http = new \Think\Http;
//        $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->findConfWithKey('AppID') . '&secret=' . $this->findConfWithKey('AppSecret'));
//        \Think\Log::write('--respond json ' . $json);
//        $json = json_decode($json);
////        S('token', $json, 7200);
////        $value = S('token');
//        if ($getJsapi_ticket == TRUE) {
//            $this->getJsapi_ticket();
//        }
//        return $json->access_token;
//        S('token',null);
        $value = S('token');
        if ($value == NULL || $value->access_token == NULL) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->findConfWithKey('AppID') . '&secret=' . $this->findConfWithKey('AppSecret'));
            $json = json_decode($json);
            S('token', $json, 5200);
            $value = S('token');
            if ($getJsapi_ticket == TRUE) {
                $this->getJsapi_ticket();
            }
            \Think\Log::write('--respond json ' . $json);
            return $value->access_token;
        } else {
            $value = S('token');
            if ($getJsapi_ticket == TRUE) {
                $this->getJsapi_ticket();
            }
            \Think\Log::write('--respond json cache' . json_encode($value));
            return $value->access_token;
        }
    }

    private function getJsapi_ticket() {
//        S('jsapi_ticket',null);
//        echo 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.S('token')->access_token.'&type=jsapi';
        $value = S('jsapi_ticket');
        \Think\Log::write('--respond jsapi_ticket ' . json_encode($value));
//        var_dump($value);
        if ($value == NULL || $value->ticket == NULL || $value->errcode != 0) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . S('token')->access_token . '&type=jsapi');
//            var_dump($json);
            $json = json_decode($json);
            S('jsapi_ticket', $json, 6200);
            if ($value->errcode != 0) {
                $this->getToken(TRUE);
                exit();
            }
            $value = S('jsapi_ticket');
            return $value->ticket;
        } else {
            $value = S('jsapi_ticket');
            return $value->ticket;
        }
    }

    //

    public function getOpenId() {
        //getOpenId 在这里才拿openid
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->findConfWithKey('AppID')}&secret={$this->findConfWithKey('AppSecret')}&code={$_REQUEST['code']}&grant_type=authorization_code";
        $http = new \Think\Http;
        $respond = $http->httpGet($url);
        //再跳回原来的网址
        $arr = json_decode($respond);

        $arrUnion = object_array(json_decode($respond));
        //////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////
        //unionid
        if (!array_key_exists("unionid", $arrUnion)) {
            $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getToken(TRUE) . '&openid=' . $arr->openid . '&lang=zh_CN');
            $arrUnion = object_array(json_decode($respond));
            $isExist = FALSE;

            if (array_key_exists('errcode', $arr)) {
                //重试
                sleep(1);
                $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getToken(TRUE) . '&openid=' . $arr->openid . '&lang=zh_CN');
                $arrUnion = object_array(json_decode($respond));
            }
        }
        \Think\Log::write('--respond ' . $respond);
        $unionid = M('users')->where(array('unionid' => $arrUnion['unionid']))->find();
//        if (!$unionid && $arr->openid != '') {
//            M('users')->add(array('openid'=>$arr->openid,'addtime'=>  time(),'logintime'=>  time(),
//                    'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($respond)),
//                    'name'=>$arr->nickname,'sex'=>$arr->sex,'country'=>$arr->country,'province'=>$arr->province,'city'=>$arr->city,
//                'unionid'=>$respond->unionid));
//            
//            $unionid = M('users')->where(array('unionid'=>$arrUnion['unionid']))->find();
//        }
//        \Think\Log::write(json_encode($unionid).'----'.$arrUnion['unionid']);
        if ($unionid) {

            $isExist = TRUE;
        }
        ////
        $userInfo = $this->getUserInfoFromWX($arr->openid);
        $jsonUserInfo = json_decode($userInfo);

        $arrAdd = array();
        if (array_key_exists("unionid", $arrUnion) || 'http://' . $this->getDomain() == 'http://tkm.yuemai168.com') {

            if (isset($isExist)) {
                \Think\Log::write('更新' . $userInfo);
                if (!isset($jsonUserInfo->errcode)) {
                    $saveArr = array();
                    $saveArr['openid'] = $arr->openid;
                    $saveArr['logintime'] = time();
                    $saveArr['sex'] = $jsonUserInfo->sex;
                    $saveArr['country'] = $jsonUserInfo->country;
                    $saveArr['province'] = $jsonUserInfo->province;
                    $saveArr['city'] = $jsonUserInfo->city;
                    $saveArr['name'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $jsonUserInfo->nickname);
                    $saveArr['headimgurl'] = $jsonUserInfo->headimgurl;
                    M('users')->where(array('unionid' => $arrUnion['unionid']))->save($saveArr);
                }
            } else {
                \Think\Log::write('添加');
                $newmember = M('users')->add(array('openid' => $arr->openid, 'addtime' => time(), 'logintime' => time(),
//                'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),
                    'name' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $jsonUserInfo->nickname), 'sex' => $jsonUserInfo->sex, 'country' => $jsonUserInfo->country, 'province' => $jsonUserInfo->province, 'city' => $jsonUserInfo->city, 'unionid' => $arrUnion['unionid'],
                    'headimgurl' => $jsonUserInfo->headimgurl));
            }

            //
        } else {
            header("Location: http://mp.weixin.qq.com/s/JDGPm0PwKZxsTaV95NNWLQ");
            exit;
        }


        session('openid', $arr->openid);
        header("Location: " . session('currentUrl') . "&openid=" . $arr->openid . "");
        exit();
    }

    function getUserInfoFromWX($openid) {
        $token = $this->getToken();
        $http = new \Think\Http;
        $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN');
        return $json;
    }

    public function weixinGetOpenid() {
        //先拿到code然后到 getOpenId
        session('currentUrl', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $redirect_uri = urlencode('http://' . $_SERVER['HTTP_HOST'] . '/?m=center&a=getOpenId');

//        exit('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->findConfWithKey('AppID')}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base#wechat_redirect";

        header("Location: " . $url . "");
    }

    function findConfWithKey($key) {
        $conf = M('config', 'center_')->where('`key` = \'' . $key . '\'')->find();
        return $conf['value'];
    }

    public function updataWXinfo() {
        \Think\Log::write('updataWXinfo ');
        $currentUser = User::getCurrentUser();
        $currentUser->getUsername();
        $currentUser->getEmail();

        \Think\Log::write('updataWXinfo ' . $currentUser->getUsername());
    }

    public function wx() {
        //验证的时候用
//        $echoStr = $_GET['echostr'];
//        //valid signature , option
//        if ($this->checkSignature()) {
//            echo $echoStr;
//            die;
//        }
//        
//        
        //正常的时候用
        //get post data, May be due to the different environments
        //    $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postStr = file_get_contents("php://input");
        //extract post data
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case 'text':
                    $resultStr = $this->handleText($postObj);
                    break;
                case 'event':
                    $resultStr = $this->handleEvent($postObj);
                    break;
                case 'click':
                    $resultStr = $this->handleEvent($postObj);
                    break;
                default:
                    $resultStr = 'UnknowXX msg type: ' . $RX_TYPE;
                    break;
            }
            echo $resultStr;
        } else {
            echo '';
            die;
        }
    }

    public function handleText($postObj) {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>';
        if (!empty($keyword)) {
            $msgType = 'text';
            $contentStr = '';
            if (strtoupper($keyword) == 'S') {
                $contentStr = '<a href=\'http://wechatshop.witheasy.com/?m=Collect&openid=' . $fromUsername . '\'>进入微商城</a>';
            } else if (strtoupper($keyword) == 'M') {
                $contentStr = '<a href=\'http://www.kmic168.com/?m=Center&c=Profile&openid=' . $fromUsername . '\'>进入列表</a>';
            } else if (strtoupper($keyword) == 'B2') {
                $contentStr = '<a href=\'http://www.kmic168.com/?m=web&a=artist&openid=' . $fromUsername . '\'>个人风采</a>';
            } else if (strtoupper($keyword) == '主持卡') {
                $contentStr = '<a href=\'http://www.kmic168.com/?m=Center&c=profile&a=qrcode&openid=' . $fromUsername . '\'>主持卡</a>';
            } else if (strtoupper($keyword) == '稿' || strtoupper($keyword) == '稿件' || strtoupper($keyword) == '主持稿' || strtoupper($keyword) == 'gao' || strtoupper($keyword) == 'GAO') {
                $contentStr = '各类主持稿件,可以点击“查看历史消息”回看本公众号之前的分享；也可以点击主页下栏【我是主持】的“主持学堂”，内含各类主持稿件及学习素材，欢迎关注！谢谢您支持开麦！';
            } else {
                $contentStr = '您是主持人吗？点击“接通告”即可查看全国海量通告；点击“更多”，还可以轻松获取各类主持词，参与闲置礼服交易。
 
您需要找主持吗？点击“找主持”就可以！点击“更多-发通告”，让你的需求一秒被知道。
 
有任何意见或反馈、商务合作，可在公众号对话框输入你的需求哦~';
            }
//            $contentStr = $keyword.'<a href=/index.php?m=Home&c=Index&a=index&openid=' . $fromUsername . '>进入广告大厅</a>';
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        } else {
            echo 'Input something...';
        }
    }

    public function handleEvent($object) {
        $fromUsername = $object->FromUserName;

        $key = $object->EventKey;

        $contentStr = '欢迎关注开麦主持！
 
您是主持人吗？点击“接通告”即可查看全国海量通告；点击“更多”，还可以轻松获取各类主持词，参与闲置礼服交易。
 
您需要找主持吗？点击“找主持”就可以！点击“更多-发通告”，让你的需求一秒被知道。
 
有任何意见或反馈、商务合作，可在公众号对话框输入你的需求哦~';
        $type = 0;
        switch ($object->Event) {
            case 'subscribe':
                \Think\Log::write('subscribe' . $fromUsername . 'KEY' . $key);

                //记录
                if (isset($object->EventKey)) {
                    $this->saveUserInfoWhenSubscribe($fromUsername, $object->EventKey);
                } else {
                    $this->saveUserInfoWhenSubscribe($fromUsername);
                }
                \Think\Log::write('有没有来到这里啊?');
                //
                break;

            case 'unsubscribe':
                \Think\Log::write('unsubscribe' . $fromUsername);
                //记录
                $this->unsubscribe($fromUsername);

                break;
            case 'CLICK':
                if ($key == 'ANNOUNCEMENT') {
                    $type = 1;
                    $contentStr = '<a href=\'http://www.kmic168.com/?m=web&openid=' . $fromUsername . '\'>通告中心</a>';
                } elseif ($key == 'ARTIST') {
                    $type = 2;
                    $contentStr = '<a href=\'http://www.kmic168.com/?m=web&a=artist&openid=' . $fromUsername . '\'>个人风采</a>';
                } elseif ($key == 'CENTER') {
                    $type = 3;
                    $contentStr = '<a href=\'http://www.kmic168.com/?m=Center&c=Profile&openid=' . $fromUsername . '\'>用户中心</a>';
                } else {

                    $contentStr = $key;
                }

                break;
            case 'SCAN':
                $contentStr = '您已经关注过了，谢谢支持。';
                break;
            default:
                $contentStr = 'Unknow Event: ' . $object->Event;
                break;
        }
        if ($type == 0) {
//            \Think\Log::write('进来这里了吗:' . $contentStr . ' Event:' . $object->Event);
            $resultStr = $this->responseText($object, $contentStr);
        } else if ($type == 1) {
            $resultStr = $this->responseTextAndImg($object, array('title' => '通告中心', 'desc' => '查阅每天实时更新的全国各地通告信息。发布通告，将有来自全国各地的主持人关注，有意向者将会主动与您联系。',
                'picurl' => 'http://www.kmic168.com/images/annou.jpg',
                'url' => 'http://www.kmic168.com/?m=web&openid=' . $fromUsername, ''));
        } else if ($type == 2) {
            $resultStr = $this->responseTextAndImg($object, array('title' => '主持资源库', 'desc' => '编辑主持人信息，发布表演照片和视频。',
                'picurl' => 'http://www.kmic168.com/images/profiles.jpg',
                'url' => 'http://www.kmic168.com/?m=web&a=artist&openid=' . $fromUsername, ''));
        } else if ($type == 3) {
            $resultStr = $this->responseTextAndImg($object, array('title' => '用户中心', 'desc' => '个人信息修改,消息查看,推广二维码,微信客服。',
                'picurl' => 'http://www.kmic168.com/images/center.jpg',
                'url' => 'http://www.kmic168.com/?m=Center&c=Profile&openid=' . $fromUsername, ''));
        }
        return $resultStr;
    }

    public function responseText($object, $content, $flag = 0) {
//        \Think\Log::write('准备要打印出来了');
        $textTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>';
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
//        \Think\Log::write('打印出来什么了' . $resultStr);
        return $resultStr;
    }

    public function responseTextAndImg($object, $data = array()) {
        $textTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[%s]]></Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    </Articles>
                    </xml>';
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $data['title'], $data['desc'], $data['picurl'], $data['url']);
        return $resultStr;
    }

    private function checkSignature() {
        // you must define TOKEN by yourself
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function udid($openid) {
        $user = M('users')->where(array('openid' => $openid))->field('id')->find();
        $udid = substr($user['id'] * 928, 0, 3) . $user['id'];
        S('udid' . $openid, $udid, 21600);
    }

    public function idToUdid($id) {
        $user = M('users')->where(array('id' => $id))->field('id')->find();
        $udid = substr($user['id'] * 928, 0, 3) . $user['id'];
        return $udid;
    }

    public function returnUdid($openid) {
        $user = M('users')->where(array('openid' => $openid))->field('id')->find();
        $udid = substr($user['id'] * 928, 0, 3) . $user['id'];
        return $udid;
    }

    public function udidToOpenid($udid) {
        $id = substr($udid, 3);
        $user = M('users')->where(array('id' => $id))->field('openid')->find();
        return $user['openid'];
    }

}
