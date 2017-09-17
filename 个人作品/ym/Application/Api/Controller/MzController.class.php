<?php
namespace Api\Controller;
use Think\Controller;

class MzController extends Controller {
    
        public function comment() {  //麦主评论
            \Think\Log::write(json_encode(I('post.'))."麦主评论");
            $controller = A('Api/App');
            $controller->encryptionAPI(array('userid','commented_uid','comment_content','stars','fluency','profession','next_comm','comm_time'), I('post.token'), I('post.timestamp'));
            $model =  new \Api\Model\Mz;
            echo $model->comment(I('post.'));  //
        }    
        
        public function commentlist() {  //麦主评论列表
            $controller = A('Api/App');
            $controller->encryptionAPI(array('userid','range'), I('post.token'), I('post.timestamp'));
            $model =  new \Api\Model\Mz;
            echo $model->commentlist(I('post.userid'),I('post.range'));  //
        }    
        
         
        public function recommendlist() {  //推荐麦主
             \Think\Log::write(json_encode(I('post.'))."recommendlist_post");
            $controller = A('Api/App');
            $controller->encryptionAPI(array(''), I('post.token'), I('post.timestamp'));
            $model =  new \Api\Model\Mz;
            echo $model->recommendlist(I('post.userid'));  //
        }
        
        public function mzlist() {  //麦主列表
            \Think\Log::write(json_encode(I('post.'))."mzlist_post");
            $controller = A('Api/App');
            $controller->encryptionAPI(array('range','sex','comm_status','comm_way'), I('post.token'), I('post.timestamp'));
            $model =  new \Api\Model\Mz;
            echo $model->mzlist(I('post.range'),I('post.sex'),I('post.comm_status'),I('post.comm_way'));  //
        }   
        

}