<?php
namespace Center\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");
//require 'Public/vendor/autoload.php';
require_once 'Public/vendor/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
//
require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";
use LeanCloud\Object;
use LeanCloud\User;
use LeanCloud\CloudException;
use LeanCloud\GeoPoint;
use LeanCloud\Client;
use LeanCloud\Relation;
use LeanCloud\Storage\SessionStorage;
use LeanCloud\Query;

Vendor('Wxpay.WxPayJsApiPay');
class ProfileController extends Controller {
    const API_URL          =   'http://www.kmic168.com/?m=Center&a=';
    const API_URL_CALLBACK          =   'http://www.kmic168.com/';
    // 用于签名的公钥和私钥
    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';
    
  
    Public function _initialize()  
    {  
        \LeanCloud\Client::initialize("29f9bXMjtcmOkhtzRCWtVlgM-gzGzoHsz", "FweIIrWPbdjifiD0GwQSEMna", "H9Hn8jcQ7dD95iTlq5AAe46s");

        $wx_info_c = A('Index');
        $wx_info = $wx_info_c->getUserInfoInSite();
//        S('udid'.session('openid'),NULL);
        if (S('udid'.I('get.openid'))==null) {
            $wx_info_c->udid(I('get.openid'));
        }
//        session('openid',null);
        if (isset($_GET['openid']) && I('get.openid')==NULL) {
            
            //
            $user = M('users')->where('openid = \''.I('get.openid').'\'')->find();
            
            
            
            M('users')->where('openid = \''.I('get.openid').'\'')->save(array('logintime'=>  time()));
                session('id',$user['id']);
                session('name',$user['name']);
                session('level',$user['level']);
                
                
            session('openid',$_GET['openid']);
//            session('name',$user['name']);
        }
        
//        /////////////leand cloud
//        if (isset($_GET['openid']) && session('openid')!=NULL && isWX()) {
//            $isE = FALSE;
//            try {
//                User::logIn($_GET['openid'], "abc");
//                $isE = TRUE;
//            } catch (CloudException $ex) {
//    //                            var_dump($ex);
//                $isE = FALSE;
//            } 
//            if ($isE == FALSE) {
//                $wx_info_c->weixinGetOpenid(); 
//            }
//            
//            
//        }
        
    }
    public function qiniuUpload(){
        $auth = new Auth($this->accessKey, $this->secretKey);

        // 生成上传Token
        $token = $auth->uploadToken($this->bucket);

        if (isset($_POST['go'])) {
            // 要上传文件的本地路径
            $filePath = $_FILES['file'];
//            var_dump($filePath);
//            exit();
            // 上传到七牛后保存的文件名
//            $key = 'my-php-logo.png';

            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, NULL, $filePath['tmp_name']);
            echo "\n====> putFile result: \n";
            if ($err !== null) {
                var_dump($err);
            } else {
//                var_dump();
                echo $ret['key'];
                return $ret;
            }
            exit();
        }
        
