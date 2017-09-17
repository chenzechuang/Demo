<?php
namespace Api\Controller;
use Think\Controller;

class ArticleController extends Controller {
    
    public function topics() {
        \Think\Log::write(json_encode(I('post.'))."topics—pos_data");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->articleListDefault();
    }
    
    public function dynamic() {
        \Think\Log::write("版本号：".json_encode($_SERVER). $_SERVER['HTTP_VERSION'].$_SERVER['HTTP_version'].$_SERVER['HTTP_USERID'].json_encode(I('post.'))."dynamic—pos_data");
       
        $controller = A('Api/App');
            $controller->encryptionAPI(array('topic_id','range','userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->articleList(I('post.range'),I('post.topic_id'),I('post.userid'));
    }
    
    public function banner() { //获取动态banner
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->bannerList();
    }
    
    public function pub() { //发布动态
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','cover','content','pics','type','topic_id'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->aritcleAdd(I('post.'));
        
    }
    
    public function del() { //删除动态
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','create_time'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->aritcleDel(I('post.'));
    }    
    
    public function userlist() { //获取个人动态列表 
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        $userID = $_SERVER["HTTP_USERID"];
        echo $model->userAritcleList($userID,I('post.userid'),I('post.range'));       
    }
    
    public function piccover() { //获取动态图片控封面和数量
        $controller = A('Api/App');
        $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->piccover();       
    }    
    
    public function info() { //获取动态详情
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','uid','create_time'), I('post.token'), I('post.timestamp'));
        $userid = I('post.userid');
        $uid = I('post.uid');
        $create_time = I('post.create_time');
        $model = D('Api/Aritcle');
        echo $model->Articleinfo($userid,$uid,$create_time);       
    }    
    
    public function listenclick() {  //点击播放，提交请求，记录收听数
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','create_time'), I('post.token'), I('post.timestamp'));
        $userid = I('post.userid');
        $create_time = I('post.create_time');      
        $model = D('Api/Aritcle');
        echo $model->listenclick($userid,$create_time);       
    }
    
    
    public function attentionlist() { //获取我关注的人的动态列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid,range'), I('post.token'), I('post.timestamp'));
        $userid = I('post.userid');
        $range = I('post.range');      
        $model = D('Api/Aritcle');
        echo $model->attentionlist($userid,$range);       
    }      
    
    public function comment() { //评论
        \Think\Log::write(json_encode(I('post.'))."comment_POST");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','create_time','comment_type','commented_uid','comment_content','comment_mp3','comment_mp3_time'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->comment(I('post.'));       
    }
    
    public function praise() { //文章点赞
        \Think\Log::write(json_encode(I('post.'))."点赞post");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','uid','create_time'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->praise(I('post.userid'),I('post.uid'),I('create_time'));       
    }    

    public function commentlist() { //文章评论列表
        $controller = A('Api/App');
        $controller->encryptionAPI(array('uid','create_time','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->commentlist(I('post.range'),I('post.uid'),I('create_time'));       
    }    
    
    public function relatedlist() { ////与我相关列表
        \Think\Log::write(json_encode(I('post.'))."relatedlist—pos_data");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','related_type','range'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->relatedlist(I('post.range'),I('post.userid'),I('post.related_type'));       
    }
    
    public function exchangeGift() { //送礼物
        \Think\Log::write(json_encode(I('post.'))."送礼post");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid','uid','commodity_id','topic_id','row'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->exchangeGift(I('post.userid'),I('post.uid'),I('post.commodity_id'),I('post.topic_id'),I('post.row'));        
    }
    
    public function userGift() { //送礼物的用户
        \Think\Log::write(json_encode(I('post.'))."送礼用户post");
        $controller = A('Api/App');
        $controller->encryptionAPI(array('topic_id','type','range','userid'), I('post.token'), I('post.timestamp'));
        $model = D('Api/Aritcle');
        echo $model->userGift(I('post.topic_id'),I('post.type'),I('post.range'),I('post.userid'));        
    }    
    
}
