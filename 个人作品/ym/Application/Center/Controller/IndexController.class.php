<?php

namespace Center\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

class IndexController extends Controller {

//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const API_URL = 'http://ym.yuemai168.com/';
    const TOKEN = 'feds34fehrsd76frerdsku8745hywevr'; //验证wxapi接口的 token

    Public function _initialize() {
        
    }

    /**
     * 
     */
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
//            array('name'=>'我找主持',
//                'sub_button'=>array(
//                    array('type'=>'view',
//                    'name'=>'发通告',
//                    'url'=>self::API_URL.'?m=web&a=announcement'),
//                    array('type'=>'view',
//                    'name'=>'主持人列表',
//                    'url'=>self::API_URL.'?m=web&a=artist'),
//                    array('type'=>'view',
//                    'name'=>'我发布的',
//                    'url'=>self::API_URL.'?m=web&a=mine'),
//                )),
//            
//            array('name'=>'我是主持',
//                'sub_button'=>array(
//                    array('type'=>'view',
//                    'name'=>'接通告',
//                    'url'=>self::API_URL.'?m=web'),
//                    array('type'=>'view',
//                    'name'=>'主持学堂',
//                    'url'=>self::API_URL.'?m=web&c=school'),
//                    array('type'=>'view',
//                    'name'=>'上传个人资料',
//                    'url'=>self::API_URL.'?m=web&a=profiles'),
//                )),
//            array('type'=>'click',
//            'name'=>'主持资源库',
//            'key'=>'ARTIST'),
//            array('type'=>'click',
//            'name'=>'用户中心',
//            'key'=>'CENTER')
//            array('name'=>'用户中心',
//                'sub_button'=>array(
//                    array('type'=>'view',
//                    'name'=>'用户中心',
//                    'url'=>self::API_URL.'?m=Center&c=Profile'),
//                    array('type'=>'view',
//                    'name'=>'消息通知',
//                    'url'=>self::API_URL.'?m=Center&c=Profile&a=message'),
//                )),

                array('type' => 'view',
                    'name' => '测试',
                    'url' => self::API_URL . '?m=web'),
//            //
            )
        );
//        echo ;
        $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $token, json_encode($array, JSON_UNESCAPED_UNICODE));
//        var_dump($post);
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

    public function getQRcode() {

        $indexA = A('Center/Index');
        checkWX();

        if (!isset($_GET['openid'])) {

            $indexA->weixinGetOpenid();
            exit();
        }
        if ($this->findQRcode(I('get.qrbg'))) {
            echo '<img src="http://' . $this->getDomain() . '/QRcode/' . I('get.openid') . '_p.jpg" width="100%"/>';
        }
    }

    function findQRcode($qrbg) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/QRcode/';
        $file = $path . I('get.openid') . '.jpg';
