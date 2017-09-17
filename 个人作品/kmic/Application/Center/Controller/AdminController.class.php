<?php

namespace Center\Controller;

use Think\Controller;
use Think\Wxpay\lib\WxPayConfig;
use Think\Wxpay\lib\WxPayApi;
use Think\Wxpay\lib\Log;
use Think\Wxpay\lib\CLogFileHandler;

header("Content-Type: text/html; charset=UTF-8");

require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";

use LeanCloud\Object;
use LeanCloud\User;
use LeanCloud\CloudException;
use LeanCloud\GeoPoint;
use LeanCloud\Client;
use LeanCloud\Relation;
use LeanCloud\Storage\SessionStorage;
use LeanCloud\Query;

class AdminController extends Controller {

//    const USER_AUTH_URL          =   'http://shop.pingmin8.com/index.php?g=Api&m=PMApi&a=MemberConfig';
    const SITE_URL = 'http://payment.kaka-games.com/';

    Public function _initialize() {
        \LeanCloud\Client::initialize("29f9bXMjtcmOkhtzRCWtVlgM-gzGzoHsz", "FweIIrWPbdjifiD0GwQSEMna", "H9Hn8jcQ7dD95iTlq5AAe46s");
    }

    public function index() {
        if (session('login') == TRUE) {
            $this->redirect('main');
        }
        if (isset($_POST['action']) && I('post.action') == 'login') {
            $data = M('admin', 'center_')->where(array('username' => I('post.username'), 'password' => sha1(md5(I('post.password')))))->find();
            if ($data) {
                session('login', TRUE);
                session('id', $data['id']);
                exit(json_encode(array('error' => 0)));
            } else {
                exit(json_encode(array('error' => 1, 'msg' => '登录失败')));
            }
        }
        $this->display();
    }

    public function main() {
        if (session('login') == FALSE) {
            $this->redirect('index');
        }

        if (isset($_POST['action']) && I('post.action') == 'get7DaysData') {
            $subscribe = array();
            //
            $list1 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 0 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[6] = count($list1);
            $list2 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 2 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[5] = count($list2);
            $list3 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 2 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[4] = count($list3);
            $list4 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 4 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[3] = count($list4);
            $list5 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 5 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 4 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[2] = count($list5);
            $list6 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 5 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[1] = count($list6);
            $list7 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 7 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 DAY)) and issubscribe = 1")->order('id desc')->select();
            $subscribe[0] = count($list7);
            //
            $unsubscribe = array();
            $list1 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 0 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[6] = count($list1);
            $list2 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 2 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[5] = count($list2);
            $list3 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 2 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[4] = count($list3);
            $list4 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 4 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[3] = count($list4);
            $list5 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 5 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 4 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[2] = count($list5);
            $list6 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 5 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[1] = count($list6);
            $list7 = M('users', 'center_')->field('subscribedata')->where("`subscribedata` > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 7 DAY)) and `subscribedata`< UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 DAY)) and issubscribe = 0")->order('id desc')->select();
            $unsubscribe[0] = count($list7);
            exit(json_encode(array('error' => 0, 'subscribe' => $subscribe, 'unsubscribe' => $unsubscribe, 'max' => max($subscribe))));
        }


        $count = M('users')->field('id')->where(array('wx_info' => array('exp', 'is not NULL')))->select();
        $this->assign('count', count($count));

        $countVIP = M('users')->field('id')->where(array('level' => 3))->select();
        $this->assign('countVIP', count($countVIP));

        $where['timeline'] = array('NEQ', 'NULL');
        $countDetail = M('user_detail')->where($where)->field('id')->select();
        $this->assign('countDetail', count($countDetail));

        //1年内的每月会员数


        $countAnn = M('announcement')->where(array('platform' => 'WX'))->field('id')->select();
        $this->assign('countAnn', count($countAnn));

        $countAnnT0 = M('announcement')->where(array('type' => 0, 'platform' => 'WX'))->field('id')->select();
        $this->assign('countAnnT0', count($countAnnT0));

        $countAnnT1 = M('announcement')->where(array('type' => 1, 'platform' => 'WX'))->field('id')->select();
        $this->assign('countAnnT1', count($countAnnT1));
        //
