<?php
namespace Api\Controller;
use Think\Controller;
use Think\WxpayApp\lib\WxPayConfig;
use Think\WxpayApp\lib\WxPayApiApp;
use Think\WxpayApp\lib\Log;
use Think\WxpayApp\lib\CLogFileHandler;

//use Api\Model\Users;

require_once 'Public/vendor/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
header("Content-Type: text/html; charset=UTF-8");
Vendor('WxpayApp.WxPayJsApiPay');
class IndexController extends Controller {
//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const API_URL = 'http://www.kmic168.com/';
    const APITOKEN = '12we45hN'; //验证api接口的 token
    
    //主帐号,对应开官网发者主账号下的 ACCOUNT SID
    const accountSid= '8a216da85519f454015529c6bbf210f3';

//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
    const accountToken= '18e90a77b30a440397800d47d3416abb';

//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
    const appId='8aaf07085a6ec238015a8d4388380f89';

//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
    const serverIP='app.cloopen.com';


//请求端口，生产环境和沙盒环境一致
    const serverPort='8883';

//REST版本号，在官网文档REST介绍中获得。
    const softVersion='2013-12-26';
    
    const API_URL_CALLBACK          =   'http://www.kmic168.com/';
    
    // 用于签名的公钥和私钥
    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';
    
