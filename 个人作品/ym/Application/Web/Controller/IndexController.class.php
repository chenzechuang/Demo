<?php
namespace Web\Controller;
use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;
 header("Content-Type: text/html; charset=UTF-8");

class IndexController extends Controller {
    const WEBSITE_URL = 'www.kmic168.com';
    const API_URL          =   'http://www.kmic168.com/?m=Center&a=';
    const WEB_URL          =   'http://www.kmic168.com/?m=Web&a=';
    const IMG_URL          =   'http://game.holdyes.com/';
    const RESOURCE_RUL     =   'http://resource.kmic168.com/';

    // 用于签名的公钥和私钥
    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';
    
    
    Public function _initialize()  
    {
        
//        exit('服务器修复中');
//        \LeanCloud\Client::initialize("29f9bXMjtcmOkhtzRCWtVlgM-gzGzoHsz", "FweIIrWPbdjifiD0GwQSEMna", "H9Hn8jcQ7dD95iTlq5AAe46s");

//        session('openid',null);
        if (session('level') == '') {
            session('openid',null);
            session('level',null);
        }
//        echo session('level');
        if (isset($_GET['openid']) && session('openid')==NULL) {
            
            //
            
            $user = M('users')->where('openid = \''.I('get.openid').'\'')->find();
            if ($user) {
                M('users')->where('openid = \''.I('get.openid').'\'')->save(array('logintime'=>  time()));
                session('id',$user['id']);
                session('name',$user['name']);
                session('level',$user['level']);
            }
            /**
            if (!$user) {
                $centerUserData = M('users','center_')->where(array('openid'=>$_GET['openid']))->find();
                $json = json_decode(stripslashes($centerUserData['userinfo']));

                $newmember = M('users')->add(array('openid'=>I('get.openid'),'addtime'=>  time(),'logintime'=>  time(),
                    'wx_info'=>$centerUserData['userinfo'],
                    'name'=>$json->nickname,'sex'=>$json->sex,'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
                if ($newmember) {
                    //如果没记录openid
                    session('id',$newmember);
                    session('name',$newmember['name']);
                    session('level',$newmember['level']);
//                    session('openid',$_GET['openid']);
                    
//                    $centerUserData = M('users','center_')->where(array('openid'=>$_GET['openid']))->find();
//                M('users')->where(array('id'=>session('id')))->save(array('wx_info'=>$centerUserData['userinfo'],
//                    'name'=>$json->nickname,'sex'=>$json->sex,'addtime'=>time(),
//                        'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
//                    if (isWX()) {
//                        $this->leanCloudSignUp(array('openid'=>I('get.openid'),
//                    'nickname'=>$json->nickname,
//                    'sex'=>$json->sex,
//                    'mob'=>$centerUserData['mob'],
//                    'platform'=>'wechat',
//                    'nickname'=>$json->nickname,
//                    'country'=>$json->country,
//                    'province'=>$json->province,
//                    'city'=>$json->city));
//                    }
                    
                }
            }  else {
                //如果记录了openid
                $wxinfoCheck = M('users')->where(array('openid'=>I('get.openid')))->find();
      
                $time = time() - $wxinfoCheck["updatatime"];     
                if($time > 86400){
                    $indexA = A('Center/Index');
                    $userInfo = $indexA->getUserInfoFromWC(I('get.openid')); 
                    $data["updatatime"] = time();
                    $data["wx_info"] = addslashes($userInfo);
                    M('users')->where(array('openid'=>I('get.openid')))->save($data);
                }
                
                if (strlen($wxinfoCheck['wx_info'])<1) {
                   
//                    $centerUserData = M('users','center_')->where(array('openid'=>$_GET['openid']))->find();
//                    $json = json_decode(stripslashes($centerUserData['userinfo']));
                    M('users')->where(array('id'=>session('id')))->save(array(
//                        'wx_info'=>$centerUserData['userinfo'],
                    'name'=>$json->nickname,'sex'=>$json->sex,'addtime'=>time(),
                        'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
                    
                    
                }else{
                }
                
                M('users')->where('openid = \''.I('get.openid').'\'')->save(array('logintime'=>  time()));
                session('id',$user['id']);
                session('name',$user['name']);
                session('level',$user['level']);
                
                
                
                
            }
             * 
             **/
            session('openid',$_GET['openid']);
//            session('name',$user['name']);
                
            //检查vip
            $this->checkExpire($_GET['openid']);
        }
        //
        if ($_GET['openid']=='oph7UwAkJj0rSAnAm74ocmDkQwR4' 
                 or $_GET['openid']=='oph7UwCjVCRfpNBHFuKO0FlmOyrU'
                or $_GET['openid']=='oph7UwPpeCPJR32C5paB_mAYPAbk' or $_GET['openid']=='oph7UwNwLQ1wV-xxB-SA2U6uXp54'
                or $_GET['openid']=='oph7UwGnt82hKvZMOtceGCNmgeEo' or $_GET['openid']=='oph7UwAJwpa5MK67hfXZy1gx6o5k'
                or $_GET['openid']=='oph7UwNK9QIZ50JGAcJ8FONCDXao' or $_GET['openid']=='oph7UwIFaToV3dwGMWZiKOkF0hkg'
                or $_GET['openid']=='oph7UwOZDfJx-pi7L9Kdou_jnSIk'
                or $_GET['openid']=='oph7UwOOXjc0DIfp8V0PabXmaYzU' 
                or $_GET['openid']=='oph7UwPEdPB46DIKNwgh4OUTu3yc' or $_GET['openid']=='oph7UwCCVF96Y8lu7Jh4fPvEe69g'
                or $_GET['openid']=='oph7UwPOJy80FfyUPNPYaAR71fe8' or $_GET['openid']=='oph7UwEY6dsS760Gc1Pe8Wj6178o'
                or $_GET['openid']=='oph7UwB2VfUzNThvK234uXmV3Pmk'
                
                //
                ) {
            session('level',10);
            //前三位不是
        }  else {
            session('level',0);
        }
        //menu
//        S('DICTIONARY',null);
        if (S('DICTIONARY')==null) {
            $dictionary = M('dictionary')->field('id,item_name')->select();
            S('DICTIONARY',$dictionary,86400);
        }
        /////////////leand cloud
//        if (isset($_GET['openid']) && session('openid')!=NULL && isWX()) {
//            $indexA = A('Center/Index');
//            $isE = FALSE;
//            try {
//                User::logIn($_GET['openid'], "abc");
//                $isE = TRUE;
//                $indexA->updataWXinfo(); 
//            } catch (CloudException $ex) {
//    //                            var_dump($ex);
//                $isE = FALSE;
//            } 
//            if ($isE == FALSE) {
//                $indexA->weixinGetOpenid(); 
//            }
//            ////////
//            
//            
//        }
            
//        var_dump(S('jsapi_ticket'));
    }
    public function autoSaveUnionInfo() {
//        $cond['unionid']=array('exp','is null');
//        $cond['openid']=array('exp','LENGTH > 9');
        
        $M = M();

        $count = $M -> query('select id FROM kwx_users where unionid is null and LENGTH(openid) > 9 ');
        $member = $M -> query('select id,openid,unionid FROM kwx_users where unionid is null and LENGTH(openid) > 9 order by id desc limit '.rand(1,count($count)).',1');
        
        $indexA = A('Center/Index');
        $http = new \Think\Http;

        $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$indexA->getToken(TRUE).'&openid='.$member[0]['openid'].'&lang=zh_CN');
        $arr = object_array(json_decode($respond));
        var_dump($arr);
        if (!array_key_exists('errcode', $arr)) {
            $unionid = M('users')->where(array('unionid'=>$arr['unionid']))->field('id')->find();
            var_dump($unionid);
            if ($unionid) {
                exit(json_encode(array('code'=>0)));
            }
            M('union_wx')->add(array('subscribe'=>$arr['subscribe'],
                'openid'=>$arr['openid'],
                'nickname'=>$arr['nickname'],
                'sex'=>$arr['sex'],
                'city'=>$arr['city'],
                'country'=>$arr['country'],
                'province'=>$arr['province'],
                'language'=>$arr['language'],
                'headimgurl'=>$arr['headimgurl'],
                'subscribe_time'=>$arr['subscribe_time'],
                'unionid'=>$arr['unionid'],
                'remark'=>$arr['remark'],
                'groupid'=>$arr['groupid'],
                'tagid_list'=>$arr['tagid_list']));
            M('users')->where(array('openid'=>$arr['openid']))->save(array('unionid'=>$arr['unionid']));
            exit(json_encode(array('code'=>0,'count'=> count($count))));
        }else{
//            sleep(2);
//            $this->autoSaveUnionInfo();
            exit(json_encode(array('code'=>0,'count'=> count($count),'respond'=>$respond,'member'=>$member[0])));
        }
    }
    public function loop() {
        
        $this->display();
    }
    public function saveUnionInfo() {
        if (!isset($_GET['openid'])) {
            exit(json_encode(array('code'=>1)));
        }

        $indexA = A('Center/Index');
        $http = new \Think\Http;
        $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$indexA->getToken(TRUE).'&openid='.I('get.openid').'&lang=zh_CN');
        $arr = object_array(json_decode($respond));
        
        if (!array_key_exists('errcode', $arr)) {
            $unionid = M('users')->where(array('unionid'=>$arr['unionid']))->find();
//            var_dump($unionid);
            if ($unionid) {
                exit(json_encode(array('code'=>0)));
            }
            
            M('union_wx')->add(array('subscribe'=>$arr['subscribe'],
                'openid'=>$arr['openid'],
                'nickname'=>$arr['nickname'],
                'sex'=>$arr['sex'],
                'city'=>$arr['city'],
                'country'=>$arr['country'],
                'province'=>$arr['province'],
                'language'=>$arr['language'],
                'headimgurl'=>$arr['headimgurl'],
                'subscribe_time'=>$arr['subscribe_time'],
                'unionid'=>$arr['unionid'],
                'remark'=>$arr['remark'],
                'groupid'=>$arr['groupid'],
                'tagid_list'=>$arr['tagid_list']));
            M('users')->where(array('openid'=>$arr['openid']))->save(array('unionid'=>$arr['unionid']));
            exit(json_encode(array('code'=>0)));
        }else{
            sleep(2);
            $this->save_union_info();
        }
    }
    function leanCloudSignUp($array){
//        var_dump($array);
        //lc会员
        $userLC = new User();              // 新建 User 对象实例
        $userLC->setUsername($array['openid']);           // 设置用户名
        $userLC->setPassword('abc');     // 设置密码
        if ($array['mob'] != null) {
            $userLC->setMobilePhoneNumber($array['mob']);
        }
        
        $userLC->set("name", $array['nickname']);
        $userLC->set("sex", $array['sex']);
        $userLC->set("country", $array['country']);
        $userLC->set("province", $array['province']);
        $userLC->set("city", $array['city']);
        $userLC->set("city", $array['city']);
//                
        try {
            $userLC->signUp();
            session('LCCurrentUser',User::getCurrentUser());
            session('LCCurrentSessionToken',User::getCurrentSessionToken());
        } catch (CloudException $ex) {
//            var_dump($ex);
            // 如果 LeanCloud 返回错误，这里会抛出异常 CloudException
            // 如用户名已经被注册：202 Username has been taken
        }     
//        exit();
    }
            
    function checkExpire($openid){
        $user = M('users')->where('openid = \''.$openid.'\'')->find();
        if ($user['level']==3 && $user['expire'] != null && $user['expire']<  time()) {
            //会员到期
            $centerUser = M('users','center_')->where('openid = \''.$openid.'\'')->find();
            if ($centerUser['mob'] != null || $centerUser['mob'] !='') {
                M('users')->where('openid = \''.$openid.'\'')->save(array('expire'=>null,'level'=>2));
            }else{
                M('users')->where('openid = \''.$openid.'\'')->save(array('expire'=>null,'level'=>0));
            }
            
        }
    }
    
    public function configApp() {
        header('Content-type: application/json');
        //广告 -1不显示 数字多少显示第几位
        echo json_encode(array('adIndex'=>2));
        exit();
    }
    
    public function sentOfferFromApp() {
        
        $indexA = A('Center/Index');
        if (S(I('get.aid').I('get.uid'))!=null) {
            header('Content-type: application/json');
            echo json_encode(array('code'=>1,'msg'=>'已经报过名了'));
            exit();
        }
        $user_detail = M('user_detail')->where(array('uid'=>I('get.uid')))->find();
        if (!$user_detail) {
            header('Content-type: application/json');
            echo json_encode(array('code'=>3,'msg'=>'您还没有上传个人资料，需求方无法查看您的信息，请在此点击进入上传完成后，即可报名。'));
            exit();
        }
        
        
        
        if (M('offers')->add(array('aid'=>I('get.aid'),'uid'=>I('get.uid'),'price'=>I('get.price')))) {
            S(I('get.aid').I('get.uid'),'1',86400);
            header('Content-type: application/json');
            echo json_encode(array('code'=>0));
            S('count'.I('get.aid'),null);
            //
            $annData = M('announcement')->where(array('id'=>I('get.aid')))->find();
            //
            $add = M('messages')->add(array('fromuid'=>I('get.uid'),
            'touid'=>$annData['signup'],
            'msg'=>'用户:'.$this->userDetail(I('get.uid'))['name'].'给您报价了。价格为'.I('get.price').'元/次。如有需要请回复他。<br/><a href="?m=web&a=artistProfiles&uid='.I('get.uid').'">点击这里查看'.$this->userDetail(I('get.uid'))['name'].'的资料</a>',
            'timeline'=>  time()));
            ////////leancloud message
//            $obj = new Object("message");
//            
//            $obj->set("fromuid", I('get.uid'));
//            $obj->set("touid", $annData['signup']);
//            $obj->set("msg", '用户:'.$this->userDetail(I('get.uid'))['name'].'给您报价了。价格为'.I('get.price').'元/次。如有需要请回复他。<br/><a href="?m=web&a=artistProfiles&uid='.I('get.uid').'">点击这里查看'.$this->userDetail(I('get.uid'))['name'].'的资料</a>');
//            try {
//                $obj->save();
//            } catch (CloudException $ex) {
//            }

        if ($add) {
            
            //
            M('messages')->add(array('fromuid'=>0,
            'touid'=>I('get.uid'),
            'msg'=>'您给'.I('get.title').'报价了。价格为'.I('get.price').'元/次。如果对方有需要会主动和您联系,请留意信息。',
            'timeline'=>  time()));

            ////////leancloud message
//            $obj = new Object("message");
//            
//            $obj->set("fromuid", 0);
//            $obj->set("touid", I('get.uid'));
//            $obj->set("msg", '您给'.I('get.title').'报价了。价格为'.I('get.price').'元/次。如果对方有需要会主动和您联系,请留意信息。');
//            try {
//                $obj->save();
//            } catch (CloudException $ex) {
//            }

            $http = new \Think\Http;
            $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
"touser":"'.$this->userDetail($annData['signup'])['openid'].'",
"msgtype":"text",
"text":
{
     "content":"用户:'.$this->userDetail(I('get.uid'))['name'].'给您报价了,请进[我找主持-我发布的-在您发布的通告下方]查看主持人资料."
}
}');
        }



        }
    }
    
    public function testPage() {
        $indexA = A('Center/Index');
        checkWX();
        
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        echo $_GET['openid'].'<br/>';
        
        $http = new \Think\Http;

        $respond = $http->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$indexA->getToken(TRUE).'&openid='.$_GET['openid'].'&lang=zh_CN');
        $arr = object_array(json_decode($respond));
        var_dump($arr);
    }
    
    public function test(){
        
        $this->display('user_info');
    }
    
    public function index(){
        
        $indexA = A('Center/Index');
        if (isset($_GET['action']) && I('get.action')=='offer') {
            if (S(I('get.aid').session('id'))!=null) {
                echo json_encode(array('code'=>1,'msg'=>'已经报过名了'));
                exit();
            }
            $user_detail = M('user_detail')->where(array('uid'=>session('id')))->find();
            if (!$user_detail) {
                echo json_encode(array('code'=>3,'msg'=>'您还没有上传个人资料，需求方无法查看您的信息，请在此点击进入上传完成后，即可报名。'));
                exit();
            }
            $member = M('users')->field('level')->where(array('id'=>session('id')))->find();
            if ($member['level']<3){
                echo json_encode(array('code'=>2,'msg'=>'您还不是VIP。我们要支付巨额的维护费。注册会员将会是对我们最大的支持。'));
                exit();
            }
            if (M('offers')->add(array('aid'=>I('get.aid'),'uid'=>session('id'),'price'=>I('get.price')))) {
                
                
                S('count'.I('get.aid'),null);
                //
                $query = new Query("announcement");
                $query->equalTo("objectId", I('get.aid'));
                $todo = $query->first();
                $todo->get('signup');
                
//                $annData = M('announcement')->where(array('id'=>I('get.aid')))->find();
                //
                $add = M('messages')->add(array('fromuid'=>session('id'),
                'touid'=>$todo->get('signup'),
                'msg'=>'用户:'.$this->userDetail(session('id'))['name'].'给您报价了。价格为'.I('get.price').'元/次。如有需要请回复他。<br/><a href="?m=web&a=artistProfiles&uid='.session('id').'">点击这里查看'.$this->userDetail(session('id'))['name'].'的资料</a>',
                'timeline'=>  time()));
                ////////leancloud message
//                $obj = new Object("message");
//
//                $obj->set("fromuid", session('id'));
//                $obj->set("touid", $todo->get('signup'));
//                $obj->set("msg", '用户:'.$this->userDetail(session('id'))['name'].'给您报价了。价格为'.I('get.price').'元/次。如有需要请回复他。<br/><a href="?m=web&a=artistProfiles&uid='.session('id').'">点击这里查看'.$this->userDetail(session('id'))['name'].'的资料</a>');
//              
//                try {
//                    $obj->save();
//                } catch (CloudException $ex) {
//                }
                
                S(I('get.aid').session('id'),'1',86400);
                echo json_encode(array('code'=>0));
            if ($add) {
                //
                M('messages')->add(array('fromuid'=>0,
                'touid'=>session('id'),
                'msg'=>'您给'.I('get.title').'报价了。价格为'.I('get.price').'元/次。如果对方有需要会主动和您联系,请留意信息。',
                'timeline'=>  time()));
                
                ////////leancloud message
//                $obj = new Object("message");
//
//                $obj->set("fromuid", 0);
//                $obj->set("touid", session('id'));
//                $obj->set("msg", '您给'.I('get.title').'报价了。价格为'.I('get.price').'元/次。如果对方有需要会主动和您联系,请留意信息。');
//              
//                try {
//                    $obj->save();
//                } catch (CloudException $ex) {
//                }
                
                
                $http = new \Think\Http;
                $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
    "touser":"'.$this->userDetail($todo->get('signup'))['openid'].'",
    "msgtype":"text",
    "text":
    {
         "content":"用户:'.$this->userDetail(session('id'))['name'].'给您报价了,请进[通告中心-我发布的-在您发布的通告下方]查看主持人资料."
    }
}');
            }
                
                
                
            }
            exit();
        }
        if (isset($_GET['action']) && I('get.action')=='signupcount') {
            S('count'.I('get.aid'),null);
            if (S('count'.I('get.aid'))==null) {
                //报价暂时无效
//                $count = M('offers')->where(array('aid'=>I('get.aid')))->count();
                //
                
                $wx_info = object_array(json_decode(stripslashes($this->userDetail(I('get.sign'))['wx_info'])));
                S('count'.I('get.aid'),$wx_info,864000);
                header('Content-type: application/json');
                echo json_encode($wx_info);
            }else{
                echo S('count'.I('get.aid'));
            }
            
            exit();
        }
        
        