//        if (!file_exists($path . I('get.openid') . '_p.jpg')) {
//            return $this->ticket($file,$qrbg);
//        } else {
//            return TRUE;
//        }
        return $this->ticket($file, $qrbg);
    }

    function ticket($file, $qrbg) {
        $ticket = '';
        $http = new \Think\Http;
        $token = $this->getToken();
        $post = $http->httpPost('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token, '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . I('get.openid') . '}}}');
        $json = json_decode($post);
        $ticket = $json->ticket;

//        echo $ticket;
//        exit();
        $get = $http->httpGetDownload('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket));
        $filename = $file;
        $local_file = fopen($filename, 'w');
        if (FALSE !== $local_file) {
            if (FALSE !== fwrite($local_file, $get['body'])) {
                fclose($local_file);
            }
        }
        //
        //
        $user = M('users')->where(array('id' => I('get.openid')))->field('headimgurl')->find();
        if (!$user || $user['headimgurl'] == '') {
            exit('头像不存在');
        }
        $t = $http->httpGetDownload($user['headimgurl']);
//        print_r($t['header']['content_type']);


        $tfilename;
        if ($t['header']['content_type'] == 'image/jpg') {
            $tfilename = $_SERVER['DOCUMENT_ROOT'] . '/QRcode/' . I('get.openid') . '_f.jpg';
        } else if ($t['header']['content_type'] == 'image/jpeg') {
            $tfilename = $_SERVER['DOCUMENT_ROOT'] . '/QRcode/' . I('get.openid') . '_f.jpg';
        } else if ($t['header']['content_type'] == 'image/png') {
            $tfilename = $_SERVER['DOCUMENT_ROOT'] . '/QRcode/' . I('get.openid') . '_f.png';
        } else if ($t['header']['content_type'] == 'image/gif') {
            $tfilename = $_SERVER['DOCUMENT_ROOT'] . '/QRcode/' . I('get.openid') . '_f.gif';
        }
//        $tfilename = './QRcode/'.session('openid').'_f.png';
        $local_file = fopen($tfilename, 'w');
        if (FALSE !== $local_file) {
            if (FALSE !== fwrite($local_file, $t['body'])) {
                fclose($local_file);
            }
        }
        //
        //
        
//        print_r($filename);
//        exit();
        //文件名如a.php，本例适应显示方式动态合并，须GD库支持
        $dest = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'] . "/QRcode/" . $qrbg . ".jpg");    //底图
        $src = imagecreatefromjpeg($filename);      //透明图
        $extension = pathinfo($tfilename)['extension'];
        $face;
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $face = imagecreatefromjpeg($tfilename);
        } else if ($extension == 'png') {
            $face = imagecreatefrompng($tfilename);
        } else if ($extension == 'gif') {
            $face = imagecreatefromgif($tfilename);
        }

        //
        //缩小二维码
        $qrimg = imagecreate(183, 183);
        imagecopyresampled($qrimg, $src, 0, 0, 0, 0, 183, 183, imagesx($src), imagesy($src)); //关键函数，后面解释 
        //
        $fimg = imagecreate(50, 50);
        imagecopyresampled($fimg, $face, 0, 0, 0, 0, 50, 50, imagesx($face), imagesy($face));

        imagecopy($qrimg, $fimg, 183 / 2 - 25, 183 / 2 - 25, 0, 0, 50, 50);    //合并，注意大小和座标
        //
        imagecopy($dest, $qrimg, 55, 39, 0, 0, 183, 183);    //合并，注意大小和座标
//        header('Content-Type:image/jpeg');    //声明格式
        imagejpeg($dest, $_SERVER['DOCUMENT_ROOT'] . "/QRcode/" . I('get.openid') . "_p.jpg", 40);    //输出图片，如果需要保存的话，imagepng($dest, $file); 
        @imagedestroy($dest);    //释放内存
        @imagedestroy($src);    //释放内存
        //
        unlink($tfilename);
        unlink($filename);
        return TRUE;
//        echo '<img src="'.$get['body'].'"/>';
    }

    function unsubscribe($openid) {
        \Think\Log::write('unsubscribeAction:' . $openid);

        $model = D('Users');
        $model->unsubscribe($openid);
    }

    //S('token')
    var $tryTime = 0;

    function saveUserInfoWhenSubscribe($openid, $agent = null) {

        $info = $this->getUserInfoFromWC($openid);
        $infoArr = json_decode($info);

        if (array_key_exists('errcode', $infoArr) && $tryTime < 3) {
            S('token', NULL);
            saveUserInfoWhenSubscribe();

            \Think\Log::write('出错了,重试' . $info);
            $tryTime++;
            exit();
        } else if ($tryTime >= 3) {
            \Think\Log::write('出错了,重试了3次' . $info);
            $tryTime = 0;
            exit();
        } else {
            \Think\Log::write('关注成功');
            $tryTime = 0;
        }


        $http = new \Think\Http;
        $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getToken(TRUE) . '&openid=' . $openid . '&lang=zh_CN');
        \Think\Log::write('关注成功 ' . $respond);

        $arr = object_array(json_decode($respond));
        //union
        if (!array_key_exists('errcode', $arr)) {
            $model = D('Users');
            $model->saveMemberData($arr, $openid, $infoArr, $agent);

            $uniondata = M('union_wx')->where(array('unionid' => $arr['unionid']))->find();
            if (!$uniondata) {

                $model->saveUnionData($arr);
            }
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
//        S('token',null);
        $value = S('token');
        if ($value == NULL || $value->access_token == NULL) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->findConfWithKey('AppID') . '&secret=' . $this->findConfWithKey('AppSecret'));
            $json = json_decode($json);
            S('token', $json, 7200);
            $value = S('token');
            if ($getJsapi_ticket == TRUE) {
                $this->getJsapi_ticket();
            }
            return $value->access_token;
        } else {
            $value = S('token');
            if ($getJsapi_ticket == TRUE) {
                $this->getJsapi_ticket();
            }
            return $value->access_token;
        }
    }

    function getJsapi_ticket() {
//        S('jsapi_ticket',null);
//        echo 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.S('token')->access_token.'&type=jsapi';
        $value = S('jsapi_ticket');
//        var_dump($value);
        if ($value == NULL || $value->ticket == NULL || $value->errcode != 0) {
            $http = new \Think\Http;
            $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . S('token')->access_token . '&type=jsapi');
//            var_dump($json);
            $json = json_decode($json);
            S('jsapi_ticket', $json, 7200);
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

    /**
     * @author jeiry
     * @param string session('currentUrl') 当成功后会跳回session的网页 失败就会跳到关注页
     * @return void
     * 第二步 通过code换取网页授权access_token
     * 
     * 第三步 拉取用户信息
     */
    public function getOpenId() {
        //getOpenId 在这里才拿openid
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->findConfWithKey('AppID')}&secret={$this->findConfWithKey('AppSecret')}&code={$_REQUEST['code']}&grant_type=authorization_code";
        $http = new \Think\Http;
        $respond = $http->httpGet($url);
        //再跳回原来的网址
        $arr = json_decode($respond);
        $getAccess_token = object_array(json_decode($respond));
        //////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////
        //unionid
        \Think\Log::write('++respond ' . $respond . ' |||' . $getAccess_token['access_token']);
        $userData = array();

        $modelUser = D('Center/Users');
        $model = D('Center/Config');
	if(array_key_exists('errcode',$arr)){
		var_dump($arr);
        	exit();   
	}
        if ($model->getScope == 'snsapi_base') {
            $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $getAccess_token['access_token'] . '&openid=' . $arr->openid . '&lang=zh_CN');
            \Think\Log::write('--respond ' . $respond);
            $userData = object_array(json_decode($respond));
            $modelUser->saveMemberData($getAccess_token, $arr->openid, json_decode($respond));
            if (array_key_exists('errcode', $arr)) {
                //重试
                sleep(1);
                $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $getAccess_token['access_token'] . '&openid=' . $arr->openid . '&lang=zh_CN');
                $userData = object_array(json_decode($respond));
            }
        } else {

            $respond = $http->httpGet('https://api.weixin.qq.com/sns/userinfo?access_token=' . $getAccess_token['access_token'] . '&openid=' . $arr->openid . '&lang=zh_CN');
            \Think\Log::write('|||respond ' . $respond);
            $userData = object_array(json_decode($respond));
            $modelUser->saveMemberData($getAccess_token, $arr->openid, json_decode($respond));
        }

        $unionid = M('union_wx')->where(array('unionid' => $getAccess_token['unionid']))->find();