        // 构建 UploadManager 对象
//        $uploadMgr = new UploadManager();
    }

    public function index(){
      //  checkWX();
        $indexA = A('Center/Index');
        if(!isset($_GET['openid'])){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
//        $userid = S('wx_info'.session('openid'))['id'];
//        $userCenter = M('users')->where(array('openid'=>$_GET['openid']))->find();
//        if (session('userData')==NULL) {
//            $user = M('users','center_')->where(array('id'=>$userid))->find();
//            session('userData',$user);
//        }else{
//            $user = session('userData');
//        }
//        var_dump($user);
//        exit();
        //取出会员等级信息
//        $configLevel = M('config','center_')->where(array('key'=>'level'))->find();
//        $levelArr = object_array(json_decode($configLevel['value']));
        //
//        echo $user['level'];
//        $html = '';
//        $period = 0;
//        if ($user['level'] == 0) {
//            $html = '<button class="ui-btn-s ui-btn-danger upgrade">免费会员</button>';
//            $this->assign('expire','终身');
//        }else if ($user['level'] == 10) {
//            $html = '<button class="ui-btn-s ui-btn-danger upgrade">终身会员</button>';
//            $this->assign('expire','终身');
//        }else{
//            foreach ($levelArr as $value) {
//                if ($value['level']==$user['level']) {
//                    $html = '<button class="ui-btn-s ui-btn-danger upgrade">'.$value['name'].'</button>';
//                    $period = $value['period'];
//                    break;
//                }
//            }
//            $this->assign('expire',date('y-m-d',strtotime('+'.$period.' month',$user['purchasetime'])));
//        }
        //代理
//        if ($userCenter['agent']!='') {
//            $agent = M('users','center_')->where(array('id'=>$userCenter['agent']))->find();
////            var_dump($agent);
//            $agent['userinfo'] = object_array(json_decode(stripslashes($agent['userinfo'])));
////            var_dump($agent['userinfo']['nickname']);
//            $this->assign('agent', $agent);
//        }else{
//            $this->assign('agent', 0);
//        }
        /////
        $orders = M('orders','center_')->where(array('openid'=>I('get.openid'),'type'=>2))->order('id desc')->select();
        $this->assign('orders', $orders);
        if ($orders[0]['paid']==0) {
            $this->assign('pendingOrder', $orders[0]['orderid']);
        }
        
//        $this->assign('agent', $agent);
        $user = M('users')->where(array('openid'=>I('get.openid')))->find();
        if($user['headimgurl_user']==""){
            $headimg = $user['headimgurl'];
        }else{
            $headimg = $user['headimgurl_user'];
        }
//        $wx_info = json_decode(stripslashes($user["wx_info_b"]),1);
//        
//        if($user['headimgurl_user']!=NULL){
//            $wx_info['headimgurl'] =  $user['headimgurl_user'];
//        }else{
//            $wx_info['headimgurl'] = $user['headimgurl'];
//        }
//        
//        if($user['name_user']!=NULL){
//            $wx_info['name'] =  $user['name_user'];
//        }else{
//            $wx_info['name'] = $user['name'];
//        }       

        //
 
        //$this->assign('user_id', S('udid'.I('get.openid')));
        $this->assign('user_id', substr($user['addtime'], -3).$user['id']);
        $this->assign('name', $user['name']);
        $this->assign('city', $user['city']);
        $this->assign('headimgurl', $headimg);
        $this->assign('uid', $user['id']);
//        $this->assign('html', $html);
        $this->display();
//        var_dump($user);
    }
    
    public function userSet(){
        $this->display();
    } 
    
    public function setCount(){
        $this->display();
    }
    
     public function setImg(){
        
        $user_data = M('users')->where(array('openid'=>I('get.openid')))->find(); 
        $pics = M('user_additional')->where(array('uid'=>$user_data['id'],'type'=>1))->select();
        $picsCode = '';
        foreach ($pics as $key => $value) {
            $picsCode .= 'var p'.$key.' = "'.$value['value'].'"
                $(".pics").prepend(\'<li id="\'+picNum+\'" ><div class="delete" onclick="deletePic(p'.$key.',\'+picNum+\')"><img src="./Public/Kmic/picDelete.png" /></div><img src="'.$value['value'].'" width="103"/></li>\');
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
        $this->assign('uid', $user_data['id']);        
        $this->display();
    }       
     public function setMessage(){

        $indexA = A('Center/Index');
        if (!isset($_GET['openid'])) {

            $indexA->weixinGetOpenid();
            exit();
        }

        if (isset($_GET['action'])&&I('get.action')=='msg') {  //我的资料
            $user_data = M('users')->where(array('openid'=>I('get.openid')))->find(); 
            $detail = M('user_detail')->where(array('uid'=>$user_data['id']))->find(); 
            $detail["error"]="0";
            echo json_encode($detail);
            exit();                
        }
        $user = M('users')->where('`openid` = \''.I('get.openid').'\'')->order('id desc')->find();
        
        if($user['headimgurl_user']!=NULL){
            $user['headimgurl'] =  $user['headimgurl_user'];
        }
        
        if($user['name_user']!=NULL){
            $user['name'] =  $user['name_user'];
        }
        $this->assign('uid', $user['id']);
        $this->assign('user', $user);
        $this->display();
    }
    
    public function setActivity(){
        $user = M('users')->where('`openid` = \''.I('get.openid').'\'')->order('id desc')->find();
        $this->assign('uid', $user['id']);
        if(IS_AJAX){
            $post = I('post.');
            $post["uid"] =  session('id');
            $id = M('user_activity')->add($post);
            $data = array();
            if($id){
                $data["error"] = "0";
            }else{
                $data["error"] = "1";
            }
            echo json_encode($data);
            exit();
        }
        $this->display(); 
    }
     
    public function userView(){
        
        $uid = I('get.uid');
        $user_where['id'] = $uid;
      
        
        if(IS_AJAX){ 
            $data = array();
            
            $user_data = M('users')->where($user_where)->find(); 

            $video_data = M('user_additional')->where(array('uid'=>$user_data['id'],'type'=>'3'))->order('id desc')->find();
            $detail_data = M('user_detail')->where(array('uid'=>$user_data['id']))->order('id desc')->find();
                     
            $additional['img'] = M('user_additional')->where(array('uid'=>$user_data['id'],'type'=>'1'))->order('id desc')->limit(6)->select();
            $activity_data  =M('user_activity')->where(array('uid'=>$user_data['id']))->order('id desc')->limit(2)->select();
           
            
            $data["id"] = substr($user_data['addtime'], -3).$user_data['id'];
            
            if($user_data['headimgurl_user'] !=NULL){
                 $data['headimgurl'] = $user_data['headimgurl_user'];
            }else if($user_data['headimgurl'] !=NULL){
                 $data['headimgurl'] = $user_data['headimgurl'];
            }else{
                 $data['headimgurl'] = $additional['img']['0']['value'];
            } 
            
            if($user_data['name_user'] !=NULL){
                 $data['name'] = $user_data['name_user'];
            }else{
                 $data['name'] = $user_data['name'];
            }   
            

            $data["sex"] = $user_data["sex"];
            $data["level"] = $user_data["level"];
            
            if($user_data["province"]){
                $data["city"] = $user_data["province"];
            }else{
                $data["city"] = $wx_info["city"];
                
            }
            
            $data["video"] = $video_data["value"];
            $data["stature"] = $detail_data["stature"]==null?'未填':$detail_data["stature"];
            $data["organization"] = $detail_data["organization"]==null?'未填':$detail_data["organization"];
            $data["school"] = $detail_data["school"]==null?'未填':$detail_data["school"];
            $data["info"] = $detail_data["info"]==null?'未填':$detail_data["info"];
            $data["honour"] = $detail_data["honour"]==null?'未填':$detail_data["honour"];
            $data["experience"] = $detail_data["experience"]==null?'':$detail_data["experience"];
            $data["price"] = $detail_data["price"]==null?'未填':$detail_data["price"];
            
            $language_arr =  json_decode($detail_data["language"],1);
           
            if(count($language_arr)>"0"){
                foreach ($language_arr as $value) {
                    $language .= $value." ";
                }              
            }else{
                $language = "未补全";
            }

            
            $data["language"] = $language;
            $data["xx"] = $additional;
             
            $i = 0;
            foreach ($activity_data as $activity) {
                $picId = substr($activity["picid"],1);
                $where['id']  = array('in',$picId);
                $activity_data[$i]['img'] = M('user_additional')->where($where)->select();      
                $i++;
            }
            
            $data["activity"] = $activity_data;
            $data["error"] = "0";
            echo json_encode($data);
            exit();
          //  print_r($user_data);
        }
        $this->assign('uid', $uid);
        $this->display(); 

     }
    
    public function addMessage(){
        if (isset($_GET['action'])&&I('get.action')=='msg') {  //我的资料
                $openid = I('post.openid');
                $post_data = I('post.');
                if($openid){
                    $user = M('users')->where(array('openid'=>$openid))->find();
                    $detail = M('user_detail')->where(array('uid'=>$user['id']))->find();
                     
                    $languageExplode = explode('-', I('post.language'));
                    $languageExplode=array_filter($languageExplode);  
                    $languageJson = json_encode($languageExplode); 
                    
                    $post_data["language"] = $languageJson;
                    if(count($detail)>0){
                       $up_detail =  M('user_detail')->where(array('uid'=>$user['id']))->save($post_data);
                    }else{
                       $up_detail =  M('user_detail')->add(array(
                        'stature' => $post_data['stature'],
                        'price' => $post_data['price'],
                        'country' => $post_data['country'],
                        'province' => $post_data['province'],
                        'language' => $post_data['language'],
                        'headimgurl' => $post_data['headimgurl'],
                        'subscribe_time' => $post_data['subscribe_time'],
                        'sex' => $post_data['sex'],
                        'experience' => $post_data['experience'],
                        'organization' => $post_data['organization'],
                        'school' => $post_data['school'],
                        'info' => $post_data['info'],
                        'honour' => $post_data['honour'],
                        'timeline' => time(),
                        'uid'=>$user['id'])); 
                    }
                    
                    //czc str
                    if (isset($_POST)&&I('post.action')=='save') {
                        $up_user = M('users')->where('`openid` = \''.I('get.openid').'\'')->save(array('name'=>I('post.name'),'sex'=>I('post.sex'),'headimgurl_user'=>I('post.headimgurl'),
                            'city'=>I('post.city')));
                    }
                    //czc end

                    //if($up_detail){   //czc
                    if ($up_detail || $up_user) {   //czc
                        $data['error'] = '0';        
                
                    }else{
                        $data['error'] = '1';
                    }
                    
                    echo json_encode($data);
                }              
        }
        
        
        
    }   
    
    public function verifyIDInApp(){
        
        if (strlen(I('get.udid'))==11) {
            $user = M('users','center_')->where(array('mob'=>I('get.udid')))->find();
        }else{
            $id = substr(I('get.udid'),3);
            $user = M('users')->where(array('id'=>$id))->find();
        }
        
        header('Content-type: application/json');
        if ($user) {
            $rand = rand(1000,9999);
            if (I('get.udid') == '9189895') {
                $rand = 9999;
            }
            
            $indexA = A('Center/Index');
                $http = new \Think\Http;
                $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$indexA->getToken(TRUE), '{
    "touser":"'.$user['openid'].'",
    "msgtype":"text",
    "text":
    {
         "content":"您的验证码是<'.$rand.'>"
    }
}');
                
                S(I('get.udid').'verifyCode',$rand,60*5);
                echo json_encode(array('code'=>0));
        }else{
            echo json_encode(array('code'=>1));
        }
        //发到公众号
    }
    public function verifyIDCodeInApp(){
        header('Content-type: application/json');
        if (I('get.code')==S(I('get.udid').'verifyCode')) {
            //验证成功
            
            if (strlen(I('get.udid'))==11) {
                $userCenter = M('users','center_')->where(array('mob'=>I('get.udid')))->find();
                $user = M('users')->where(array('openid'=>$userCenter['openid']))->find();
                $wx_info_c = A('Index');
                $user['udid'] = $wx_info_c->idToUdid($user['id']);
            }else{
                $id = substr(I('get.udid'),3);
                $user = M('users')->where(array('id'=>$id))->find();
                $user['udid'] = I('get.udid');
            }
            //
            $wx_infoObj = json_decode(stripslashes($user['wx_info']));
            $user['headimgurl'] = $wx_infoObj->headimgurl;
            echo json_encode(array('code'=>0,'data'=>$user));
        }else{
            echo json_encode(array('code'=>1));
        }

    }
    
    public function verifyIDIsBecomeMemberApp(){
        header('Content-type: application/json');
        $user = M('users')->where(array('id'=>I('get.uid')))->find();
        if ($user) {
            echo json_encode(array('code'=>0,'data'=>$user));
        }else{
            echo json_encode(array('code'=>1));
        }

    }
    
    public function verify(){
        checkWX();
        if ($_GET['action'] == 'verify') {
            if (strlen(I('post.mob'))==11) {
                $http = new \Think\Http();
                $code = rand(1000,9999);
                M('verify','center_')->where(array('mob'=>I('post.mob')))->delete();
                M('verify','center_')->add(array('mob'=>I('post.mob'),'code'=>$code));
                $xml = $http->httpGet('http://115.238.169.140:8888/sms.aspx?action=send&userid=634&account=kaiyan&password=a222889&mobile='.I('post.mob').'&content=【开麦主持】短信验证码['.$code.']&sendTime=&extno=');
                $xmlToArr = xml_to_array($xml);
                if ($xmlToArr['returnsms']['returnstatus']=='Success') {
                    M('config','center_')->where(array('id'=>10))->save(array('value'=>$xmlToArr['returnsms']['remainpoint']));
                    exit(json_encode(array('error'=>0,'msg'=>'发送成功,请注意接收短信验证码.')));
                }else{
                    exit(json_encode(array('error'=>1,'msg'=>$xmlToArr['returnsms']['message'])));
                }
                
            }else{
                exit(json_encode(array('error'=>1,'msg'=>'电话号码不正确')));
            }
            exit();
        }
        if ($_GET['action'] == 'verifyCode') {
            if (strlen(I('post.mob'))==11 && strlen(I('post.verify'))==4) {
                $verify = M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->find();
                if ($verify) {
                    M('verify','center_')->where(array('mob'=>I('post.mob'),'code'=>I('post.verify')))->delete();
                    M('users','center_')->where(array('openid'=>session('openid')))->save(array('mob'=>I('post.mob')));
                    
                    //设等级
                    M('users','center_')->where(array('openid'=>session('openid'),'level'=>0))->save(array('level'=>2));
                    M('users')->where(array('openid'=>session('openid'),'level'=>0))->save(array('level'=>2));
                    session('userData',null);
                    exit(json_encode(array('error'=>0,'msg'=>'验证成功')));
                }else{
                    exit(json_encode(array('error'=>1,'msg'=>'验证码错误')));
                }
            }
        }
        
        $this->display();
    }
    public function edit(){
        if (isset($_POST)&&I('post.action')=='save') {
     
            M('users')->where('`openid` = \''.I('get.openid').'\'')->save(array('name_user'=>I('post.name'),
                'sex'=>I('post.sex'),'headimgurl_user'=>I('post.headimgurl'),'province'=>I('post.province')));
            exit(json_encode(array('code'=>0)));
        }
        $user = M('users')->where('`openid` = \''.I('get.openid').'\'')->order('id desc')->find();
        
        if($user['headimgurl_user']!=NULL){
            $user['headimgurl'] =  $user['headimgurl_user'];
        }
        
        if($user['name_user']!=NULL){
            $user['name'] =  $user['name_user'];
        }
  
        $this->assign('user',$user);
        $this->display();
    }

    public function member(){
//        echo session('openid');
//        $userid = S('wx_info'.session('openid'))['id'];
//        //level config
//        $configLevel = M('config','center_')->where(array('key'=>'level'))->find();
////        $levelArr = object_array(json_decode($configLevel['value']));
//        //
//        if (isset($_REQUEST['action'])&&$_REQUEST['action']=='pay') {
//            $AES = new \Think\AES();
//            $user = M('users','center_')->where('`openid` = \''.session('openid').'\'')->find();
////            echo json_decode(stripslashes($user['userinfo']))->nickname;
////            exit();
//            $securityCode['id']= 0;
//            $securityCode['openid']= session('openid');
//            $securityCode['price']= 1;
//            $securityCode['title']= json_decode(stripslashes($user['userinfo']))->nickname;
//            $securityCode['orderid']= 0;
//            $securityCode['type']= 2;
//            $securityCode['level']= 1;
//            $securityCode['time']= time();
//            $securityCode['returnUrl'] = self::API_URL_CALLBACK.'/?m=Center&c=Profile&a=member&action=sus';
//            $securityCode = json_encode($securityCode);
////            echo $securityCode;
//            $str =  $AES->encode($securityCode);
//            header('Location: '.self::API_URL.'pay&data='.$str);
//            //$arr = authData(1,array('openid'=>$_GET['openid'],'type'=>'collect','time'=>time()),self::API_URL.'getUserInfo');
//            exit();
//        }
//        if (isset($_REQUEST['action'])&&$_REQUEST['action']=='sus') {
//            //支付成功
//            $order = M('orders','center_')->where('`openid` = \''.session('openid').'\' and `orderid` = 0 and `iid` = 0 and `type` = 2 and `paid` = 1')->find();
//            if ($order) {
//                if (M('users','center_')->where(array('id'=>$userid))->save(array('level'=>$order['level'],'purchaseTime'=>  time()))) {
//                    //清空用户临时保存的数据
//                    session('userData',null);
//                    //修改会员等级
//                    $agent = M('users','center_')->where(array('id'=>$userid))->find();
//                    if ($agent['agent']>0) {
//                        //给钱代理 1级
//                        M('users','center_')->where(array('id'=>$agent['agent']))->setInc('deposit',10);
//                        M('agent_history','center_')->add(array('uid'=>$agent['agent'],'agentuid'=>$userid,'money'=>10,'timeline'=>  time()));
//                        
//                        //2级
//                        $agent2 = M('users')->where(array('id'=>$agent['agent']))->find();
//                        if ($agent2['agent']>0) {
//                            M('users','center_')->where(array('id'=>$agent2['agent']))->setInc('deposit',2);
//                            M('agent_history','center_')->add(array('uid'=>$agent2['agent'],'agentuid'=>$agent['agent'],'money'=>2,'timeline'=>  time()));
//                            //3级
//                            $agent3 = M('users','center_')->where(array('id'=>$agent2['agent']))->find();
//                            if ($agent3['agent']>0) {
//                                M('users','center_')->where(array('id'=>$agent3['agent']))->setInc('deposit',1);
//                                M('agent_history','center_')->add(array('uid'=>$agent3['agent'],'agentuid'=>$agent2['agent'],'money'=>1,'timeline'=>  time()));
//
//                            }
//                        }
//                        
//                        
//                        $this->success('付款成功', U('member'));
//                    }
//                }else{
//                    $this->error('已经处理过了', U('member'));
//                }
//            }  else {
//                $this->error('还没进行支付', U('member'));
//            }
//            exit();
//        }
//        checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        
        
        $user = M('users')->where(array('openid'=>I('get.openid')))->find();
//        var_dump($user);
        $this->assign('user', $user);
        

        $orders = M('orders','center_')->where(array('openid'=>I('get.openid'),'type'=>2))->order('id desc')->select();
        $this->assign('orders', $orders);
        if ($orders[0]['paid']==0) {
            $this->assign('pendingOrder', $orders[0]['orderid']);
        }
        
//        $this->assign('configLevel', $levelArr);
        $this->display();
    }
    
    public function checkOrder() {
        $out_trade_no = I('order');
        $notifiy = true;
        $ordersCherck = M('orders','center_')->where(array('orderid'=>$out_trade_no))->find();
        $findUser = M('users')->where(array('openid'=>$ordersCherck['openid']))->find();
        if ($findUser) {
            if ($findUser['level']>2) {
                $notifiy=FALSE;
            }
        }
        
	$input = new \WxPayOrderQuery();
	$input->SetOut_trade_no($out_trade_no);
        $array = \WxPayApi::orderQuery($input);
//        var_dump($array);
        if ($array['out_trade_no']==I('order')) {
            
            if ($array['trade_state']=='NOTPAY') {
                echo json_encode(array('state'=>0));
            }else if ($array['trade_state']=='SUCCESS'){
                //取出order
                $ordersCherck = M('orders','center_')->where(array('orderid'=>$array['out_trade_no']))->find();
                
                
                if ($ordersCherck['paid']==1) {
                    exit(json_encode(array('state'=>2)));
                }
                //取出member
//                $member = M('users')->where(array('openid'=>$orders['openid']))->find();
                ////////////////////////////////////////////////////////////////////////////////////////
                
                M('orders','center_')->where(array('orderid'=>$array['out_trade_no']))->save(
                            array('paid'=>1,
                                'paidtime'=>time(),
                                'transaction_id'=>$array['transaction_id'],
                                'return_code'=>$array['return_code'],
                                'mch_id'=>$array['mch_id']));
                    $order = M('orders','center_')->where(array('orderid'=>$array['out_trade_no']))->find();
                    //会员时间 
                    $expire = 0;
                    
                    
                    $user = M('users')->where(array('openid'=>$order['openid']))->find();
                    if ($user['expire']!=null) {
                        
                        switch ($order['level']) {
                            case 0:
                                $expire = strtotime("+1 month",$user['expire']);
                                break;
                            case 1:
                                $expire = strtotime("+3 month",$user['expire']); 
                                break;
                            case 2:
                                $expire = strtotime("+6 month",$user['expire']); 
                                break;
                            case 3:
                                $expire = strtotime("+1 year",$user['expire']); 
                                break;
                            default:
                                break;
                        }
                    }else{
                        switch ($order['level']) {
                            case 0:
                                $expire = strtotime("+1 month");
                                break;
                            case 1:
                                $expire = strtotime("+3 month");
                                break;
                            case 2:
                                $expire = strtotime("+6 month");
                                break;
                            case 3:
                                $expire = strtotime("+1 year");
                                break;
                            default:
                                break;
                        }
                    }
                    $userCenter = M('users','center_')->where(array('openid'=>$order['openid']))->find();
                    if ($user['not_first']==0) {
                        M('users')->where(array('openid'=>$order['openid']))->save(array('level'=>3,'expire'=>$expire,'not_first'=>1));
                        session('level',3);
                        //第一次给代理钱
                        //给第一个
                        $agent1 = M('users','center_')->field('id,agent,userinfo')->where(array('openid'=>$order['openid']))->find();
                        if ($agent1['agent'] != null) {
                            M('users','center_')->where(array('id'=>$agent1['agent']))->setInc('deposit',5);
                            $json = json_decode(stripslashes($agent1['userinfo']));
                            //发信息
                            M('messages')->add(array('fromuid'=>112,
                            'touid'=>$user['id'],
                            'msg'=>'您成为了会员的同时为['.$json->nickname.']赚了5元',
                            'timeline'=>  time()
                                ));
                            //
                            M('pay_log','center_')->add(array('uid'=>$agent1['agent'],'amout'=>5,'timeline'=>time(),'fromuid'=>$userCenter['id']));
                            //给第二个
                            $agent2 = M('users','center_')->field('id,agent,userinfo')->where(array('id'=>$agent1['agent']))->find();
                            if ($agent2['agent'] != null) {
                                M('users','center_')->where(array('id'=>$agent2['agent']))->setInc('deposit',3);
                                
                                $json = json_decode(stripslashes($agent2['userinfo']));
                                //发信息
                                M('messages')->add(array('fromuid'=>112,
                                'touid'=>$user['id'],
                                'msg'=>'您成为了会员的同时为['.$json->nickname.']赚了2元',
                                'timeline'=>  time()
                                    ));
                                //
                                M('pay_log','center_')->add(array('uid'=>$agent2['agent'],'amout'=>3,'timeline'=>time(),'fromuid'=>$userCenter['id']));
                            }
                        }
                    }else{
                        M('users')->where(array('openid'=>$order['openid']))->save(array('level'=>3,'expire'=>$expire));
                        session('level',3);
                        //不是第一次
                    }
                
                
                
                ////////////////////////////////////////////////////////////////////////////////////////
                if ($notifiy) {
                    echo json_encode(array('state'=>1));
                }else{
                    exit(json_encode(array('state'=>4)));
                }
                
            }
        }
        
        
    }
    
    public function testing() {
        $time = date('Y-m-d','1464756921'); 
        $a = date("Y-m-d",strtotime("+1 months",'1464756921')); 
        var_dump($time.'--'.$a);
    }
    
    public function wxJsapi() {
        checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        vendor('Wxpay.WxPayJsApiPay');
        $tools = new \JsApiPay();
        $openId = I('get.openid');
        
//        $Out_trade_no=date('YHis').rand(100,1000);
        $Total_fee=1;
        switch (I('get.type')) {
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
        
        if ($openId == 'oph7UwJ-xbnaf408n1d8R-CpoWJE') {
            $Total_fee = 1;
        }
        
        //order
        $array = array();
        $array['openid']=$openId;
        //随机order
        $rand = mt_rand(1,9);
        $orderID = time().$rand;
        
        $array['orderid']=$orderID;
        $array['addtime']=time();
        $array['price']=$Total_fee/100;
        $array['type']=2;
        $array['level']=I('get.type');
        //
        $order = M('orders','center_')->where(array('openid'=>$openId,'type'=>2,'paid'=>0))->find();
        if (!$order) {
            M('orders','center_')->add($array);
        }else{
            M('orders','center_')->where(array('id'=>$order['id']))->save(
                    array(
                        'addtime'=>time(),
                        'price'=>$Total_fee/100,
                        'level'=>I('get.type'),
                        'orderid'=>$orderID
                    ));
        }
        //
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("开麦主持VIP会员");
        $input->SetAttach("开麦主持VIP会员");
        $input->SetOut_trade_no($orderID);
        $input->SetTotal_fee($Total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("VIP");
        $input->SetNotify_url(self::API_URL_CALLBACK.'index.php/notify/');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->display();
    }
    
    public function notify(){
        vendor('Wxpay.notify');
        \Think\Log::write('notify');
        //初始化日志
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
//        $this->display();
    }
    
    
    public function sus(){
        $this->display();
    }
    
    public function deposit(){
        $userid = S('wx_info'.session('openid'))['id'];
        $user = M('users','center_')->where(array('id'=>$userid))->find();
        $this->assign('user', $user);
        //
        $deposit = M('deposit_order','center_')->field('timeline,money,status')->where(array('uid'=>$userid))->order('timeline desc')->limit('20')->select();
        $this->assign('deposit', $deposit);
        //
        $this->display();
    }
    public function qrcode(){
        $list = M('users')->where(array('openid'=>I('get.openid')))->find();
        $this->assign('uid', $list['id']);
        $this->display();
    }
    public function share(){
        $this->display();
    }
    public function messageForApp() {
        $list = M('messages')->where('touid = '.I('get.uid') .' or fromuid =  '.I('get.uid') .'')->field('kwx_users.name,kwx_users.id,kwx_messages.timeline,kwx_messages.msg,kwx_messages.fromuid,kwx_messages.touid,kwx_messages.mainid')->group('kwx_users.id')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('kwx_messages.timeline desc')->limit('80')->select();
        //json_decode(stripslashes($agent['userinfo']))
        $isUpdate= 0; //因为nickname有时是空的 所以要检测更新
        foreach ($list as $key => $value) {
            $user = M('users')->where(array('id'=>$value['id']))->find();
            $wx_infoObj = json_decode(stripslashes($user['wx_info']));

            if ($value['name'] == null) {
                
                M('users')->where(array('id'=>$value['id']))->save(array('name'=>$wx_infoObj->nickname));
                $isUpdate= 1;
            }
            $list[$key]['headimgurl'] = $wx_infoObj->headimgurl;
        }
        if ($isUpdate==1) {
            $this->messageForApp();
        }
        
        header('Content-type: application/json');
        echo json_encode(array('code'=>0,'data'=>$list));
    }
    
    public function chatForApp() {
        $list = M('messages')->where('(touid = '.I('get.fromuid') .' && fromuid =  '.I('get.touid') .') or (touid = '.I('get.touid') .' && fromuid =  '.I('get.fromuid') .') ')->field('kwx_users.name,kwx_users.id,kwx_messages.timeline,kwx_messages.msg,kwx_messages.fromuid,kwx_messages.touid')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('kwx_messages.timeline')->limit('80')->select();
        //json_decode(stripslashes($agent['userinfo']))
        
        $isUpdate= 0; //因为nickname有时是空的 所以要检测更新
        
        foreach ($list as $key => $value) {
            $user = M('users')->where(array('id'=>$value['id']))->find();
            $wx_infoObj = json_decode(stripslashes($user['wx_info']));
            $list[$key]['headimgurl'] = $wx_infoObj->headimgurl;
            
            
            
            $user = M('users')->where(array('id'=>$value['fromuid']))->find();
            $wx_infoObj = json_decode(stripslashes($user['wx_info']));

            if ($user['name'] == null) {
                
                M('users')->where(array('id'=>$value['fromuid']))->save(array('name'=>$wx_infoObj->nickname));
                $isUpdate= 1;
            }
            $list[$key]['fromuidNickname'] = $user['name'];
            $list[$key]['fromuidHeadimgurl'] = $wx_infoObj->headimgurl;
            
            preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $value['msg'], $matches);
            $list[$key]['url'] = $matches[2];
            $list[$key]['msg'] = strip_tags($value['msg']);
            
            $contact = explode('value="', $value['msg']);
            $contact2 = explode('"', $contact[1]);
            $list[$key]['contact'] = $contact2[0];
            
        }
        if ($isUpdate==1) {
            $this->messageForApp();
        }
        header('Content-type: application/json');
        echo json_encode(array('code'=>0,'data'=>$list));
    }

    //Alpha add for change the activity function str 
     public function changeActivity() {
        if (IS_AJAX) {
            $post = I('post.');

            // try {
                M('user_activity')->where(array('id'=>I('post.id')))->save(array('title'=>I('post.title'),'time'=>I('post.time'),'video'=>I('post.video'),'picId'=>I('post.picId')));

                $data['error'] = '0'; 
                $data['title'] = I('post.title');
                echo json_encode($data);
            // } catch {
            //     exit(json_encode(array('code'=>1)));
            // }

        }
     }
     //Alpha add for change the activity function end
    
    public function message() {
        // checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        $list = M('users')->where(array('openid'=>session('openid')))->find();
        $this->assign('uid', $list['id']);
        $this->display();
    }

    public function messageList() {
        $list = M('users')->where(array('openid'=>session('openid')))->find();
        // echo $mine['id'];
        //        exit(); 
        $list['system'] = M('messages')->where('touid = '.$list['id'] .' and (fromuid = 0 or fromuid = 112)')->field('kwx_users.name,kwx_messages.*')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('kwx_messages.timeline desc')->find();


        $M = M();

        $list['toUser'] = $M -> query('select a.fromuid, a.touid, a.msg, a.timeline, u.name, u.headimgurl, u.headimgurl_user FROM (SELECT fromuid, max(timeline) timeline FROM kwx_messages where touid = '.$list['id'] .' and fromuid != 112 and fromuid != 0 GROUP BY fromuid ) b JOIN kwx_users u JOIN kwx_messages a ON u.id = a.fromuid and a.fromuid = b.fromuid AND a.timeline = b.timeline');

        $list['fromUser'] = $M -> query('select a.fromuid, a.touid, a.msg, a.timeline, u.name, u.headimgurl, u.headimgurl_user, c.msg as tomsg, c.timeline as totimeline FROM (SELECT fromuid, max(timeline) timeline FROM kwx_messages where touid = '.$list['id'] .' and fromuid != 112 and fromuid != 0 GROUP BY fromuid ) b JOIN kwx_users u JOIN kwx_messages a JOIN kwx_messages c ON u.id = a.fromuid and a.fromuid = b.fromuid and c.fromuid = '.$list['id'] .' AND c.touid = a.fromuid AND (c.timeline = (SELECT max(timeline) FROM kwx_messages where touid = b.fromuid and fromuid = '.$list['id'] .' )) AND a.timeline = b.timeline');


        // $list['fromUser']= M('messages')->where('((touid = '.$list['id'] .' and fromuid = '.$_GET['fromuid'].' ) or (touid = '.$_GET['fromuid'].' and fromuid = '.$list['id'] .'))')->field('kwx_users.name,kwx_messages.*')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('kwx_messages.timeline desc')->select();
        
        header('Content-type: application/json');
        echo json_encode($list);
    }

    // CZC
    public function contact() {
    // checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        $list = M('users')->where(array('openid'=>session('openid')))->find();

        if($list['headimgurl_user'] !=NULL){
            $list['headimgurl'] = $list['headimgurl_user'];
        } else if($list['headimgurl'] !=NULL){
            $list['headimgurl'] = $list['headimgurl'];
        } else{
            $list['headimgurl'] = $list['additional']['0']['value'];  
        }

        $this->assign('headimgurl', $list['headimgurl']);
        $this->assign('uid', $list['id']);
        $this->assign('fromuid', $_GET['fromuid']);
        $this->display();
        

    }

    public function contactList() {
        $list = M('users')->where(array('openid'=>session('openid')))->find();
        $list['fromUser'] = M('users')->where('id='.$_GET['fromuid'])->find();
        // echo $mine['id'];
        //        exit(); 
        if ($_GET['fromuid'] == '112') {
            $list['system'] = M('messages')->where('(touid = '.$list['id'] .' and (fromuid = 112 or fromuid = 0)) ')->order('kwx_messages.timeline desc')->select();

        } else {
            $list['fromUser']['msg'] = M('messages')->where('((touid = '.$list['id'] .' and fromuid = '.$_GET['fromuid'].' ) or (touid = '.$_GET['fromuid'].' and fromuid = '.$list['id'] .'))')->field('kwx_users.name,kwx_messages.*')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('kwx_messages.timeline desc')->select();
        }
        
    
        
//        var_dump($list);
//        exit(); 
        /*$webController = A('Web/Index');
        foreach ($list as $key => $value) {
            $subList = M('messages')->where('mainid = '.$value['id'])->field('kwx_users.name,kwx_messages.*')->join('kwx_users ON kwx_users.id =  kwx_messages.fromuid')->order('id desc')->limit('10')->select();
            $list[$key]['subMsg'] = $subList;
            $member = M('users')->field('name')->where(array('id'=>$value['touid']))->find();
            $list[$key]['toName'] = $member['name'];
//            $list[$key]['fromName'] = $webController->userDetail($value['fromuid'])['name'];
        }*/
        header('Content-type: application/json');
        echo json_encode($list);
    }
    
     // CZC

    
    public function wallet() {
        checkWX();
        if(!isset($_GET['openid'])){
            $indexA = A('Center/Index');
            $indexA->weixinGetOpenid();    
            exit();
        }
        $mine = M('users','center_')->where(array('openid'=>session('openid')))->find();
        $this->assign('userCenter',$mine);
        $pay_log = M('pay_log','center_')->where(array('uid'=>$mine['id']))->field('center_pay_log.*,center_users.userinfo')->join('center_users ON center_users.id = center_pay_log.uid')->select();
        foreach ($pay_log as $key => $value) {
            $pay_log[$key]['userinfo'] = object_array(json_decode(stripslashes($pay_log[$key]['userinfo'])));
        }
        $this->assign('pay_log',$pay_log);
        $this->display();
    }

    public function updataonlitime() {
        $outPut = "1";
        $uid = I('get.uid');
        $UserDetail = D('Center/UserDetail');
        $InfoData = $UserDetail->getUserDataInspect($uid); //判断用户信息是否完整
       
        if ($InfoData["state"] == "1") {       //用户信息完整
            $UserDetailImg = D('Center/UserAdditional');
            $ImgData = $UserDetailImg->getUserImgInspect($uid); //查相册是否有图片
            if ($ImgData > 0) { 
                $timeline = $InfoData['userData']['timeline'];
                $updataTimeLine =$UserDetail->updataTimeLine($uid,$timeline); //更新timline时间
                if($updataTimeLine=="1"){ //更新成功
                    $outPut = "1";
                }else{
                    $outPut = "2";
                    
                }
            } else {
                $outPut = "0";
            }        
        }else {
            $outPut = "0";
        }
        echo $outPut;  //0、失败（资料不完整）/1、成功/2、更新失败，更新时间未够24小时
        exit();
    }
}