//        checkWX();
        
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        $this->assign('signature',  $this->wxSign());
        $this->display();
    }
    
    
    
    //发送联系方式
    public function sentContact() {
        if (isset($_POST)&&I('post.action')=='ann') {
            if (S('limit_log'.session('id').I('post.id'))!=null) {
                exit(json_encode(array('error'=>1,'msg'=>S('limit_log'.session('id').I('post.id')))));
            }
            
            $limit_log = M('limit_log')->where(array('timeline'=>strtotime(date('Y-m-d')),'uid'=>session('id')))->find();
            if (!$limit_log) {
                M('limit_log')->add(array('uid'=>session('id'),'timeline'=>strtotime(date('Y-m-d')),'count'=>1));
                $contact = $this->contactSentToMsg(I('post.id'));
                S('limit_log'.session('id').I('post.id'),$contact,75600);
                exit(json_encode(array('error'=>0,'msg'=>$contact)));
            }else{
                $member = M('users')->field('level')->where(array('id'=>session('id')))->find();
                
                if ($member['level']<3 && $limit_log['count'] < 1) {
                    //不是会员
                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
                    $contact=$this->contactSentToMsg(I('post.id'));
                    S('limit_log'.session('id').I('post.id'),$contact,75600);
                    exit(json_encode(array('error'=>0,'msg'=>$contact)));
                }else if ($member['level']>=3) {
                    //是会员
                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
                    $contact = $this->contactSentToMsg(I('post.id'));
                    S('limit_log'.session('id').I('post.id'),$contact,75600);
                    exit(json_encode(array('error'=>0,'msg'=>$contact)));
                }else{
                    exit(json_encode(array('error'=>2,'msg'=>'普通会员每天只能获取1次联系方式。开麦不易，最好的支持，是您成为VIP。')));
                }
            }
        }
    }
    
    //发送联系方式在app中
    public function sentContactFromApp() {
        header('Content-type: application/json');
        if (isset($_GET['uid'])) {
            if (S('limit_log'.I('get.uid').I('get.aid'))!=null) {
                exit(json_encode(array('error'=>1,'msg'=>S('limit_log'.I('get.uid').I('get.aid')))));
            }
            
            $limit_log = M('limit_log')->where(array('timeline'=>strtotime(date('Y-m-d')),'uid'=>I('get.uid')))->find();
            if (!$limit_log) {
                M('limit_log')->add(array('uid'=>I('get.uid'),'timeline'=>strtotime(date('Y-m-d')),'count'=>1));
                $contact = $this->contactSentToMsg(I('get.aid'));
                S('limit_log'.I('get.uid').I('get.aid'),$contact,75600);
                exit(json_encode(array('code'=>0,'msg'=>$contact)));
            }else{
                $member = M('users')->field('level')->where(array('id'=>I('get.uid')))->find();
                
                if ($member['level']<3 && $limit_log['count'] < 2) {
                    //不是会员
                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
                    $contact=$this->contactSentToMsg(I('get.aid'));
                    S('limit_log'.I('get.uid').I('get.aid'),$contact,75600);
                    exit(json_encode(array('error'=>0,'msg'=>$contact)));
                }else if ($member['level']>=3) {
                    //是会员
                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
                    $contact = $this->contactSentToMsg(I('post.id'));
                    S('limit_log'.I('get.uid').I('get.aid'),$contact,75600);
                    exit(json_encode(array('code'=>0,'msg'=>$contact)));
                }else{
                    exit(json_encode(array('code'=>1,'msg'=>'您还不是VIP，请先到<开麦主持>微信公众号注册成为VIP。普通会员每天只能获取2次联系方式。开麦不易，最好的支持，是您成为VIP。')));
                }
            }
        }
    }
    
    //发送联系方式
    public function sentContactLeancloud() {
//        if (isset($_POST)&&I('post.action')=='ann') {
//            if (S('limit_log'.session('id').I('post.id'))!=null) {
//                exit(json_encode(array('error'=>1,'msg'=>S('limit_log'.session('id').I('post.id')))));
//            }
//            
//            $limit_log = M('limit_log')->where(array('timeline'=>strtotime(date('Y-m-d')),'uid'=>session('id')))->find();
//            if (!$limit_log) {
//                M('limit_log')->add(array('uid'=>session('id'),'timeline'=>strtotime(date('Y-m-d')),'count'=>1));
////                $contact = $this->contactSentToMsgLeancloud(I('post.id'));
//                S('limit_log'.session('id').I('post.id'),$contact,75600);
//                exit(json_encode(array('error'=>0,'msg'=>'')));
//            }else{
//                $member = M('users')->field('level')->where(array('id'=>session('id')))->find();
//                
//                if ($member['level']<3 && $limit_log['count'] < 1) {
//                    //不是会员
//                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
////                    $contact=$this->contactSentToMsgLeancloud(I('post.id'));
//                    S('limit_log'.session('id').I('post.id'),$contact,75600);
//                    exit(json_encode(array('error'=>0,'msg'=>'')));
//                }else if ($member['level']>=3) {
//                    //是会员
//                    M('limit_log')->where('id='.$limit_log['id'])->setInc('count',1);
////                    $contact = $this->contactSentToMsgLeancloud(I('post.id'));
//                    S('limit_log'.session('id').I('post.id'),$contact,75600);
//                    exit(json_encode(array('error'=>0,'msg'=>'')));
//                }else{
//                    exit(json_encode(array('error'=>2,'msg'=>'普通会员每天只能获取1次联系方式。开麦不易，最好的支持，是您成为VIP。')));
//                }
//            }
//        }
    }
    
    function contactSentToMsg($id) {
        $announcement = M('announcement')->where(array('id'=>$id))->find();
        $contact = '';
        if ($announcement['email']!='') {
            $contact .= '邮箱:'.$announcement['email'].' ';
        }
        if ($announcement['echat']!='') {
            $contact .= '微信:'.$announcement['echat'].' ';
        }
        if ($announcement['phone']!='') {
            $contact .= '电话:'.$announcement['phone'].' ';
        }
        if ($announcement['qq']!='') {
            $contact .= 'QQ:'.$announcement['qq'].' ';
        }

        M('messages')->add(array('fromuid'=>112,
        'touid'=>session('id'),
        'msg'=>'通告《<a href="?m=web&a=announcementShare&aid='.$id.'">'.$announcement['title'].'</a>》的联系方式是:<input name="contact" type="text" style="width:100%; border:0" value="'.$contact.'"/>',
        'timeline'=>  time()
            ));
        
        ////////leancloud message
//        $obj = new Object("message");
//
//        $obj->set("fromuid", 112);
//        $obj->set("touid", session('id'));
//        $obj->set("msg",'通告《<a href="?m=web&a=announcementShare&aid='.$id.'">'.$announcement['title'].'</a>》的联系方式是:<input name="contact" type="text" style="width:100%; border:0" value="'.$contact.'"/>');
//
//        try {
//            $obj->save();
//        } catch (CloudException $ex) {
//        }
        return $contact;
    }
    
    
    public function deleteData() {
        if (session('level')==10) {
            if (M('announcement')->where(array('id'=>I('get.id')))->delete()) {
                echo json_encode(array('code'=>0));
            }
        }
    }
    public function deleteArtistData() {
        
        if (session('level')==10) {
            if (M('user_detail')->where(array('id'=>I('get.id')))->save(array('isshow'=>1))) {
                echo json_encode(array('code'=>0));
            }
        }
    }
    
    public function artist(){
        
        if (isset($_GET['action']) && I('get.action')=='offer') {
            if (M('offers')->add(array('aid'=>I('get.aid'),'uid'=>session('id'),'price'=>I('get.price')))) {
                echo json_encode(array('code'=>0));
                S('count'.I('get.aid'),null);
            }
            exit();
        }
        if (isset($_GET['action']) && I('get.action')=='signupcount') {
            if (S('count'.I('get.aid'))==null) {
                $count = M('offers')->where(array('aid'=>I('get.aid')))->count();
                S('count'.I('get.aid'),$count,864000);
                echo $count;
            }else{
                echo S('count'.I('get.aid'));
            }
            
            exit();
        }
        
        
        ///
       // checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        
        
        $this->assign('signature',  $this->wxSign());
        
        $cssCode = '';
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $cssCode = '.ui-scroller {  
            position: fixed;   
            right: 0;   
            bottom: 0;   
            left: 0;  
            top: 0;  
            -webkit-overflow-scrolling: touch;  
            overflow-y: scroll;  
          }  

          .ui-scroller iframe {  
            height: 100%;  
            width: 100%;  
          } ';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
