<?php
namespace Web\Controller;
use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;
 header("Content-Type: text/html; charset=UTF-8");
require_once 'Public/vendor/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;

require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";
use LeanCloud\Object;
use LeanCloud\User;
use LeanCloud\CloudException;
use LeanCloud\GeoPoint;
use LeanCloud\Client;
use LeanCloud\Relation;
use LeanCloud\Storage\SessionStorage;
use LeanCloud\Query;

class SchoolController extends Controller {
    const WEBSITE_URL = 'www.kmic168.com';
    const API_URL          =   'http://www.kmic168.com/?m=Center&a=';
    const WEB_URL          =   'http://www.kmic168.com/?m=Web&a=';  

    // 用于签名的公钥和私钥
    private $accessKey = 'gpbk1K9jF4SdKRhSOzWYNEtP59FlusOsfjw1iwuH';
    private $secretKey = 'EqEySBU9mj5pYo_8dgkdCY2kwnWRxv2MwhqtFfJl';
    private $bucket = 'kmic';
    
    
    Public function _initialize()  
    {
        //exit('服务器修复中');
//        session('openid',null);
        \LeanCloud\Client::initialize("29f9bXMjtcmOkhtzRCWtVlgM-gzGzoHsz", "FweIIrWPbdjifiD0GwQSEMna", "H9Hn8jcQ7dD95iTlq5AAe46s");

        //$wx_info_c = A('Center/Index'); 
        // $wx_info = $wx_info_c->getUserInfoInSite();
        // //        S('udid'.session('openid'),NULL);
        // if (S('udid'.I('get.openid'))==null) {
        //     $wx_info_c->udid(I('get.openid'));
        // }

        if (session('level') == '') {
            session('openid',null);
            session('level',null);
        }

//        echo session('level');
        if (isset($_GET['openid']) && session('openid')==NULL) {
            
            //
            $user = M('users')->where('openid = \''.I('get.openid').'\'')->find();
            
            
            
             M('users')->where('openid = \''.I('get.openid').'\'')->save(array('logintime'=>  time()));
                session('id',$user['id']);
                session('name',$user['name']);
                session('level',$user['level']);
                
            session('openid',$_GET['openid']);
            
//            session('name',$user['name']);
            //检查vip
            $this->checkExpire($_GET['openid']);
        }
        if (S('DICTIONARY')==null) {
            $dictionary = M('dictionary')->field('id,item_name')->select();
            S('DICTIONARY',$dictionary,86400);
        }
        
        /////////////leand cloud
        if (isset($_GET['openid']) && session('openid')!=NULL && isWX()) {
            $isE = FALSE;
            try {
                User::logIn($_GET['openid'], "abc");
                $isE = TRUE;
            } catch (CloudException $ex) {
    //                            var_dump($ex);
                $isE = FALSE;
            } 
            if ($isE == FALSE) {
                //$wx_info_c->weixinGetOpenid(); 
                //exit();
            }
        }
        
        
//        var_dump(S('jsapi_ticket'));
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
    
    public function index(){
        $indexA = A('Center/Index');
//        checkWX();
        
        if(session('openid')==null){
            
            $indexA->weixinGetOpenid();    
            exit();
        }
        
        $c1 = M('channel')->field('id,channel_name')->where(array('type'=>1))->select();
        $this->assign('c1',$c1);
        
        $c2 = M('channel')->field('id,channel_name')->where(array('type'=>2))->select();

        $this->assign('c2',$c2);

        $c3 = M('channel')->field('id,channel_name')->where(array('type'=>3))->select();
        $this->assign('c3',$c3);
        
        $indexB = A('Web/Index');
        $this->assign('signature',  $indexB->wxSign());
        $user = M('users')->where(array('openid'=>session('openid')))->find();
        $this->assign('level',  $user['level']);
        $this->display();
    }

    public function getTypes() {
        $type = array();
        $type['c1'] = M('channel')->field('id,channel_name')->where(array('type'=>1))->select();
        $type['c2']= M('channel')->field('id,channel_name')->where(array('type'=>2))->select();
        $type['c3'] = M('channel')->field('id,channel_name')->where(array('type'=>3))->select();
        exit(json_encode($type));
    }
    
    public function getArticles() {
        
//        if (session('openid')==null) {
//            exit();
//        }
        $arr = array();
        if (isset($_GET['action']) && I('get.action')=='search') {
            $arr['kwx_article.title'] = array('like','%'.I('get.keyword').'%');
            $articles = M('article')->where($arr)->field('kwx_article.*,kwx_type.value')
                ->join('kwx_type ON kwx_type.id = kwx_article.type')->limit(I('get.page')*10,10)->order('kwx_article.id desc')->select();
        } else {
            
            
            if (isset($_POST['channelc1']) && $_POST['channelc1'] !=0) {
                $arr['c1']=$_POST['channelc1'];
            }
            if (isset($_POST['channelc2']) && $_POST['channelc2'] !=0) {
                $arr['c2']=$_POST['channelc2'];
            }
            if (isset($_POST['channelc3']) && $_POST['channelc3'] !=0) {
                $arr['c3']=$_POST['channelc3'];
            }
            $articles = M('article')->where($arr)->field('kwx_article.*,kwx_type.value')
                    ->join('kwx_type ON kwx_type.id = kwx_article.type')->limit(I('post.page')*10,10)->order('kwx_article.id desc')->select();
        }
        
//var_dump($articles);
        
        foreach ($articles as $key => $value) {
//            var_dump($value[$key]);
            $str= htmlspecialchars_decode($value['content']); 
            $str= preg_replace("/<(.*?)>/","",$str); 
            $str= $this->substr_chinese($str, 0,80);
            $articles[$key]['description']= DeleteHtml($str).'...';
            
            
            $c1 = M('channel')->where(array('id'=>$value['c1']))->find();
            $articles[$key]['c1name']=$c1['channel_name'];
            $c2 = M('channel')->where(array('id'=>$value['c2']))->find();
            $articles[$key]['c2name']=$c2['channel_name'];
            $c3 = M('channel')->where(array('id'=>$value['c3']))->find();
            $articles[$key]['c3name']=$c3['channel_name'];
        }
        //echo 'abc'.json_encode($articles);
        //var_dump(function_exists("json_encode"));
        exit(json_encode($articles));
    }
    public function getArticleDetails() {
//        \Think\Log::write('getArticleDetails');
//        if (session('openid')==null) {
//            \Think\Log::write('getArticleDetails:no session');
//            exit();
//        }
        if (isset($_POST) && I('post.action')=='look') {
            if (S('look_log'.I('post.uid').I('post.id'))!=null) {
                exit(json_encode(array('code'=>4,'data'=>S('look_log'.I('post.uid').I('post.id')))));
            }
            
            $look_log = M('look_log')->where(array('timeline'=>strtotime(date('Y-m-d')),'uid'=>I('post.uid')))->find();

            if (!$look_log) {
                M('look_log')->add(array('uid'=>I('post.uid'),'timeline'=>strtotime(date('Y-m-d')),'count'=>1));
                $contact = $this->articleSentToMsg(I('post.id'));

                S('look_log'.I('post.uid').I('post.id'),$contact,75600);
                exit(json_encode(array('code'=>1,'data'=>$contact)));

            } else {
                $member = M('users')->field('level')->where(array('id'=>I('post.uid')))->find();
                
                if ($member['level']<3 && $look_log['count'] < 1) {
                    //不是会员
                    M('look_log')->where('id='.$look_log['id'])->setInc('count',1);

                    $contact=$this->articleSentToMsg(I('post.id'));

                    S('look_log'.I('post.uid').I('post.id'),$contact,75600);

                    exit(json_encode(array('code'=>2,'data'=>$contact)));

                } else if ($member['level']>=3) {
                    //是会员
                    M('look_log')->where('id='.$look_log['id'])->setInc('count',1);
                    $contact = $this->articleSentToMsg(I('post.id'));
                    S('look_log'.I('post.uid').I('post.id'),$contact,75600);
                    exit(json_encode(array('code'=>3,'data'=>$contact)));
                } else {
                    exit(json_encode(array('code'=>0,'data'=>'普通会员每天只能查看1篇文章。开麦不易，最好的支持，是您成为VIP。')));
                }
            }
        }
        
        //read
        if (session('article'.I('post.openid'))=='') {
            session('article'.I('post.openid'),'1');
            M('article')->where(array('id'=>$_POST['id']))->setInc('read_count',1);
        }
        
        // exit(json_encode(array('code'=>0,'data'=>$articles)));
    }

    function articleSentToMsg($id) {
        $articles = M('article')->where(array('kwx_article.id'=>$id))->field('kwx_article.*,kwx_type.value')
                ->join('kwx_type ON kwx_type.id = kwx_article.type')->find();
        $articles['content'] = htmlspecialchars_decode($articles['content']);
        $c1 = M('channel')->where(array('id'=>$articles['c1']))->find();
        $articles['c1name']=$c1['channel_name'];
        $c2 = M('channel')->where(array('id'=>$articles['c2']))->find();
        $articles['c2name']=$c2['channel_name'];
        $c3 = M('channel')->where(array('id'=>$articles['c3']))->find();
        $articles['c3name']=$c3['channel_name'];

        return $articles;
    }
    
    function substr_chinese($str, $start, $length = null) {
        return join("",
        array_slice(
            preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $start, $length)
        );
    }
}