//        if (!$unionid && $arr->openid != '') {
//            M('users')->add(array('openid'=>$arr->openid,'addtime'=>  time(),'logintime'=>  time(),
//                    'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($respond)),
//                    'name'=>$arr->nickname,'sex'=>$arr->sex,'country'=>$arr->country,'province'=>$arr->province,'city'=>$arr->city,
//                'unionid'=>$respond->unionid));
//            
//            $unionid = M('users')->where(array('unionid'=>$arrUnion['unionid']))->find();
//        }
//        \Think\Log::write(json_encode($unionid).'----'.$arrUnion['unionid']);
//        $userInfo = $this->getUserInfoFromWX($arr->openid);
//        $jsonUserInfo = json_decode($userInfo);
        $modelUser = D('Center/Users');
        if (!$unionid) {
            $modelUser->saveUnionData($userData);
        }
        ////


        $arrAdd = array();
        if (array_key_exists("unionid", $getAccess_token) || 'http://' . $this->getDomain() == 'http://tym.yuemai168.com') {

//            if ($isExist) {
//                \Think\Log::write('更新' . $userInfo);
//                $saveArr = array();
//                $saveArr['openid'] = $arr->openid;
//                $saveArr['logintime'] = time();
//
//                $saveArr['sex'] = $jsonUserInfo->sex;
//                $saveArr['country'] = $jsonUserInfo->country;
//                $saveArr['province'] = $jsonUserInfo->province;
//                $saveArr['city'] = $jsonUserInfo->city;
//                if (strlen($unionid['name']) > 0) {
//                    $saveArr['name'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $jsonUserInfo->nickname);
//                }
//                if (strlen($unionid['headimgurl']) > 0) {
//                    $saveArr['headimgurl'] = $jsonUserInfo->headimgurl;
//                }
//                M('users')->where(array('unionid' => $arrUnion['unionid']))->save($saveArr);
//            } else {
//                \Think\Log::write('添加');
//                $newmember = M('users')->add(array('openid' => $arr->openid, 'addtime' => time(), 'logintime' => time(),
////                'wx_info'=>preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo)),
//                    'name' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $jsonUserInfo->nickname), 'sex' => $jsonUserInfo->sex, 'country' => $jsonUserInfo->country, 'province' => $jsonUserInfo->province, 'city' => $jsonUserInfo->city, 'unionid' => $arrUnion['unionid'],
//                    'headimgurl' => $jsonUserInfo->headimgurl));
//            }
        //
        } else {
            header("Location: http://ym.yuemai168.com/index.php?m=web&c=article");
            exit;
        }


        session('openid', $arr->openid);
        $user_data = M('users')->where(array('openid' => $arr->openid))->find();
	header("Location: " . session('currentUrl') . "&openid=" . $user_data['id'] . "");
        exit();
    }

    function getUserInfoFromWX($openid) {
        $token = $this->getToken();
        $http = new \Think\Http;
        $json = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN');
        return $json;
    }

    function getDomain() {
	//S('domain',null);
	//var_dump(S('domain'));
	//exit();
	if (S('domain') != '') {
            $explodeDomain = explode('.',S('domain'));
	    if(count($explodeDomain)>2){
	    	S('domain',null);
	        $this->getDomain();
    	    }
	    return S('domain');
        } else {
            $domain = explode(':', $_SERVER['HTTP_HOST']);
            $domain = $domain[0];
            S('domain',$domain,3600);
            return $domain;
        }
    }

    /**
     * @author jeiry
     * 第一步 用户同意授权，获取code 
     */
    public function weixinGetOpenid() {
        //先拿到code然后到 getOpenId
        //var_dump($this->getDomain());
	//exit();
	session('currentUrl', 'http://' . $this->getDomain() . $_SERVER['REQUEST_URI']);
        $redirect_uri = urlencode('http://' . $this->getDomain() . '/?m=center&a=getOpenId');
        $model = D('Center/Config');
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->findConfWithKey('AppID')}&redirect_uri={$redirect_uri}&response_type=code&scope=" . $model->getScope() . "#wechat_redirect";

        header("Location: " . $url . "");
    }

    function findConfWithKey($key) {
        $conf = M('config')->where('`key` = \'' . $key . '\'')->find();
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
        //正常的时候用
        //get post data, May be due to the different environments
//        $postStr = $GLOBALS['HTTP_RAW_POST_DATA'];
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
        \Think\Log::write('subscribe handleText');
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
            } else if (strtoupper($keyword) == '稿' || strtoupper($keyword) == '稿件' || strtoupper($keyword) == '主持稿' || strtoupper($keyword) == 'GAO') {
                $contentStr = '各类主持稿件,可以点击“查看历史消息”回看本公众号之前的分享；也可以点击主页下栏【我是主持】的“主持学堂”，内含各类主持稿件及学习素材，欢迎关注！谢谢您支持开麦！';
            } else {
                $contentStr = '欢迎关注开麦';
            }
//            $contentStr = $keyword.'<a href=/index.php?m=Home&c=Index&a=index&openid=' . $fromUsername . '>进入广告大厅</a>';
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        } else {
            echo 'Input something...';
        }
    }

    public function handleEvent($object) {
        \Think\Log::write('subscribe handleEvent');
        $fromUsername = $object->FromUserName;

        $key = $object->EventKey;

        $contentStr = '';
        $type = 0;
        switch ($object->Event) {
            case 'subscribe':
                \Think\Log::write('subscribe' . $fromUsername);
                $type = 0;
                $contentStr = '欢迎进入开麦。
商家点击下栏按钮【我找主持】，可进行“发通告”操作，进入页面填写活动信息后发布；也可在“主持人列表”中查阅全国各地主持人的资料。
主持人点击【我是主持】中的“接通告”，可查阅每天实时更新的全国各地通告信息；同时请在“上传个人资料”页面中，输入个人信息、图片、视频，生成个人风采页面。';

                //记录
                if (isset($object->EventKey)) {
                    $this->saveUserInfoWhenSubscribe($fromUsername, $object->EventKey);
                } else {
                    $this->saveUserInfoWhenSubscribe($fromUsername);
                }

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
        $textTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>';
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
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