//            echo 'systerm is Android';
        }
        $this->assign('cssCode',$cssCode);
        //
        
        $this->display();
    }
    function wxSign(){
        session('timestamp',  time());
        session('nonceStr',  $this->createNonceStr());
//        echo S('jsapi_ticket')->ticket;
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $string = "jsapi_ticket=".S('jsapi_ticket')->ticket."&noncestr=".session('nonceStr')."&timestamp=".session('timestamp')."&url=".$url;
//        exit(); 
        return sha1($string);
    }
    function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
    
    public function announcement(){
        
        $indexA = A('Center/Index');
        $centerUserData = M('users')->where(array('openid'=>$_GET['openid']))->find();
        

        if (isset($_GET['action'])&& I('get.action')=='addann') {
//            print_r($_REQUEST);
//            $wxinfoCheck = M('users')->where(array('id'=>session('id')))->find();
//            if (strlen($wxinfoCheck['wx_info'])<1) {
////                exit(json_encode(array("error"=>3)));
//                
//                $json = json_decode(stripslashes($centerUserData['userinfo']));
//                M('users')->where(array('id'=>session('id')))->save(array('wx_info'=>$centerUserData['userinfo'],
//                    'name'=>$json->nickname,'sex'=>$json->sex,'addtime'=>time(),
//                        'country'=>$json->country,'province'=>$json->province,'city'=>$json->city));
//            }
            
            //发布一条加3积分
            M('users')->where('id='.$centerUserData['id'])->setInc('point',3);
            
            $pics = explode('[-]', I('post.pics'));
            $http = new \Think\Http;
            
            ////
            
            $arr = array(
                'title'=>I('post.title'),
                'activity_type'=>I('post.activity_type'),
                'activity_time_input'=>I('post.activity_time_input'),
                'activity_time'=>I('post.activity_time'),
                'province'=>I('post.province'),
                'city'=>I('post.city'),
                'area'=>I('post.area'),
                'address'=>I('post.address'),
                'sex'=>I('post.sex'),
                'number'=>I('post.number'),
                'price'=>I('post.price'),
                'price_h'=>I('post.price_h'),
                'original_desc'=>I('post.original_desc'),
                'phone'=>I('post.phone'),
                'qq'=>I('post.qq'),
                'echat'=>I('post.echat'),
                'email'=>I('post.email'),
                'signup'=>$centerUserData['id'],
                'create_time'=>date('Y-m-d H:i:s',time()),
                'modify_time'=>date('Y-m-d H:i:s',time()),
                'platform'=>'WX',
                'img_url'=>  json_encode($pics)
                );
            
            if ($centerUserData['level']==10) {
                $arr['type'] = 1;
            }
            $announcement = M('announcement')->add($arr);
            
            
             //推荐会员
                if ($centerUserData['level']!=10) {
                    //
//                    $announcementData = M('announcement')->where(array('id'=>$announcement))->find();
                    //
                    $province = I('post.province');
                    $condition["province"] = array('like', "%".mb_substr($province,0,2)."%");
                    $condition["level"] = array('eq', 3);
//                    $condition["sex"] = array('neq', 0);//不是男
                    $list = M('users')->where($condition)->order('rand()')->limit('10')->select();
                    foreach ($list as $key => $value) {
                        $name = $value['name']==''?'VIP':$value['name'];

                        M('messages')->add(array('fromuid'=>112,
                    'touid'=>$centerUserData['id'],
                    'msg'=>'VIP系统推荐会员[<a href="?m=web&a=artistProfiles&uid='.$value['id'].'">'.$name.'</a>]可能适合您。',
                    'timeline'=>  time()
                        ));
                        
//                        ////////leancloud message
//                        $obj = new Object("message");
//
//                        $obj->set("fromuid", 112);
//                        $obj->set("touid",session('id'));
//                        $obj->set("msg",'VIP系统推荐会员[<a href="?m=web&a=artistProfiles&uid='.$value['id'].'">'.$name.'</a>]可能适合您。');
//
//                        try {
//                            $obj->save();
//                        } catch (CloudException $ex) {
//                        }
                        
                        //告诉会员
                        M('messages')->add(array('fromuid'=>112,
                    'touid'=>$value['id'],
                    'msg'=>'VIP系统已把您推荐给通告[<a href="?m=web&a=announcementShareLeancloud&aid='.$announcement.'">'.I('post.title').'</a>]',
                    'timeline'=>  time()
                        ));
                        
                        ////////leancloud message
//                        $obj = new Object("message");
//
//                        $obj->set("fromuid", 112);
//                        $obj->set("touid",$value['id']);
//                        $obj->set("msg",'VIP系统已把您推荐给通告[<a href="?m=web&a=announcementShareLeancloud&aid='.$obj->get('objectId').'">'.$obj->get('title').'</a>]');
//
//                        try {
//                            $obj->save();
//                        } catch (CloudException $ex) {
//                        }
                        
                        
                        $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
            "touser":"'.  $value['openid'].'",
            "msgtype":"text",
            "text":
            {
                 "content":"VIP系统已把您推荐给一些通告。请到[用户中心 > 消息通知]中查看。"
            }
        }');
                    }
                        
                }
                    if (count($list)>0) {
                        
                        $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
            "touser":"'.  $_GET['openid'].'",
            "msgtype":"text",
            "text":
            {
                 "content":"VIP系统为您推荐了一些可能适合的主持人。请到[用户中心 > 消息通知]中查看。"
            }
        }');
                    }
                    echo json_encode(array("error"=>0));
                exit();