//        echo sha1(md5('abcabcabc'));
        $this->display();
    }

    public function member() {
//        if (session('login')==FALSE) {
//            $this->redirect('index');
//        }
//        if (isset($_POST['action'])&&I('post.action')=='add') {
//            $data = M('users')->where(array('mob'=>I('post.mob')))->find();
//            if ($data) {
//                exit(json_encode(array('error'=>1,'msg'=>'手机已经存在')));
//            }else if(M('users')->add(array('mob'=>I('post.mob'),'password'=> sha1 (md5(I('post.password'))) ,'addtime'=>time()))){
//                
//            }
//            exit(json_encode(array('error'=>0)));
//        }
//        $this->display();
    }

    public function article() {
        if (session('login') == FALSE) {
            $this->redirect('index');
        }
        if (isset($_POST['action']) && I('post.action') == 'add') {


            if (I('post.c1') == 0) {
                $this->error('没选择活动类型', U("Admin/article"));
                exit();
            }
            if (I('post.c2') == 0) {
                $this->error('没选择行业', U("Admin/article"));
                exit();
            }

            if (I('post.type') == 0) {
                $this->error('没选择类型', U("Admin/article"));
                exit();
            }
            if (I('post.title') == '') {
                $this->error('没有标题', U("Admin/article"));
                exit();
            }
            if (I('post.content') == '') {
                $this->error('没有内容', U("Admin/article"));
                exit();
            }
            M('article')->add(array('c1' => I('c1'), 'c2' => I('c2'), 'c3' => I('c3'), 'type' => I('type'), 'title' => I('title'), 'content' => I('content'), 'uid' => session('id'), 'timeline' => time()));
            $this->success('新增成功', U("Admin/article"));
            exit();
        }
        //amend
        if (isset($_POST['action']) && I('post.action') == 'amend') {


            if (I('post.c1') == 0) {
                $this->error('没选择活动类型', U("Admin/article"));
                exit();
            }
            if (I('post.c2') == 0) {
                $this->error('没选择行业', U("Admin/article"));
                exit();
            }
            if (I('post.type') == 0) {
                $this->error('没选择类型', U("Admin/article"));
                exit();
            }
            if (I('post.title') == '') {
                $this->error('没有标题', U("Admin/article"));
                exit();
            }
            if (I('post.content') == '') {
                $this->error('没有内容', U("Admin/article"));
                exit();
            }
            M('article')->where(array('id' => I('id')))->save(array('c1' => I('c1'), 'c2' => I('c2'), 'c3' => I('c3'), 'type' => I('type'), 'title' => I('title'), 'content' => I('content'), 'timeline' => time()));
            $this->success('修改成功', U("Admin/article", array('id' => I('id'))));
            exit();
        }


        if (isset($_GET['id'])) {
            $article = M('article')->where(array('kwx_article.id' => I('get.id')))
                            ->join('kwx_type ON kwx_type.id = kwx_article.type')->find();
            $c1 = M('channel')->where(array('id' => $article['c1']))->find();
            $article['c1name'] = $c1['channel_name'];
            $c2 = M('channel')->where(array('id' => $article['c2']))->find();
            $article['c2name'] = $c2['channel_name'];
            $c3 = M('channel')->where(array('id' => $article['c3']))->find();
            $article['c3name'] = $c3['channel_name'];

            $article['c1'] = $article['c1'] == null ? 0 : $article['c1'];
            $article['c2'] = $article['c2'] == null ? 0 : $article['c2'];
            $article['c3'] = $article['c3'] == null ? 0 : $article['c3'];
//    var_dump($article);
//    exit();
//    var_dump($article);   
//            $str = trim($article['content']); //清除字符串两边的空格
//            $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
//            $str = preg_replace("/\r\n/","",$str); 
//            $str = preg_replace("/\r/","",$str); 
//            $str = preg_replace("/\n/","",$str); 
////            $str = preg_replace("/ /","",$str);
//            $str = preg_replace("/  /","",$str);  //匹配html中的空格
//
//            $article['content'] = htmlspecialchars($article['content']);

            $this->assign('article', $article);
        }

        $dictionary1 = M('channel')->where(array('type' => 1))->select();
        $this->assign('c1', $dictionary1);

        $dictionary2 = M('channel')->where(array('type' => 2))->select();
        $this->assign('c2', $dictionary2);

        $dictionary3 = M('channel')->where(array('type' => 3))->select();
        $this->assign('c3', $dictionary3);

        $type = M('type')->select();
        $this->assign('type', $type);

        $this->display();
    }

    public function memberList() {
        if (isset($_GET['action']) and I('get.action') == 'getdata') {
            if (I('get.end') != '' && I('get.start') != '') {
                $list = M('users', 'center_')->where("subscribedata between " . strtotime(I('get.start')) . " and " . strtotime(I('get.end') . ' +1   day'))->order('id desc')->select();
            } else if (I('get.start') != '') {
                $list = M('users', 'center_')->where("subscribedata between " . strtotime(I('get.start')) . " and " . strtotime(I('get.start') . ' +1   day'))->order('id desc')->select();
            } else {
                $list = M('users', 'center_')->where("to_days(date_format(from_UNIXTIME(`subscribedata`),'%Y-%m-%d')) = to_days(now())")->order('id desc')->select();
            }

            foreach ($list as $key => $value) {
                $json = json_decode(stripslashes($list[$key]['userinfo']));

                $list[$key]['nickname'] = $json->nickname;
                if ($list[$key]['level'] == 10) {
                    $list[$key]['level'] = '管理员';
                } else if ($list[$key]['level'] == 1) {
                    $list[$key]['level'] = '高级会员';
                } else {
                    $list[$key]['level'] = '普通会员';
                }

                if ($list[$key]['issubscribe'] == 1) {
                    $list[$key]['issubscribe'] = '关注中';
                } else {
                    $list[$key]['issubscribe'] = '取消关注';
                }

//                $list[$key]['issubscribe'] = $list[$key]['issubscribe'] = 1?'关注中':'取消关注';
                $list[$key]['subscribedata'] = $list[$key]['subscribedata'] == null ? '不详' : date('Y-m-d H:i:s', $list[$key]['subscribedata']);
                $list[$key]['unsubscribedata'] = $list[$key]['unsubscribedata'] == null ? '没数据' : date('Y-m-d H:i:s', $list[$key]['unsubscribedata']);

                if ($list[$key]['agent'] == null) {
                    $list[$key]['agent'] = '直接关注';
                } else {
                    $agentData = M('users', 'center_')->where(array('id' => $list[$key]['agent']))->find();
                    $agentDataJson = json_decode(stripslashes($agentData['userinfo']));
                    $list[$key]['agent'] = $agentDataJson->nickname;
                }
                switch ($json->sex) {
                    case 1:
                        $list[$key]['sex'] = '男';

                        break;
                    case 2:
                        $list[$key]['sex'] = '女';

                        break;
                    default:
                        $list[$key]['sex'] = '未知';
                        break;
                }
            }
            exit(json_encode($list));
        }

        if (session('login') == FALSE) {
            $this->redirect('index');
        }
        $this->assign('today', time());

        $this->display();
    }

    public function order() {
        $wx_info_c = A('Index');
        if (isset($_GET['action']) and I('get.action') == 'getdata') {
            if (isset($_GET['userid'])) {
                $list = M('orders', 'center_')->where(array('openid' => $wx_info_c->udidToOpenid(I('get.userid'))))->order('id desc')->select();
            } else if (isset($_GET['orderid'])) {
                $list = M('orders', 'center_')->where(array('orderid' => I('get.orderid')))->order('id desc')->select();
            } else if (I('get.end') != '' && I('get.start') != '') {
                $list = M('orders', 'center_')->where("addtime between " . strtotime(I('get.start')) . " and " . strtotime(I('get.end') . ' +1   day'))->order('id desc')->select();
            } else if (I('get.start') != '') {
                $list = M('orders', 'center_')->where("addtime between " . strtotime(I('get.start')) . " and " . strtotime(I('get.start') . ' +1   day'))->order('id desc')->select();
            } else {
                $list = M('orders', 'center_')->where("to_days(date_format(from_UNIXTIME(`addtime`),'%Y-%m-%d')) = to_days(now())")->order('id desc')->select();
            }

            foreach ($list as $key => $value) {


                $list[$key]['addtime'] = date('Y-m-d H:i:s', $list[$key]['addtime']);
                $list[$key]['paidtime'] = $list[$key]['paidtime'] == null ? '' : date('Y-m-d H:i:s', $list[$key]['paidtime']);
                $list[$key]['paid'] = $list[$key]['paid'] == 0 ? '未支付' : '已支付';
                $list[$key]['type'] = $list[$key]['type'] == 2 ? '会员办理' : '其他';
                $list[$key]['udid'] = $wx_info_c->returnUdid($list[$key]['openid']);
            }
            exit(json_encode($list));
        }
        if (session('login') == FALSE) {
            $this->redirect('index');
        }
        $this->assign('today', time());
        $this->display();
    }

    public function articleList() {
        $wx_info_c = A('Index');
        if (isset($_GET['action']) and I('get.action') == 'getdata') {
            if (I('get.end') != '' && I('get.start') != '') {
                $list = M('article')->where("timeline between " . strtotime(I('get.start')) . " and " . strtotime(I('get.end') . ' +1   day'))->order('id desc')->select();
            } else if (I('get.start') != '') {
                $list = M('article')->where("timeline between " . strtotime(I('get.start')) . " and " . strtotime(I('get.start') . ' +1   day'))->order('id desc')->select();
            } else if (isset($_GET['today'])) {
                $list = M('article')->where("to_days(date_format(from_UNIXTIME(`timeline`),'%Y-%m-%d')) = to_days(now())")->order('id desc')->select();
            } else {
                $list = M('article')->order('id desc')->limit('20')->select();
            }

            foreach ($list as $key => $value) {
//                
//                
                $list[$key]['timeline'] = date('Y-m-d H:i', $list[$key]['timeline']);
//                $list[$key]['paidtime'] = $list[$key]['paidtime']==null?'':date('Y-m-d H:i:s',$list[$key]['paidtime']);
                $list[$key]['content'] = mb_substr($list[$key]['content'], 0, 200) . '......';
//                $list[$key]['type'] = $list[$key]['type']==2?'会员办理':'其他';
//                $list[$key]['udid'] = $wx_info_c->returnUdid($list[$key]['openid']);
            }
            exit(json_encode($list));
        }

        if (isset($_POST['action']) and I('post.action') == 'delete') {
            M('article')->where(array('id' => I('post.id')))->delete();
            exit(json_encode(array('error' => 0)));
        }
        if (session('login') == FALSE) {
            $this->redirect('index');
        }
        $this->assign('today', time());
        $this->display();
    }

    public function agent() {
        //
        if (isset($_GET['action']) and I('get.action') == 'getdata') {
            //有份的代理
            $agaentGrpup = M('users', 'center_')->field('agent')->group('agent')->select();
            $array = array();
            foreach ($agaentGrpup as $key => $value) {
                //代理名
                $agaent = M('users', 'center_')->field('userinfo,id')->where(array('id' => $value['agent']))->find();
                $userinfo = json_decode(stripslashes($agaent['userinfo']));
                if ($agaent['id'] != null) {
                    //介绍了多少人
                    $memberCount = M('users', 'center_')->where(array('agent' => $value['agent']))->count();
                    $memberCountToday = M('users', 'center_')->where(array('agent' => $value['agent'], 'subscribeData' => array('gt', strtotime(date('Y-m-d')))))->count();
                    $memberCountYesterday = M('users', 'center_')->where(array('agent' => $value['agent'], 'subscribeData' => array('lt', strtotime(date('Y-m-d'))),
                                'subscribeData' => array('gt', strtotime("-1 day"))))->count();
                    $memberCountBeforeyesterday = M('users', 'center_')->where(array('agent' => $value['agent'], 'subscribeData' => array('lt', strtotime("-1 day")),
                                'subscribeData' => array('gt', strtotime("-2 day"))))->count();
                    $memberCountVIP = M('users', 'center_')->where(array('center_users.agent' => $value['agent'], 'kwx_users.level' => 3))->join('kwx_users ON kwx_users.openid = center_users.openid')->count();
                    //
                    $array[] = array('nickname' => $userinfo->nickname,
                        'id' => $agaent['id'],
                        'count' => $memberCount,
                        'vipcount' => $memberCountVIP,
                        'todaycount' => $memberCountToday,
                        'yesterdaycount' => $memberCountYesterday,
                        'beforeyesterdaycount' => $memberCountBeforeyesterday);
                }

//                var_dump($array);
            }
            exit(json_encode($array));
        }

        $this->display();
    }

    public function ann() {
        //
        if (isset($_GET['action']) and I('get.action') == 'getdata') {
            //有份的代理
            $levelMember = M('users')->field('id,name')->where(array('level' => 10))->select();
//            var_dump($levelMember);
            $array = array();

            foreach ($levelMember as $key => $value) {
                $all = M('announcement')->where(array('signup' => $value['id']))->count();

                $today = M('announcement')->where(array('signup' => $value['id'], 'create_time' => array('between', array(
                                    date("Y-m-d"),
                                    date("Y-m-d", strtotime("+1 day"))))))->count();
                $yesterday = M('announcement')->where(array('signup' => $value['id'], 'create_time' => array('between', array(
                                    date("Y-m-d", strtotime("-1 day")),
                                    date("Y-m-d")
                        ))))->count();
                $beforeyesterday = M('announcement')->where(array('signup' => $value['id']
                            , 'create_time' => array('between', array(
                                    date("Y-m-d", strtotime("-2 day")),
                                    date("Y-m-d", strtotime("-1 day"))
                                ))
//                    ,'create_time'=>array('lt',date("Y-m-d",strtotime("-1 day")) )
                        ))->count();

                //
                if (isset($_GET['start'])) {
                    $searchday = M('announcement')->where(array('signup' => $value['id'],
                                'create_time' => array('lt', date("Y/m/d", strtotime($_GET['start']) + 86400)),
                                'create_time' => array('gt', $_GET['start'])
                            ))->count();
                }
                //
//                var_dump(strtotime($_GET['start'])+86400);
                $array[] = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'all' => $all,
                    'today' => $today,
                    'yesterday' => $yesterday,
                    'beforeyesterday' => $beforeyesterday,
                    'searchday' => $searchday
                );
            }
            exit(json_encode($array));
        }
        $this->assign('today', time());
        $this->display();
    }

    public function logout() {
        session('login', FALSE);
        session('id', NULL);
        $this->redirect('index');
    }
    
    public function announcement() {
        
         if(I('get.action')=="getdata"){
             $Announcement = D('Center/Announcement');
             $list = $Announcement->getData();
             echo json_encode($list);
             exit();
         }
         
         if(I('get.action')=="count"){
             if(I('get.uid')){
               $list[0]['uid'] = I('get.uid');
               $where["signup"] = I('get.uid');
             }else{
               $list[0]['uid'] = "所有";
             }
             
             if(I('get.start') && I('get.end')){
                 $where["create_time"] = array('between',array(I('get.start'),I('get.end')));
             }
             
             $Announcement = D('Center/Announcement');
             $list[0]['count'] = $Announcement->countData($where);
             $where["type"] = "1";
             $list[0]['two_count'] = $Announcement->countData($where);
             $list[0]['one_count'] =$list[0]['count'] - $list[0]['two_count'];
             echo json_encode($list);
             exit();
         }
         
         if(I('get.action')=="del" && I('get.id')){
             $Announcement = D('Center/Announcement');
             $delete = $Announcement->DeleteData(I('get.id'));
             if($delete){
                 $this->success('删除成功！');
             }
             exit();
         }
         
        if(I('get.action')=="level" && I('get.id')){
             $users = D('Center/Users');
             $level = $users->Userlevel(I('get.type'),I('get.id'));
             if($level){
                 $this->success('更新状态成功！');
             }
             exit();
         }        
         $this->display();
    }

}