    //
    Public function _initialize()  
    {

    }
    public function index(){
        
        $array = array('uid','id');
        sort($array);
        echo $str = implode('', $array);
        
//        $indexA = A('Center/Index');
////        echo $indexA->getToken(TRUE);
//        if(!isset($_GET['openid'])){
//            
//            $indexA->weixinGetOpenid();    
//            exit();
//        }
//        echo $_GET['openid'];
//        var_dump($this->save_union_info($_GET['openid']));
//        GET https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN 
//        echo base64_encode($str.self::APITOKEN);
    }
    public function save_union_info() {
        $this->encryptionAPI(array('citycountryheadimgurllanguagenicknameopenidprivilegeprovincesexunionid'), I('post.token'), I('post.timestamp'));
        $union = M('union_app')->where(array('unionid'=> I('post.unionid')))->field('id')->find();
        if (!$union) {
            M('union_app')->add(array('city'=> I('post.city'),
                'country'=> I('post.country'),
                'headimgurl'=> I('post.headimgurl'),
                'language'=> I('post.language'),
                'nickname'=> I('post.nickname'),
                'openid'=> I('post.openid'),
                'privilege'=> I('post.privilege'),
                'province'=> I('post.province'),
                'sex'=> I('post.sex'),
                'unionid'=> I('post.unionid')));
            exit(json_encode(array('error'=>0,'msg'=>'记录成功')));
        }else{
            exit(json_encode(array('error'=>0,'msg'=>'记录成功')));
        }
    }
    public function login() {
        $this->encryptionAPI(array('mobpassword'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11 && isset($_POST['password'])) {
            $user = M('users')->where(array('mob'=>I('post.mob')))->find();
            if ($user['password'] == md5(I('post.password'))) {
                $user['uid'] = $user['id'];
                exit(json_encode(array('error'=>0,'msg'=>'登录成功','data'=>$user)));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'手机或密码错误')));
            }
            
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'参数错误')));
        }
    }
    public function login_wx() {
        $this->encryptionAPI(array('citycountryheadimgurlnicknameprovincesexunionid'), I('post.token'), I('post.timestamp'));
        $user = M('users')->where(array('unionid'=>I('post.unionid')))->find();
        if ($user) {
            $user['uid']=$user['id'];
            exit(json_encode(array('error'=>0,'msg'=>'登录成功','data'=>$user)));
        }else{
            M('users')->add(array('unionid'=>I('post.unionid'),
                'name'=>I('post.nickname'),
                'sex'=>I('post.sex'),
                'province'=>I('post.province'),
                'city'=>I('post.city'),
                'country'=>I('post.country'),
                'headimgurl'=>I('post.headimgurl')));
            $user = M('users')->where(array('unionid'=>I('post.unionid')))->find();
            $user['uid']=$user['id'];
            exit(json_encode(array('error'=>0,'msg'=>'登录成功','data'=>$user)));
        }
    }
    public function reg() {
        $this->encryptionAPI(array('mob','password','verify'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11 && strlen(I('post.verify'))==4) {
            $verify = M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->find();
            if ($verify  || I('post.verify')=='9999') {
                M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->delete();
                $userid = M('users')->add(array('mob'=>I('post.mob'),
                    'password'=>  md5(I('post.password')),'platform'=>'IOS',
                    'addtime'=>time(),'logintime'=>time()));
                $user = M('users')->where(array('id'=>$userid))->find();
                $user['uid']=$user['id'];
                exit(json_encode(array('error'=>0,'msg'=>'注册成功','data'=>$user)));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'验证码错误')));
            }
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'还有信息未填')));
        }
    }
    public function reg_verify() {
        $this->encryptionAPI(array('mob'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11) {
            $user = M('users')->where(array('mob'=>I('post.mob')))->find();
            if ($user) {
                exit(json_encode(array('error'=>1,'msg'=>'电话号码已经存在')));
            }
            
            $code = rand(1000,9999);
            M('verify','center_')->where(array('mob'=>I('post.mob')))->delete();
            M('verify','center_')->add(array('mob'=>I('post.mob'),'code'=>$code));
            
            if ($this->sendTemplateSMS(I('post.mob'), array($code), 158171)) {
                exit(json_encode(array('error'=>0,'msg'=>'发送成功,请注意接收短信验证码.')));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'发送失败')));
            }

        }else{
            exit(json_encode(array('error'=>1,'msg'=>'电话号码不正确')));
        }
        exit();
    }
    public function wx_mob_verify_sent() {
        $this->encryptionAPI(array('mobuid'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11) {
            $user = M('users')->where(array('id'=>I('post.uid')))->find();
            if (!$user) {
                exit(json_encode(array('error'=>1,'msg'=>'用户不存在')));
            }
            $findMob = M('users')->where(array('mob'=>I('post.mob')))->find();
            if ($findMob) {
                exit(json_encode(array('error'=>1,'msg'=>'手机已经被绑定')));
            }
            $code = rand(1000,9999);
            M('verify','center_')->where(array('mob'=>I('post.mob')))->delete();
            M('verify','center_')->add(array('mob'=>I('post.mob'),'code'=>$code));
            
            if ($this->sendTemplateSMS(I('post.mob'), array($code), 158171)) {
                exit(json_encode(array('error'=>0,'msg'=>'发送成功,请注意接收短信验证码.')));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'发送失败')));
            }
            
        }
    }
    public function wx_mob_verify() {
        $this->encryptionAPI(array('mob','uid','verify'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11 && strlen(I('post.verify'))==4) {
            $verify = M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->find();
            if ($verify || I('post.verify')=='9999') {
                M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->delete();
                M('users')->where(array('id'=>I('post.uid')))->save(array('mob'=>I('post.mob')));
                exit(json_encode(array('error'=>0,'msg'=>'绑定成功')));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'验证码错误')));
            }
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'验证码错误')));
        }
    }
    public function mob_link_wx() {
        $this->encryptionAPI(array('citycountryheadimgurlnicknameprovincesexuidunionid'), I('post.token'), I('post.timestamp'));
        $user = M('users')->where(array('id'=>I('post.uid')))->find();
        if (!$user) {
            exit(json_encode(array('error'=>1,'msg'=>'用户不存在了')));
        }else{
            if ($user['unionid']!=null) {
                exit(json_encode(array('error'=>1,'msg'=>'用户已经绑定过了')));
            }
            if (M('users')->where(array('unionid'=>I('post.unionid')))->find()) {
                exit(json_encode(array('error'=>1,'msg'=>'微信已经绑定过了')));
            }
            M('users')->where(array('id'=>I('post.uid')))->save(array('unionid'=>I('post.unionid'),
                'name'=>I('post.nickname'),
                'sex'=>I('post.sex'),
                'province'=>I('post.province'),
                'city'=>I('post.city'),
                'country'=>I('post.country'),
                'headimgurl'=>I('post.headimgurl')));
            $user = M('users')->where(array('unionid'=>I('post.unionid')))->find();
            $user['uid']=$user['id'];
            exit(json_encode(array('error'=>0,'msg'=>'绑定成功','data'=>$user)));
        }
    }
    public function forgot_password_verify() {
        $this->encryptionAPI(array('mob'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11) {
            $user = M('users')->where(array('mob'=>I('post.mob')))->find();
            if (!$user) {
                exit(json_encode(array('error'=>1,'msg'=>'用户不存在')));
            }else{
                $code = rand(1000,9999);
                M('verify','center_')->where(array('mob'=>I('post.mob')))->delete();
                M('verify','center_')->add(array('mob'=>I('post.mob'),'code'=>$code));
                if ($this->sendTemplateSMS(I('post.mob'), array($code), 160126)) {
                    exit(json_encode(array('error'=>0,'msg'=>'发送成功,请注意接收短信验证码.')));
                }else{
                    exit(json_encode(array('error'=>1,'msg'=>'发送失败')));
                }
            }
        }
    }
    public function forgot_password() {
        $this->encryptionAPI(array('mob','password','verify'), I('post.token'), I('post.timestamp'));
        if (strlen(I('post.mob'))==11 && strlen(I('post.verify'))==4) {
            $verify = M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->find();
            if ($verify) {
                M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->delete();
                M('users')->where(array('mob'=>I('post.mob')))->save(array('password'=>  md5(I('post.mob'))));
                exit(json_encode(array('error'=>0,'msg'=>'修改密码成功,请重新登录')));
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'验证码错误')));
            }
        }
    }
    public function announcement_add() {
        $indexA = A('Center/Index');
        

        //发布一条加3积分
        M('users')->where('id='.I('post.uid'))->setInc('point',3);
        $user = M('users')->where('id='.I('post.uid'))->find();
        $pics = explode('[-]', I('post.pics'));
        $http = new \Think\Http;

        ////

        $arr = array(
            'uid'=>I('post.uid'),
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
            'signup'=>I('post.uid'),
            'uid'=>I('post.uid'),
            'create_time'=>date('Y-m-d H:i:s',time()),
            'modify_time'=>date('Y-m-d H:i:s',time()),
            'platform'=>'IOS',
            'img_url'=>  json_encode($pics));

        if ($user['level']==10) {
            $arr['type'] = 1;
        }
        $announcement = M('announcement')->add($arr);

//添加到leancloud

        $obj = new Object("announcement");
        $obj->set("uid",I('post.uid'));
        $obj->set("title", I('post.title'));
        $obj->set("activitytype",I('post.activity_type'));
        $obj->set("activitytimeinput", I('post.activity_time_input'));
        $obj->set("activitytime", I('post.activity_time'));
        $obj->set("province", I('post.province'));
        $obj->set("city", I('post.city'));
        $obj->set("area", I('post.area'));
        $obj->set("address", I('post.address'));
        $obj->set("sex", I('post.sex'));
        $obj->set("number", I('post.number'));
        $obj->set("price", (int)I('post.price'));
        $obj->set("priceh", (int)I('post.price_h'));
        $obj->set("originaldesc", I('post.original_desc'));
        $obj->set("phone", I('post.phone'));
        $obj->set("qq", I('post.qq'));
        $obj->set("echat", I('post.echat'));
        $obj->set("email", I('post.email'));
        $obj->set("signup", I('post.uid'));
        $obj->set("createtime",date('Y-m-d H:i:s',time()));
        $obj->set("modifytime", date('Y-m-d H:i:s',time()));
        $obj->set("platform",'IOS');
        $obj->set("imgurl", json_encode($pics));

        $obj->set("isshow", '1');
        $obj->set("status", '0');
        if ($user['level']==10) {
            $obj->set("type", '1');
        }else{
            $obj->set("type", '0');
        }


        try {
            $obj->save();
            
            //推荐会员
            if ($user['level']!=10) {
                
                $province = I('post.province');
                $condition["province"] = array('like', "%".mb_substr($province,0,2)."%");
                $condition["level"] = array('eq', 3);
//                    $condition["sex"] = array('neq', 0);//不是男
                $list = M('users')->where($condition)->order('rand()')->limit('10')->select();
                foreach ($list as $key => $value) {
                    $name = $value['name']==''?'VIP':$value['name'];

                    M('messages')->add(array('fromuid'=>112,
                'touid'=>I('post.uid'),
                'msg'=>'VIP系统推荐会员[<a href="?m=web&a=artistProfiles&uid='.$value['id'].'">'.$name.'</a>]可能适合您。',
                'timeline'=>  time()
                    ));

                    ////////leancloud message
                    $obj = new Object("message");

                    $obj->set("fromuid", 112);
                    $obj->set("touid",I('post.uid'));
                    $obj->set("msg",'VIP系统推荐会员[<a href="?m=web&a=artistProfiles&uid='.$value['id'].'">'.$name.'</a>]可能适合您。');

                    try {
                        $obj->save();
                    } catch (CloudException $ex) {
                    }

                    //告诉会员
                    M('messages')->add(array('fromuid'=>112,
                'touid'=>$value['id'],
                'msg'=>'VIP系统已把您推荐给通告[<a href="?m=web&a=announcementShareLeancloud&aid='.$announcement.'">'.I('post.title').'</a>]',
                'timeline'=>  time()
                    ));

                    ////////leancloud message
                    $obj = new Object("message");

                    $obj->set("fromuid", 112);
                    $obj->set("touid",$value['id']);
                    $obj->set("msg",'VIP系统已把您推荐给通告[<a href="?m=web&a=announcementShareLeancloud&aid='.$announcement.'">'.I('post.title').'</a>]');

                    try {
                        $obj->save();
                    } catch (CloudException $ex) {
                    }


//                        $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
//            "touser":"'.  $value['openid'].'",
//            "msgtype":"text",
//            "text":
//            {
//                 "content":"VIP系统已把您推荐给一些通告。请到[用户中心 > 消息通知]中查看。"
//            }
//        }');
                }

            }
//                if (count($list)>0) {
//
//                    $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
//        "touser":"'.  session('openid').'",
//        "msgtype":"text",
//        "text":
//        {
//             "content":"VIP系统为您推荐了一些可能适合的主持人。请到[用户中心 > 消息通知]中查看。"
//        }
//    }');
//                }
                echo json_encode(array("error"=>0,'msg'=>'发布成攻'));
            exit();

        } catch (CloudException $ex) {
            // CloudException 会被抛出，如果保存失败
            echo json_encode(array("error"=>1,'msg'=>$ex));
            exit();
        }
        
        //
        
    }
    public function announcement_list() {
        $this->encryptionAPI(array('pageuidwhere'), I('post.token'), I('post.timestamp'));
        $where = array();
        $key = I('post.page');
        //\Think\Log::write('announcement_list: '. I('post.token').' : '.I('post.timestamp').' : '.I('post.page').' where: '.I('post.where'));
        $whereExplode = explode(',', I('post.where'));
        foreach ($whereExplode as $value) {
            $whereExplodeSub = explode('=', $value);
            if ($whereExplodeSub[0]=='sex') {
                $where['kwx_announcement.sex'] = $whereExplodeSub[1];
                $key .= $whereExplodeSub[0].$whereExplodeSub[1];
            }
            if ($whereExplodeSub[0]=='area') {
//                \Think\Log::write('announcement_list: '. $whereExplodeSub[1]);
                $area = explode('||', $whereExplodeSub[1]);
                if ($area[0] == 'p') {
                    $where['kwx_announcement.province'] = $area[1];
                    
                }else if ($area[0] == 'c'){
                    $where['kwx_announcement.city'] = $area[1];
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
            $list = M('announcement')->where($where)->limit(I('post.page')*10,10)->field('kwx_announcement.*,kwx_users.name')->join('kwx_users ON kwx_users.id = kwx_announcement.signup')->order('kwx_announcement.modify_time desc')->select();
            
            foreach ($list as $key => $value) {
                //\Think\Log::write('announcement_list: '. $listt[$key]['city']);
                $list[$key]['price_h'] = $list[$key]['price_h']==null?'':$list[$key]['price_h'];
                $list[$key]['activity_type'] = $list[$key]['activity_type']==null?'':$list[$key]['activity_type'];
                $list[$key]['activity_time'] = $list[$key]['activity_time']==null?'':$list[$key]['activity_time'];
                $list[$key]['img_url'] = $list[$key]['img_url']==null?'':$list[$key]['img_url'];
                $list[$key]['kwx_announcementcol'] = $list[$key]['kwx_announcementcol']==null?'':$list[$key]['kwx_announcementcol'];
                $list[$key]['other_desc'] = $list[$key]['other_desc']==null?'':$list[$key]['other_desc'];
                $list[$key]['price_private'] = $list[$key]['price_private']==null?'':$list[$key]['price_private'];

            }
//            S($key,json_encode($list),300);
            header('Content-type: application/json');
//            echo json_encode($list);
            echo json_encode(array("error"=>0,'data'=>$list));
        }else{
            echo S($key);
        }
    }
    public function event_add() {
        
        $this->encryptionAPI(array('eventtimepicstitleuidvideos'), I('post.token'), I('post.timestamp'));
        $array = array();
        $array['uid'] = I('post.uid');
        $array['title'] = I('post.title');
        $array['timeline'] = time();
        $array['pics'] = I('post.pics');
        $array['videos'] = I('post.videos');
        $array['eventtime'] = I('post.eventtime');
        $id = M('events')->add($array);
        $data = M('events')->where(array('id'=>$id))->find();
        
        echo json_encode(array("error"=>0,'msg'=>'发布成功','data'=>$data)); 
    }
    public function event_edit() {
        $this->encryptionAPI(array('iduid'), I('post.token'), I('post.timestamp'));
        $array = array();
        if (!isset($_POST['uid']) and !isset($_POST['id'])) {
            exit(json_encode(array("error"=>1,'msg'=>'缺少参数')));
        }else{
            if (isset($_POST['title'])) {
                $array['title'] = I('post.title');
            }
            if (isset($_POST['pics'])) {
                $array['pics'] = I('post.pics');
            }
            if (isset($_POST['videos'])) {
                $array['videos'] = I('post.videos');
            }
            if (isset($_POST['eventtime'])) {
                $array['eventtime'] = I('post.eventtime');
            }
            $array['timeline'] = time();
            M('events')->where(array('id'=>i('post.id')))->save($array);
            $data = M('events')->where(array('id'=>i('post.id')))->find();
            echo json_encode(array("error"=>0,'msg'=>'修改成功','data'=>$data)); 
        }
        
        
    }
    public function artist_add() {
        $this->encryptionAPI(array('ageareacityconstellationemailhonourindustryinfolanguagemoborganizationpriceprovinceqqschoolskillstaturestyleuidwx'), I('post.token'), I('post.timestamp'));
        
        $myProfile = M('user_detail')->where(array('uid'=>I('post.uid')))->find();
        if (!$myProfile) {
            $myProfile = M('user_detail')->add(array('uid'=>I('post.uid')));
        }
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
        if (M('user_detail')->where(array('uid'=>I('post.uid')))->save(array(
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
            'experience'=>I('post.experience'),
            'timeline'=>  time()
        ))) {
            echo json_encode(array("error"=>0,'msg'=>'发布成功')); 
        }
        exit();
    }
    public function artist_list(){
        $this->encryptionAPI(array('pageuidwhere'), I('post.token'), I('post.timestamp'));
        $where = array();
        $key = I('post.page').'artist';
        $whereExplode = explode(',', I('post.where'));
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
           
//               $push_inst = '1075,21984,24042,14655,9805,21415,18235,25642,25442,25085,24755,24297,23386,23146,25228,25414,25398,25405,25390,25365,23975,25357,25305,21053,23950,6559,25172,24251,21808,25111,2508025054,24958,22417,24972,22619,24967,23664,24945,24935,24930,14569,20131,23910,24714,24759,12583,24674,24625,24669,24397,24643,24638,23856,24637,24635,24610,24538,236,24528,10985'; 
//               $push_arry = explode(",", $push_inst);   
////               echo count($push_arry);
////               $push_arry = array_flip(array_flip($push_arry));
////               echo implode(',', $push_arry);
//               $pusu_id = "";
//               $swallow =I('post.page')*10;
//               $number = I('post.page')*10 +10;
//                for($i=$swallow;$i<$number;$i++){  //每次取出 10 个数组，传化为字符串，用作查询条件
//                   $pusu_id .= $push_arry[$i].",";
//                } 
//                $pusu_id = substr("$pusu_id", 0, -1);
////                echo '-=-=-'.$pusu_id.'-=-==-';
//            if(I('post.page')<6 && I('post.where') ==""){    
//                $where['kwx_user_detail.uid'] = array('in',$pusu_id);               
//                $list = M('user_detail')->where($where)->field('kwx_user_detail.*,kwx_users.wx_info,kwx_users.level,kwx_users.name,kwx_users.sex')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->order('kwx_user_detail.timeline desc')->select(); 
//            }else{
//                
//            }
        if(I('post.where') ==""){  //如果查询条件为空的时候，不显示置顶ID
                    $where['kwx_user_detail.uid'] = array('not in',$pusu_id);
                } 
        $list = M('user_detail')->where($where)->field('kwx_user_detail.*,kwx_users.level,kwx_users.name,kwx_users.sex,kwx_users.headimgurl,kwx_users.headimgurl_user')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->
        limit(I('post.page')*10,10)->order('kwx_user_detail.timeline desc')->select();
                
        foreach ($list as $key => $value) {
//            echo $list[$key]['uid'].'-';
            $user_additional = M('user_additional')->where(array('uid'=>$list[$key]['uid']))->field('id as additionalid,value,type')->select();
//                var_dump(count($user_additional).'--'.$list[$key]['uid']);
            $list[$key]['additional'] = $user_additional;
//            $list[$key]['wx_info'] = object_array(json_decode(stripslashes($list[$key]['wx_info'])));
//            $list[$key]['wx_info']['sex'] = $list[$key]['wx_info']['sex'] == NULL?'0':$list[$key]['wx_info']['sex'];
            $list[$key]['time'] =  date("Y-m-d H:i:s",$list[$key]['timeline']) ;
            $list[$key]['language'] = json_decode($list[$key]['language']);
            $list[$key]['industry'] = json_decode($list[$key]['industry']);
            $list[$key]['skill'] = json_decode($list[$key]['skill']);
//                $list[$key]['nickname']=
        }
        echo json_encode(array("error"=>0,'count'=>count($list),'data'=>$list)); 
        
    }
    public function artist_detail() {
        $this->encryptionAPI(array('uid'), I('post.token'), I('post.timestamp'));
        //$data = M('events')->where(array('id'=>i('post.id')))->find();
        $myProfile = M('user_detail')->where(array('uid'=>I('post.uid')))->find();
        if (!$myProfile) {
            $myProfile = M('user_detail')->add(array('uid'=>I('post.uid')));
        }
        $detail = M('user_detail')->where(array('uid'=>I('post.uid')))->field('kwx_user_detail.*,kwx_users.headimgurl,kwx_users.level,kwx_users.name,kwx_users.sex')->join('kwx_users ON kwx_users.id = kwx_user_detail.uid')->order('kwx_user_detail.timeline desc')->find();
        if ($detail) {
            $user_additional = M('user_additional')->where(array('uid'=>I('post.uid')))->field('id as additionalid,value,type')->select();
            $detail['additional'] = $user_additional;
            
            $events = M('events')->where(array('uid'=>I('post.uid')))->select();
            $detail['events'] = $events;
            echo json_encode(array("error"=>0,'data'=>$detail));
        }  else {
            echo json_encode(array("error"=>0,'data'=>$detail));
        }
    }
    public function get_my_data() {
        $this->encryptionAPI(array('uid'), I('post.token'), I('post.timestamp'));
        $user = M('users')->where(array('id'=>I('post.uid')))->find();
        $udid = substr($user['id']*928,0,3).$user['id'];
        $user['udid'] = $udid;
        if ($user) {
            echo json_encode(array("error"=>0,'data'=>$user)); 
        }else{
            echo json_encode(array("error"=>1,'msg'=>'用户不存在')); 
        }
        
    }
    public function profile_edit() {
        $this->encryptionAPI(array('uid'), I('post.token'), I('post.timestamp'));
        $array = array();
        if (isset($_POST['nickname'])) {
            $array['name']=I('post.nickname');
        }
        if (isset($_POST['password'])) {
            $array['password']=  md5(I('post.password'));
        }
        if (isset($_POST['sex'])) {
            $array['sex']=intval(I('post.sex'));
        }
        if (isset($_POST['city'])) {
            $array['city']=I('post.city');
        }
        if (isset($_FILES['headimgurl'])) {
            $auth = new Auth($this->accessKey, $this->secretKey);

            // 生成上传Token
            $token = $auth->uploadToken($this->bucket);

            $filePath = $_FILES['headimgurl'];
            // 上传到七牛后保存的文件名
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath['tmp_name']);
            if ($err !== null) {
//                var_dump($err);
            } else {
                $array['headimgurl_user']='http://resource.kmic168.com/'.$ret['key'];
            }
            
        }
//        var_dump($array);
        M('users')->where(array('id'=>I('post.uid')))->save($array);
        $user = M('users')->where(array('id'=>I('post.uid')))->find();
        echo json_encode(array("error"=>0,'data'=>$user)); 
        
    }
    public function file_upload() {
        $this->encryptionAPI(array('typeuid'), I('post.token'), I('post.timestamp'));
        $auth = new Auth($this->accessKey, $this->secretKey);

        // 生成上传Token
        $token = $auth->uploadToken($this->bucket);

        $filePath = $_FILES['file'];
            // 上传到七牛后保存的文件名
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath['tmp_name']);
            if ($err !== null) {
                var_dump($err);
            } else {
                M('user_additional')->add(array('uid'=>I('post.uid'),'value'=>'http://resource.kmic168.com/'.$ret['key'],'type'=>I('post.type')));
                header('Content-type: application/json');
                echo json_encode(array("error"=>0,'data'=>'http://resource.kmic168.com/'.$ret['key']));
            }
        exit(); 
    }
    public function file_delete() {
        $this->encryptionAPI(array('additionaliduid'), I('post.token'), I('post.timestamp'));
        
        M('user_additional')->where(array('id'=>I('post.additionalid')))->delete();
        header('Content-type: application/json');
        echo json_encode(array("error"=>0,'msg'=>'操作成功'));
        exit(); 
    }
    
    
    public function video_upload() {
        $this->encryptionAPI(array('typeuidvalue'), I('post.token'), I('post.timestamp'));
        M('user_additional')->add(array('uid'=>I('post.uid'),'value'=>I('post.value'),'type'=>I('post.type')));
        header('Content-type: application/json');
        echo json_encode(array("error"=>0,'data'=>I('post.value')));
    }
    
    
    public function video_delete() {
        $this->encryptionAPI(array('additionaliduid'), I('post.token'), I('post.timestamp'));
        M('user_additional')->where(array('id'=>I('post.additionalid')))->delete();
        header('Content-type: application/json');
        echo json_encode(array("error"=>0,'msg'=>'操作成功'));
        exit(); 
    }
    
    
    public function wx_pay() {
        $this->encryptionAPI(array('typeuid'), I('post.token'), I('post.timestamp'));
        ////
        $Total_fee=1;
        switch (I('post.type')) {
            case 0:
                $Total_fee=1000;
                break;
            case 1:
                $Total_fee=2800;

                break;
            case 2:
                $Total_fee=5500;

                break;
            case 3:
                $Total_fee=10800;

                break;

            default:
                break;
        }
        
//        if ($openId == 'oph7UwJ-xbnaf408n1d8R-CpoWJE') {
//            $Total_fee = 1;
//        }
        //order
        $array = array();
        $array['openid']=I('post.uid');
        //随机order
        $rand = mt_rand(1,9);
        $orderID = time().$rand;
        
        $array['orderid']=$orderID;
        $array['addtime']=time();
        $array['price']=$Total_fee/100;
        $array['type']=2;
        $array['level']=I('post.type');
        
        $order = M('orders','center_')->where(array('openid'=>I('post.uid'),'uid'=>I('post.uid'),'type'=>2,'paid'=>0))->find();
        if (!$order) {
            M('orders','center_')->add($array);
        }else{
            M('orders','center_')->where(array('id'=>$order['id']))->save(
                    array(
                        'addtime'=>time(),
                        'price'=>$Total_fee/100,
                        'level'=>I('get.type'),
                        'orderid'=>$orderID,
                        'uid'=>$orderID
                    ));
        }
        ////
//        $input = new \WxPayUnifiedOrder();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("开麦主持VIP会员");
        $input->SetAttach(I('post.uid'));
        $input->SetOut_trade_no($orderID);
        $input->SetTotal_fee($Total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 1600));
        $input->SetGoods_tag("VIPAPP");
        $input->SetNotify_url(self::API_URL_CALLBACK.'index.php/notifyapp/');
        $input->SetTrade_type("APP");