//添加到leancloud
            
//            $obj = new Object("announcement");
//            
//            $obj->set("title", I('post.title'));
//            $obj->set("activitytype",I('post.activity_type'));
//            $obj->set("activitytimeinput", I('post.activity_time_input'));
//            $obj->set("activitytime", I('post.activity_time'));
//            $obj->set("province", I('post.province'));
//            $obj->set("city", I('post.city'));
//            $obj->set("area", I('post.area'));
//            $obj->set("address", I('post.address'));
//            $obj->set("sex", I('post.sex'));
//            $obj->set("number", I('post.number'));
//            $obj->set("price", (int)I('post.price'));
//            $obj->set("priceh", (int)I('post.price_h'));
//            $obj->set("originaldesc", I('post.original_desc'));
//            $obj->set("phone", I('post.phone'));
//            $obj->set("qq", I('post.qq'));
//            $obj->set("echat", I('post.echat'));
//            $obj->set("email", I('post.email'));
//            $obj->set("signup", session('id'));
//            $obj->set("createtime",date('Y-m-d H:i:s',time()));
//            $obj->set("modifytime", date('Y-m-d H:i:s',time()));
//            $obj->set("platform",'WX');
//            $obj->set("imgurl", json_encode($pics));
//            
//            $obj->set("isshow", '1');
//            $obj->set("status", '0');
//            if (session('level')==10) {
//                $obj->set("type", '1');
//            }else{
//                $obj->set("type", '0');
//            }
//            
//            
//            try {
//                $obj->save();
////                echo $obj->get('platform');
//                
//                
//               
//                    
//            } catch (CloudException $ex) {
//                // CloudException 会被抛出，如果保存失败
//                echo json_encode(array("error"=>1,'msg'=>$ex));
//                exit();
//            }
                
                
            }
            
        
        //
        checkWX();
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        //
        $limit = 0;
        if ($centerUserData['level']==0) {
            $checkData = M('announcement')->where(array('signup'=>$centerUserData['id']))->field('create_time')->order('id desc')->find();
            if ((strtotime($checkData['create_time'])+21600) > time()) {
                $limit = 1;
            }  
        } 
        $this->assign('limit',$limit);
        $this->assign('centerUserData',$centerUserData);    
        $this->assign('signature',  $this->wxSign());
        $this->assign('dictionary',S('DICTIONARY'));
        $this->display();
    }
    
    function base64_to_img( $base64_string, $output_file ) {
        $ifp = fopen( $output_file, "wb" ); 
        fwrite( $ifp, base64_decode( $base64_string) ); 
        fclose( $ifp ); 
        return( $output_file ); 
    }

    public function announcementList(){
        $where = array();
        $key = I('get.page');
        $whereExplode = explode(',', I('get.where'));
        foreach ($whereExplode as $value) {
            $whereExplodeSub = explode('=', $value);
            if ($whereExplodeSub[0]=='sex') {
                $where['kwx_announcement.sex'] = $whereExplodeSub[1];
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            if ($whereExplodeSub[0]=='area') {
                $area = explode('||', $whereExplodeSub[1]);
                if ($area[0] == 'p') {
                    $where['kwx_announcement.province'] = $area[1];
                    
                }else if ($area[0] == 'c'){
                    $where['kwx_announcement.city'] = $area[1];
//                            array('not in',array('北京市','上海市','广东省','浙江省','天津市','四川省','江苏省',''));
//                    $key .= $whereExplodeSub[0].$whereExplodeSub[1];
                }
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            if ($whereExplodeSub[0]=='price') {
                if ($whereExplodeSub[1] == '0') {
                    unset($where['price']);
                }else if ($whereExplodeSub[1] == '1') {
                    $where['price'] = array(between,array(0,1000));
                    $key .= 'price1000';
                }else if ($whereExplodeSub[1] == '1-3') {
                    $where['price'] = array(between,array(1000,3000));
                    $key .= 'price1-3000';
                }else if ($whereExplodeSub[1] == '3-5') {
                    $where['price'] = array(between,array(3000,5000));
                    $key .= 'price3-5000';
                }
//                $where['price'] = array(between,array(1000,2000));
            }
            if ($whereExplodeSub[0]=='keyword') {
                $where['title'] = array('like','%'.$whereExplodeSub[1].'%');
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            
        }
        $where['is_show'] = 1;
        
//        echo implode("",$where);
        S($key,NULL);
        if (S($key)==NULL) {
            $list = M('announcement')->where($where)->limit(I('get.page')*10,10)->field('kwx_announcement.*,kwx_users.wx_info')->join('kwx_users ON kwx_users.id = kwx_announcement.signup')->order('modify_time desc')->select();
            foreach ($list as $key => $value) {
                $list[$key]['price_h'] = $list[$key]['price_h']==null?'':$list[$key]['price_h'];
                $list[$key]['activity_type'] = $list[$key]['activity_type']==null?'':$list[$key]['activity_type'];
                $list[$key]['activity_time'] = $list[$key]['activity_time']==null?'':$list[$key]['activity_time'];
                $list[$key]['img_url'] = $list[$key]['img_url']==null?'':$list[$key]['img_url'];
                $list[$key]['kwx_announcementcol'] = $list[$key]['kwx_announcementcol']==null?'':$list[$key]['kwx_announcementcol'];
                $list[$key]['other_desc'] = $list[$key]['other_desc']==null?'':$list[$key]['other_desc'];
                $list[$key]['price_private'] = $list[$key]['price_private']==null?'':$list[$key]['price_private'];

                $list[$key]['wx_info'] = object_array(json_decode(stripslashes($list[$key]['wx_info'])));
                $list[$key]['wx_info']['headimgurl']=$list[$key]['wx_info']['headimgurl']==''?'':$list[$key]['wx_info']['headimgurl'];
                $list[$key]['wx_info']['nickname']=$list[$key]['wx_info']['nickname']==''?'-':$list[$key]['wx_info']['nickname'];
            }
//            S($key,json_encode($list),300);
            header('Content-type: application/json');
            echo json_encode($list);
        }else{
            echo S($key);
        }
        
    }
    
    public function announcementShare(){
        checkWX();
        $list = M('announcement')->where(array('kwx_announcement.id'=>I('get.aid')))->field('kwx_announcement.*,kwx_users.wx_info')->join('kwx_users ON kwx_users.id = kwx_announcement.signup')->find();
//        foreach ($list as $key => $value) {
        $list['wx_info'] = object_array(json_decode(stripslashes($list['wx_info'])));
        $list['img_url'] = object_array(json_decode($list['img_url']));
        $img_url_arr = '';
        
        foreach ($list['img_url'] as $key => $value) {
            $list['img_url'][$key] = substr(htmlspecialchars_decode($value),1,-1) ;
            $img_url_arr .= substr(htmlspecialchars_decode($value),1,-1).',';
            
        }
        $thumbnail = '/Public/Kmic/noPic.png';
        if (count($list['img_url'])>0) {
            $thumbnail = substr($list['img_url'][0], 1);
        }
        
        $thumbnail = $thumbnail==''?'/Public/Kmic/noPic.png':$thumbnail;
        $this->assign('thumbnail',  $thumbnail);
        
        $this->assign('list',$list);
        $this->assign('img_url_arr',  substr($img_url_arr, 0,-1));
        $this->assign('signature',  $this->wxSign());
        
        $this->assign('shareTitle', substr($list['title'], 0,5));
        $this->display();
    }
    
    public function artistList(){
        $where = array();
        $key = I('get.page').'artist';
        $whereExplode = explode(',', I('get.where'));
        foreach ($whereExplode as $value) {
            $whereExplodeSub = explode('=', $value);
            if ($whereExplodeSub[0]=='sex') {
                $where['kwx_users.sex'] = $whereExplodeSub[1];
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            
            if ($whereExplodeSub[0]=='area') {
                $area = explode('||', $whereExplodeSub[1]);
                if ($area[0] == 'p') {
                    $where['kwx_user_detail.province'] = $area[1];
                    
                }else if ($area[0] == 'c'){
                    $where['kwx_user_detail.city'] = $area[1];
                }
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            
            if ($whereExplodeSub[0]=='price') {
                if ($whereExplodeSub[1] == '0') {
                    unset($where['price']);
                }else if ($whereExplodeSub[1] == '1') {
                    $where['price'] = array(between,array(0,1000));
                    $key .= 'price1000';
                }else if ($whereExplodeSub[1] == '1-3') {
                    $where['price'] = array(between,array(1000,3000));
                    $key .= 'price1-3000';
                }else if ($whereExplodeSub[1] == '3-5') {
                    $where['price'] = array(between,array(3000,5000));
                    $key .= 'price3-5000';
                }
//                $where['price'] = array(between,array(1000,2000));
            }
            
            if ($whereExplodeSub[0]=='keyword') {
                $where['kwx_users.name'] = array('like','%'.$whereExplodeSub[1].'%');
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
        }
        $where['timeline']=array('NEQ','NULL');
        $where['isshow'] = 0;
//        $where['sex']=array('NEQ','NULL');
//        echo implode("",$where);  
      
//        S($key,NULL);
        if (S($key)==NULL) {
            
//               $push_inst = '127,1075,21984,24042,14655,9805,18235,25642,25442,25085,24755,24297,23386,23146,25228,25414,25398,25405,25390,25365,23975,25357,25305,21053,23950,6559,25172,24251,21808,25111,2508025054,24958,22417,24972,22619,24967,23664,24945,24935,24930,14569,20131,23910,24714,24759,12583,24674,24625,24669,24397,24643,24638,23856,24637,24635,24610,24538,236,24528,10985'; 
//               $push_arry = explode(",", $push_inst);      
//               $pusu_id = "";
//               $swallow =I('get.page')*10;
//               $number = I('get.page')*10 +10;
//                for($i=$swallow;$i<$number;$i++){  //每次取出 10 个数组，传化为字符串，用作查询条件
//                   $pusu_id .= $push_arry[$i].",";
//                } 
//                $pusu_id = substr("$pusu_id", 0, -1);
//            if(I('get.page')<6 && I('get.where') ==""){    
//                $where['kwx_user_detail.uid'] = array('in',$pusu_id);               
//                $list = M('user_detail')->where($where)->field('kwx_user_detail.*,kwx_users.wx_info_b,kwx_users.wx_info,kwx_users.headimgurl,kwx_users.level,kwx_users.name,kwx_users.sex')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->order('kwx_user_detail.timeline desc')->select();  
//            }else{ 
//                if(I('get.where') ==""){  //如果查询条件为空的时候，不显示置顶ID
//                    $where['kwx_user_detail.uid'] = array('not in',$pusu_id);
//                } 
//                $list = M('user_detail')->where($where)->field('kwx_user_detail.*,kwx_users.wx_info_b,kwx_users.headimgurl,kwx_users.level,kwx_users.name,kwx_users.sex')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->
//                    limit(I('get.page')*10,10)->order('kwx_user_detail.timeline desc')->select();   
//            }
//            if(I('get.where') ==""){  //如果查询条件为空的时候，不显示置顶ID
//                    $where['kwx_user_detail.uid'] = array('not in',$pusu_id);
//                } 
            $list = M('user_detail')->where($where)->field('kwx_user_detail.*,kwx_users.openid,kwx_users.headimgurl,kwx_users.headimgurl_user,kwx_users.name_user,kwx_users.level,kwx_users.openid,kwx_users.name,kwx_users.sex')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->
                limit(I('get.page')*10,10)->order('kwx_user_detail.timeline desc')->select();  
               

            foreach ($list as $key => $value) {
                $user_additional = M('user_additional')->where(array('uid'=>$list[$key]['uid'],'type'=>'1'))->order('id desc')->select();
//                var_dump(count($user_additional).'--'.$list[$key]['uid']);
                $list[$key]['additional'] = $user_additional;
//                $list[$key]['wx_info'] = object_array(json_decode(stripslashes($list[$key]['wx_info'])));
//                $list[$key]['wx_info']['sex'] = $list[$key]['wx_info']['sex'] == NULL?'0':$list[$key]['wx_info']['sex'];
                $list[$key]['time'] =  date("Y-m-d H:i:s",$list[$key]['timeline']) ;
                $list[$key]['language'] = json_decode($list[$key]['language']);
                $list[$key]['industry'] = json_decode($list[$key]['industry']);
                $list[$key]['skill'] = json_decode($list[$key]['skill']);

                if($value['headimgurl_user'] !=NULL){
                     $list[$key]['headimgurl'] = $value['headimgurl_user'];
                }else if($value['headimgurl'] !=NULL){
                     $list[$key]['headimgurl'] = $value['headimgurl'];
                }else{
                    $list[$key]['headimgurl'] = $list[$key]['additional']['0']['value'];  
                }
                
                if($value['name_user'] !=NULL){
                     $list[$key]['name'] = $value['name_user'];
                }                      
                
               unset($list[$key]['headimgurl_user']);  //删除不显示的字段
               unset($list[$key]['name_user']);
//                $list[$key]['nickname']=
            }
   
            S($key,json_encode($list),300);
            echo json_encode($list);
        }else{
            echo S($key);
        }
        
    }
    
    function userDetail($id){
        return M('users')->where(array('id'=>$id))->find();
    }
    
    public function messages() {
        if (isset($_POST)&&I('post.action')=='send') {
            $data = array('fromuid'=>I('post.fromuid'),
                'touid'=>I('post.touid'),
                'msg'=>I('post.msg'),
                'timeline'=>  time());
            if (isset($_POST['mainid'])) {
                $data['mainid'] = I('post.mainid');
                M('messages')->where(array('id'=>I('post.mainid')))->save(array('timeline'=>time()));
            }
            
            $add = M('messages')->add($data);
            
            ////////leancloud message
//            $obj = new Object("message");
//
//            $obj->set("fromuid", I('post.fromuid'));
//            $obj->set("touid",I('post.touid'));
//            $obj->set("msg",I('post.msg'));
//            if (isset($_POST['mainid'])) {
//                $obj->set("mainid",I('post.mainid'));
////                M('messages')->where(array('id'=>I('post.mainid')))->save(array('timeline'=>time()));
//            }
//            try {
//                $obj->save();
//                ////更新
//                $todo = Object::create("message", I('post.mainid'));
//                $todo->set("timeline",time());
//                $todo->save();
//                
//            } catch (CloudException $ex) {
//            }
            
            if ($add) {
                $indexA = A('Center/Index');
                
                $http = new \Think\Http;
                $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
    "touser":"'.$this->userDetail(I('post.touid'))['openid'].'",
    "msgtype":"text",
    "text":
    {
         "content":"'.$this->userDetail(I('post.fromuid'))['name'].' 给您发来了一条留言,请进[用户中心-消息通知]查看。"
    }
}');
                header('Content-type: application/json');
                exit(json_encode(array("error"=>0,'msg'=>'')));
            }  else {
                header('Content-type: application/json');
                exit(json_encode(array("error"=>1,'msg'=>'写入错误')));
            }
        }else{
            header('Content-type: application/json');
            exit(json_encode(array("error"=>1,'msg'=>'参数错误','request'=>$_REQUEST)));
        }
    }
    
    public function mine(){
        checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        if (isset($_POST) && I('post.action')=='end') {
            M('announcement')->where(array('id'=>I('post.id')))->save(array('status'=>1));
            exit(json_encode(array("error"=>0,'msg'=>'')));
        }
        if (isset($_POST) && I('post.action')=='find') {
            M('announcement')->where(array('id'=>I('post.id')))->save(array('status'=>2));
            exit(json_encode(array("error"=>0,'msg'=>'')));
        }
        if (isset($_POST) && I('post.action')=='delete') {
            M('announcement')->where(array('id'=>I('post.id')))->save(array('is_show'=>0));
            exit(json_encode(array("error"=>0,'msg'=>'')));
        }
        
        if (isset($_GET['list'])) {
            $key = session('id').I('get.page').'signup';
            S($key,null);
            if (S($key)==NULL) {
                $list = M('announcement')->where(array('signup'=>session('id'),'is_show'=>1))->limit('50')->order('id desc')->select();
                
                foreach ($list as $key => $value) {
                    $offers = M('offers')->where(array('aid'=>$value['id']))->order('kwx_offers.id desc')->field('kwx_offers.*,kwx_users.name')->join('kwx_users ON kwx_users.id = kwx_offers.uid')->select();
//                    var_dump($offers);
                    $list[$key]['offers']=$offers;
                }
                
                
//                exit();
                echo json_encode($list);
                S($key,json_encode($list),30);
            }else{
                echo S($key);
            }
            
            exit();
            
        }
        $this->display();
    }
    public function profiles(){
        
        if (isset($_GET['action'])&&I('get.action')=='add') {
//            print_r($_REQUEST);
            $languageExplode = explode('-', I('post.language'));
            $languageExplode=array_filter($languageExplode); 
            $languageJson = json_encode($languageExplode);
            //
            $industryExplode = explode('-', I('post.industry'));
            $industryExplode=array_filter($industryExplode); 
            if (count($industryExplode)>3) {
                exit(json_encode(array("error"=>1,'msg'=>'行业偏向只能选择3个')));
            }
            $industryJson = json_encode($industryExplode);
            
            //
            $skillExplode = explode('-', I('post.skill'));
            $skillExplode=array_filter($skillExplode); 
            $skillJson = json_encode($skillExplode);
            if (M('user_detail')->where(array('uid'=>session('id')))->save(array(
                'province'=>I('post.province'),
                'city'=>I('post.city'),
                'area'=>I('post.area'),
                'age'=>I('post.age'),
                'constellation'=>I('post.constellation'),
                'stature'=>I('post.stature'),
                'language'=>$languageJson,
                'industry'=>$industryJson,
                'skill'=>$skillJson,
                'style'=>I('post.style'),
                'price'=>I('post.price'),
                'school'=>I('post.school'),
                'mob'=>I('post.mob'),
                'qq'=>I('post.qq'),
                'email'=>I('post.email'),
                'wx'=>I('post.wx'),
                'organization'=>I('post.organization'),
                'info'=>I('post.info'),
                'honour'=>I('post.honour'),
                'timeline'=>  time()
            ))) {
                echo json_encode(array("error"=>0)); 
            }
            exit();
        }
        
        if (isset($_GET['upload'])) {
            $base64=file_get_contents("php://input"); //获取输入流
            $base64=json_decode($base64,1);
            $data = $base64['base64'];
            preg_match("/data:image\/(.*);base64,/",$data,$res);
            $ext = $res[1];
            if(!in_array($ext,array("jpg","jpeg","png","gif"))){
                echo json_encode(array("error"=>1));die;
            }
            if ($ext == 'jpeg') {
                $file=time().'.jpg';
            }else{
                $file=time().'.'.$ext;
            }
            
            $savepath = './images/'.$file; 
            $data = preg_replace("/data:image\/(.*);base64,/","",$data);
            
            $image = $this->base64_to_img( $data, $savepath );
            
            //
            if($base64["pic_type"]){
                $pic_type = $base64["pic_type"];
            }else{
                $pic_type = '1';
            }
            if($base64["openid"]){
                $user_data = M('users')->where(array('openid'=>$base64["openid"]))->find(); 
                $uid = $user_data['id'];
            }else{
                $uid = session(id);
            }
            
            $pic_id = M('user_additional')->add(array('uid'=>$uid,'value'=>'http://www.kmic168.com/images/'.$file,'type'=>$pic_type));
            if ($pic_id) {
                echo json_encode(array("error"=>0,'url'=>$savepath,'picId'=>$pic_id));
            }else{
                echo json_encode(array("error"=>1,'url'=>$savepath));
            }
            //
             
            exit();
        }
        if (isset($_POST['delete'])) {
            if (isset($_POST['type']) && I('post.type') == 2) {
                M('user_additional')->where(array('value'=>urldecode(I('post.delete'))))->delete();
                echo json_encode(array("error"=>0)); 
                exit();
            }
            //
            if (unlink(I('post.delete'))) {
                M('user_additional')->where(array('value'=>I('post.delete')))->delete();
                echo json_encode(array("error"=>0)); 
            }else{
                echo json_encode(array("error"=>1)); 
            }
            exit();
        }
        
        if (isset($_GET['action'])&&I('get.action')=='addvideo') {
            if (M('user_additional')->add(array('uid'=>  session(id),'value'=>I('post.video'),'type'=>2))){
                echo json_encode(array("error"=>0)); 
            }else{
                echo json_encode(array("error"=>1)); 
            }
            exit();
        }
        
        $centerUserData = M('users','center_')->where(array('openid'=>$_GET['openid']))->find();
        $this->assign('centerUserData',$centerUserData);  
        
        if (isset($_POST['action'])&&I('post.action')=='top') {
//            var_dump($centerUserData);
            if ($centerUserData['point']>=2) {
                if (M('users','center_')->where('id='.$centerUserData['id'])->setDec('point',2)) {
                    M('user_detail')->where(array('uid'=>session('id')))->save(array('timeline'=>  time()));
                    echo json_encode(array("error"=>0)); 
                }else{
                    echo json_encode(array("error"=>1,'msg'=>'写入错误,请重试')); 
                }
                
            }else{
                echo json_encode(array("error"=>1,'msg'=>'积分不足')); 
            }
            exit();
        }
        //
    //    checkWX();
        
        $indexA = A('Center/Index');
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        //
        $wxData = M('users')->field('wx_info')->where(array('id'=>session('id')))->find();
        $myPrifile = M('user_detail')->where(array('uid'=>session('id')))->find();
        if (!$myPrifile) {
            M('user_detail')->add(array('uid'=>session('id')));
            $myPrifile = M('user_detail')->where(array('uid'=>session('id')))->find();
        }
        //var_dump($wxData);
        $this->assign('wxData', object_array(json_decode(stripslashes($wxData['wx_info']))));
        $this->assign('myPrifile',$myPrifile);
        
        //
        $leng = object_array(json_decode($myPrifile['language']));
        $this->assign('language',$leng);
        $code = '';
        foreach (array_unique($leng) as $key => $value) {
            $code .= 'langArr['.$key.'] =\''.$value.'\'; ';
        }
        $this->assign('jsLanguage',$code);
        //
        $industry = object_array(json_decode($myPrifile['industry']));
        $this->assign('industry',$industry);
        $code = '';
        foreach (array_unique($industry) as $key => $value) {
            $code .= 'industryArr['.$key.'] =\''.$value.'\'; ';
        }
        $this->assign('jsIndustry',$code);
        //
        $skill = object_array(json_decode($myPrifile['skill']));
        $this->assign('skill',$skill);
        $code = '';
        foreach (array_unique($skill) as $key => $value) {
            $code .= 'skillArr['.$skill.'] =\''.$value.'\'; ';
        }
        $this->assign('jsSkill',$code);
        //
        $pics = M('user_additional')->where(array('uid'=>session('id'),'type'=>1))->select();
        $picsCode = '';
        foreach ($pics as $key => $value) {
            $picsCode .= 'var p'.$key.' = "'.$value['value'].'"
                $(".pics").prepend(\'<li id="\'+picNum+\'"><div class="delete" onclick="deletePic(p'.$key.',\'+picNum+\')"><img src="./Public/Kmic/picDelete.png" /></div><img src="'.$value['value'].'" width="103"/></li>\');
            picNum++;
            ';
        }
        $picsCode .= "if (picNum >= 9) {
                                $('.add').css('display','none');
                                $('.file').css('height','420px');
                            }else if (picNum >= 6) {
                                $('.file').css('height','420px');
                            }else if (picNum >= 3) {
                                $('.file').css('height','300px');
                            }";

        $this->assign('jsPics',$picsCode);
        
        $isComplete = FALSE;
        if (count($pics)>0) {
            $isComplete = TRUE;
        }
        $this->assign('isComplete',$isComplete);
        
        //
        //
        $videos = M('user_additional')->where(array('uid'=>session('id'),'type'=>2))->select();
        $videosCode = '';
        foreach ($videos as $key => $value) {
            $videosCode .= 'var v'.$key.' = "'.$value['value'].'"
                $(".video").prepend(\'<li id="v\'+videoNum+\'"><div class="delete" onclick="deleteVideo(v'.$key.',\'+videoNum+\')"><img src="./Public/Kmic/picDelete.png" width="30"/></div><div class="content" onclick="reviewVideo(v'.$key.')"></div></li>\');
            videoNum++;
            ';
        }
        $this->assign('jsVideos',$videosCode);
        //
        //录音
        $voice = M('user_additional')->where(array('uid'=>session('id'),'type'=>3))->find();
        $this->assign('voice',$voice['value']);
        //
        $indexA = A('Center/Index');
        $indexA->getToken(TRUE);
        $this->assign('signature',$this->wxSign());
//        var_dump(S('jsapi_ticket'));
//        exit();
        $this->assign('AppID',  $indexA->findConfWithKey('AppID'));
        $this->display();
    }
    
    public function qiniuUpload(){
        ignore_user_abort(true);
        set_time_limit(0);
        $files = explode('[-]', I('post.link'));
        $files=array_filter($files);
        $fileNameArr = array();
        foreach ($files as $value){
            $value = str_replace("&amp;","&",$value);
            $fileInfo = downloadWeixinFile($value);
            $type = header_byte($fileInfo['header']['content_type']);
            $filename = "record_".time().rand(1111,9999).".".$type;
            saveWeixinFile("./Uploads/".$filename, $fileInfo['body']);
            $fileNameArr[] = $filename;
        }
        
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传Token
        
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        $pipeline = 'kmicMedia';
        //
        $encodedUrlN = array();
        //
        $key = array();
        //如果只有一个就直接转mp3
        if (count($fileNameArr)==1) {
            /////////正常使用  上传时 立刻转mp3
            $filePath = dirname(__FILE__)."/../../../Uploads/".$fileNameArr[0];
            $pfop = "avthumb/mp3";

            //转码完成后回调到业务服务器。（公网可以访问，并相应200 OK）
            $notifyUrl = self::WEB_URL.'Notifi';

            //独立的转码队列：https://portal.qiniu.com/mps/cpipeline
            $pipeline = 'kmicMedia';

            $policy = array(
                'persistentOps' => $pfop,
                'persistentNotifyUrl' => $notifyUrl,
                'persistentPipeline' => $pipeline
            );
            $token = $auth->uploadToken($this->bucket, null, 3600, $policy);
            list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath);
            if ($err !== null) {
            } else if($ret['key']!=''){
                unlink($filePath);
                S($ret['key'],I('post.id'));
            }
        }else{
            foreach ($fileNameArr as $value){
                //上传
                $filePath = dirname(__FILE__)."/../../../Uploads/".$value;

                // 上传到七牛后保存的文件名

                $token = $auth->uploadToken($this->bucket);
                // 调用 UploadManager 的 putFile 方法进行文件的上传
                list($ret, $err) = $uploadMgr->putFile($token, null, $filePath);
                if ($err !== null) {
                } else if($ret['key']!=''){
                    $key[] = $ret['key'];
                    $encodedUrlN[]= base64_encode(self::RESOURCE_RUL.$ret['key']);
                    unlink($filePath);
                    S($ret['key'],I('post.id'));
                }


            }


            //转格式

            //转码完成后通知到你的业务服务器。
            $notifyUrl = self::WEB_URL.'Notifi';
            $pfop = new PersistentFop($auth, $this->bucket, $pipeline, $notifyUrl);
            //要进行转码的转码操作。 http://developer.qiniu.com/docs/v6/api/reference/fop/av/avthumb.html
            $encodedUrlNShift=array_splice($encodedUrlN,1);
            $encodedUrlNFilter=array_filter($encodedUrlNShift);
            $fops = "avconcat/2/format/mp3/".implode('/', $encodedUrlNFilter);
            list($id, $err) = $pfop->execute($key[0], $fops);
            $checkID = '';
            if ($err != null) {
            } else {
                $checkID = $id;
            }

//            //查询转码的进度和状态
//            list($ret, $err) = $pfop->status($checkID);
//            if ($err != null) {
//                \Think\Log::write('qiniuUpload:status '. $err['code']. $err['error']);
//            } else if($ret['key']!=''){
//                \Think\Log::write('qiniuUpload:status '. $ret);
//            }
        }
        exit();
        
        
        // 构建 UploadManager 对象
//        $uploadMgr = new UploadManager();
    }
    public function Notifi() {
        $notifyBody = file_get_contents('php://input');
        
        $json = \Qiniu\json_decode($notifyBody);
        
        
        $deleteFiles = explode('/', $json->items[0]->cmd);
        if (S($json->inputKey)!=null) {
            $this->deleteQiNiuFile($json->inputKey);
            //
            $oldVoice = M('user_additional')->where(array('uid'=>S($json->inputKey),'type'=>3))->find();
            if (!$oldVoice) {
                //加3积分
                $user = M('users')->where(array('id'=>S($json->inputKey)))->find();
                M('users','center_')->where(array('openid'=>$user['openid']))->setInc('point',3);
            }
            
            
            //
            M('user_additional')->where(array('uid'=>S($json->inputKey),'type'=>3))->delete();
            if (M('user_additional')->add(array('uid'=>S($json->inputKey),'value'=>self::RESOURCE_RUL.$json->items[0]->key,'type'=>3))) {
            }
            //这里是 删除前一个7牛上的录音
            $oldVoiceExplode = str_replace("http://resource.kmic168.com/","",$oldVoice['value']);
            $this->deleteQiNiuFile($oldVoiceExplode);
            //还没写

            if (count($deleteFiles)>4) {
                for ($index = 4; $index < count($deleteFiles); $index++) {
                    $qiniuID = explode('/', base64_decode($deleteFiles[$index]));
                    $this->deleteQiNiuFile($qiniuID[3]);

                }
            }
        }
        
        //取出记录
    }
    
    function deleteQiNiuFile($inputKey){
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
    //异步上传音频
    public function asyncAction(){
        
        $data["link"]=$_POST['link'];
        $data["id"]=$_POST['id'];
        $post = http_build_query($data);
        $len = strlen($post);
        //发送
        $host = self::WEBSITE_URL;
        $path = "/?m=web&a=qiniuUpload";
        $fp = fsockopen( $host , 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)\n";
        } else {

            $out = "POST $path HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n";
            $out .= "\r\n";
            $out .= $post."\r\n";
            fwrite($fp, $out);


            fclose($fp);
        }
        exit(json_encode(array("error"=>0)));
    }
    
    
    
    public function recordVoice() {
        
        //
        $indexA = A('Center/Index');
        $indexA->getToken(TRUE);
        $this->assign('signature',$this->wxSign());
        $this->assign('access_token',S('token')->access_token);
        $this->assign('AppID',  $indexA->findConfWithKey('AppID'));
        $this->display();
    }


    public function artistProfiles(){
        
        $indexA = A('Center/Index');
        
        ///
        if (isset($_POST)&&I('post.action')=='msg') {
//            var_dump($_POST);
            $add = M('messages')->add(array('fromuid'=>I('post.fromuid'),
                'touid'=>I('post.touid'),
                'msg'=>I('post.msg'),
                'timeline'=>  time()));
            
            ////////leancloud message
            $obj = new Object("message");

            $obj->set("fromuid", I('post.fromuid'));
            $obj->set("touid",I('post.touid'));
            $obj->set("msg",I('post.msg'));

            try {
                $obj->save();
            } catch (CloudException $ex) {
            }
            
            if ($add) {
                $http = new \Think\Http;
                $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
    "touser":"'.$this->userDetail(I('post.touid'))['openid'].'",
    "msgtype":"text",
    "text":
    {
         "content":"'.$this->userDetail(I('post.fromuid'))['name'].' 给您发来了一条留言,请进[用户中心-消息通知]查看。"
    }
}');
                
                exit(json_encode(array("error"=>0,'msg'=>'')));
            }  else {
                exit(json_encode(array("error"=>1,'msg'=>'写入错误')));
            }
            exit();
        }
        
        
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        ///
        //
        checkWX();
        if(!isset($_GET['openid'])){
            $indexA->weixinGetOpenid();    
            exit();
        }
        ///
        $wxData = M('users')->field('wx_info')->where(array('id'=>I('get.uid')))->find();
        $myPrifile = M('user_detail')->where(array('uid'=>I('get.uid')))->field('kwx_user_detail.*,kwx_users.*')->join('kwx_users on kwx_users.id = kwx_user_detail.uid')->find();
        if (!$myPrifile) {
            M('user_detail')->add(array('uid'=>I('get.uid')));
            $myPrifile = M('user_detail')->where(array('uid'=>I('get.uid')))->find();
        }
        //var_dump($wxData);
        $this->assign('wxData', object_array(json_decode(stripslashes($wxData['wx_info']))));
        $this->assign('myPrifile',$myPrifile);
        
        //
        $leng = object_array(json_decode($myPrifile['language']));
        $this->assign('language',$leng);
        $code = '';
        foreach (array_unique($leng) as $key => $value) {
            $code .= 'langArr['.$key.'] =\''.$value.'\'; ';
        }
        $this->assign('jsLanguage',$code);
        //
        $industry = object_array(json_decode($myPrifile['industry']));
        $this->assign('industry',$industry);
        $code = '';
        foreach (array_unique($industry) as $key => $value) {
            $code .= 'industryArr['.$key.'] =\''.$value.'\'; ';
        }
        $this->assign('jsIndustry',$code);
        //
        $skill = object_array(json_decode($myPrifile['skill']));
        $this->assign('skill',$skill);
        $code = '';
        foreach (array_unique($skill) as $key => $value) {
            $code .= 'skillArr['.$skill.'] =\''.$value.'\'; ';
        }
        $this->assign('jsSkill',$code);
        $this->assign('countSkill', count(array_unique($skill)));
        //
        $pics = M('user_additional')->where(array('uid'=>I('get.uid'),'type'=>1))->select();
        $picsCode = '';
        //
        $arrayPic = array();
        $img_url_arr = '';
        foreach ($pics as $key => $value) {
            $arrayPic[]=  $value['value'];
            $img_url_arr .= substr($value['value'], 2).',';
        }
        $this->assign('img_url_arr',  substr($img_url_arr, 0,-1));
        $this->assign('pics',$arrayPic);
        
        //
        $thumbnail = '/Public/Kmic/noPic.png';
        if (count($arrayPic)>0) {
            $thumbnail = substr($arrayPic[0], 1);
        }
        
        $thumbnail = $thumbnail==''?'/Public/Kmic/noPic.png':$thumbnail;
        $this->assign('thumbnail',  $thumbnail);
        //
        $videos = M('user_additional')->where(array('uid'=>I('get.uid'),'type'=>2))->select();
        $videosCode = '';
        foreach ($videos as $key => $value) {
            $videosCode .= 'var v'.$key.' = "'.$value['value'].'"
                $(".video").prepend(\'<li id="v\'+videoNum+\'"><div class="content" onclick="reviewVideo(v'.$key.')"></div></li>\');
            videoNum++;
            ';
        }
        
        $this->assign('countVideo',count($videos));
        $this->assign('jsVideos',$videosCode);
        
        //录音
        $voice = M('user_additional')->where(array('uid'=>I('get.uid'),'type'=>3))->find();
        $this->assign('voice',$voice['value']);
        
        $indexA->getToken(TRUE);
        $this->assign('signature',$this->wxSign());
        //read
        if (session('a'.I('get.uid'))=='') {
            session('a'.I('get.uid'),'1');
            M('user_detail')->where(array('uid'=>I('get.uid')))->setInc('read_count',1);
        }
        
        //
        $this->display();
    }
    
    
    public function getSession(){
        $data = $this->getUserDataByOpenid(I('post.openid'));
        if ($data) {
            echo $data['name'];
        }else{
            echo FALSE;
        }
    }
    
//    public function checkOpenid() {
//        
//        $indexA = A('Center/Index');
//        if(!session('openid')){
//            $indexA->weixinGetOpenid();
//            exit();
//        }else{
//            $json = '';
//            $userdata = M('users','center_')->where('`openid` = \''.session('openid').'\'')->find();
//            $userInfo = '';
//            if ($userdata) {
//                if (date("Y-m-d",strtotime("-1 day"))<$userInfo['updataTime']) {
//                    $userInfo = $indexA->getUserInfoFromWC(session('openid'));
//                    $json = json_decode($userInfo);
//                    
//                    $save = array();
//                    $save['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo));
//                    $save['updataTime'] = time();
//                    
//                    M('users','center_')->where('`openid` = \''.session('openid').'\'')->save($save);
//                    
//                    echo '一天';
//                }else{
//                    $userInfo = $userdata['userinfo'];
//                    $json = json_decode(stripslashes($userInfo));
//                }
//                
//            }else{
//                $userInfo = $indexA->getUserInfoFromWC(session('openid'));
//                $json = json_decode($userInfo);
//                if (array_key_exists('errcode',$json) && $tryTime < 3) {
//                    S('token',NULL);
//                    $this->getUserInfo();
//
//                    $tryTime++;
//                    exit();
//                }else if($tryTime >=3){
//                    $tryTime = 0;
//                    exit();
//                }else{
//                    $tryTime = 0;
//                }
//
//
//                if (array_key_exists("errcode",$json)) {
////                    $arrJson['code'] = $userInfo;
//                }else{
//                    $add = array();
//                    $add['openid'] = session('openid');
//                    $add['userinfo'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($userInfo));
//                    $add['updataTime'] = time();
//                    $add['platform'] = 'wechat';
//                    if ($json->subscribe == 1) {
//                        $add['subscribeData'] = time();
//                    }
//                    
//                    M('users','center_')->add($add);
//                }
//
//            }
//
//            ///////////////
//            $data = $userInfo;
//            $user = M('users')->where('openid = \''.session('openid').'\'')->find();
//            if ($user) {
//                var_dump($json);
//                $arr = array();
////                $arr['wx_info'] = $data;
//                if ($user['name']==null) {
//                    $arr['name'] = $json->nickname;
//                }
//                if ($user['sex']==null) {
//                    $arr['sex'] = $json->sex;
//                }
//                M('users')->where('id = '.session('id'))->save($arr);
//            }else{
//
//                M('users')->add(array(
////                    'wx_info'=>$data,
//                'name'=>$json->nickname,'sex'=>$json->sex,'addtime'=>time(),'openid'=>$openid));
//            }
////                var_dump($arr['data']);
//            session('name',$json->nickname);
//            if ($data != NULL) {
//                session('wx_info', object_array($userInfo));
//            }else{
//                session('wx_info',NULL);
//            }
//
////            session('level',$arr['level']);
////            session('isban',$arr['isban']);
//                
//            exit();
//        }
//    }
    
    public function getUserData(){
        $openid = isset($_GET['openid'])?I('get.openid'):session('openid');
        $AES = new \Think\AES();
        
//        session('wx_info',NULL);
        if (session('wx_info')==NULL) {
            $arr = authData(1,array('openid'=>$openid,'type'=>'kmic','time'=>time()),self::API_URL.'getUserInfo');
//            var_dump($arr);
//            $arr = json_encode($arr);
//            print_r($arr['data']);
            if ($arr['code']==0) {
                $data = addslashes(json_encode($arr['data']))=='null'?null:addslashes(json_encode($arr['data']));
                if (M('users')->where('id = '.session('id'))->find()) {
                    M('users')->where('id = '.session('id'))->save(array('wx_info'=>$data));
                }else{
                    
                    M('users')->add(array('wx_info'=>$data,
                    'name'=>$arr['data']->nickname,'sex'=>$arr['data']->sex,'addtime'=>time(),'openid'=>$openid,
                        'country'=>$arr['data']->country,'province'=>$arr['data']->province,'city'=>$arr['data']->city));
                }
//                var_dump($arr['data']);
                session('name',$arr['data']->nickname);
                if ($data != NULL) {
                    session('wx_info', object_array($arr['data']));
                }else{
                    session('wx_info',NULL);
                }
                
                session('level',$arr['level']);
                session('isban',$arr['isban']);
//                var_dump(session('wx_info'));
                //
                //
                echo json_encode(array('result'=>TRUE,'headimgurl'=>$arr['data']->headimgurl,'nickname'=>$arr['data']->nickname,'levelTitle'=>$arr['levelTitle'],'level'=>$arr['level'],'isban'=>$arr['isban'],'ismob'=>$arr['ismob'],'data'=>  $AES->encode(json_encode(array('openid'=>$openid,'url'=>'http://www.baidu.com')))));
            }
        }  else {
//            M('users')->where('`openid` = \''.$openid.'\'')->find();
//                session('name',$arr['data']->nickname);
//                session('wx_info',stripslashes($arr['data']));
            echo json_encode(array('result'=>TRUE,'headimgurl'=>$arr['data']->headimgurl,'nickname'=>$arr['data']->nickname,'levelTitle'=>$arr['levelTitle'],'level'=>$arr['level'],'isban'=>$arr['isban'],'ismob'=>TRUE,'data'=>  $AES->encode(json_encode(array('openid'=>$openid,'url'=>'http://www.baidu.com')))));

        }
        
    }
    
    public function area() {
        if (isset($_GET['action'])) {
            $province = M('city')->where(array('fatherid'=>I('get.fatherid')))->select();
            exit(json_encode($province));
        }
        $province = M('province')->select();
        $this->assign('province',$province);
        $this->display();
    }
    
    function getUserDataByOpenid($openid){
        $data = M('users')->where('openid = \''.$openid.'\'')->find();
        return $data;
    }
    public function getUserDataJS(){
        $data = $this->getUserDataByOpenid(I('get.openid'));
        if ($data) {
            echo 'var nickname = "'.$data['name'].'";';
        }else{
            echo 'var nickname = "";';
        }
    }
    
    function getUserDataWhenNull($openid){
        $arr = authData(1,array('openid'=>$openid,'type'=>'yyqg','time'=>time()),self::USER_AUTH_URL);
            
        if ($arr['code']==0) {
            $ex = M('users')->where('openid = \''.$openid.'\'')->find();
//            print_r($openid);
            if ($ex) {
                M('users')->where('id = \''.$openid.'\'')->save(array('wx_info'=>addslashes(json_encode($arr['data'])),
                'name'=>$arr['data']->nickname));
            }else{
                M('users')->add(array('wx_info'=>addslashes(json_encode($arr['data'])),
                'name'=>$arr['data']->nickname,'openid'=>$openid,'addtime'=>  time()));
            }
            
            return TRUE;
        }else{
            return FALSE;
        }
    }
}