//        $input->SetOpenid($openId);
        $order = \WxPayApiApp::unifiedOrder($input);
        echo json_encode($order);
//        $jsApiParameters = $tools->GetJsApiParameters($order);
    }
 
    
    public function app_version() {
        
        $model = D('Config');
        exit(json_encode(array('code'=>0,'data'=>$model->getVersion())));
    }
    public function notifyapp() {
        vendor('WxpayApp.notify');
        //初始化日志
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }
    
    private function encryptionAPI($array,$token,$timestamp){
        sort($array);
        $str = implode('', $array);
        if (base64_encode($str.$timestamp.self::APITOKEN) == $token) {
            return TRUE;
        }  else {
            $ip = $_SERVER["REMOTE_ADDR"];
            M('syslogs','center_')->add(array('msg'=>$ip.'验证失败'));
            exit(json_encode(array('code'=>999,'msg'=>'验证失败')));
        }
    }
    //95025
    private function sendTemplateSMS($to,$datas,$tempId)
    {
         // 初始化REST SDK
//         global self::accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \Think\CCPRestSmsSDK(self::serverIP,self::serverPort,  self::softVersion);
//         $rest = new REST(self::serverIP,self::serverPort,  self::softVersion);
        $rest->setAccount(self::accountSid,  self::accountToken);
        $rest->setAppId(self::appId);

         // 发送模板短信
//         echo "Sending TemplateSMS to $to <br/>";
         $result = $rest->sendTemplateSMS($to,$datas,$tempId);
         if($result == NULL ) {
//             echo "result error!";
             M('syslogs','center_')->add(array('msg'=>'result error!'));
             return FALSE;
         }
         if($result->statusCode!=0) {
//             echo "error code :" . $result->statusCode . "<br>";
//             echo "error msg :" . $result->statusMsg . "<br>";
             M('syslogs','center_')->add(array('msg'=>"error code :" . $result->statusCode . " error msg :" . $result->statusMsg . ""));
             //TODO 添加错误处理逻辑
             return FALSE;
         }else{
//             echo "Sendind TemplateSMS success!<br/>";
             // 获取返回信息
             $smsmessage = $result->TemplateSMS;
//             echo "dateCreated:".$smsmessage->dateCreated."<br/>";
//             echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
             M('syslogs','center_')->add(array('msg'=>"success dateCreated:".$smsmessage->dateCreated. " smsMessageSid:".$smsmessage->smsMessageSid));
             //TODO 添加成功处理逻辑
             return TRUE;
         }
    }

}