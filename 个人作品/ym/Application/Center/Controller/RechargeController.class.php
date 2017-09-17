<?php
namespace Center\Controller;
use Think\Controller;
header("Content-Type: text/html; charset=UTF-8");

class RechargeController extends AdminController {
    public function Userorder(){
       if(IS_AJAX){
            $RechargeData =  new \Center\Model\Recharge;
            $data = $RechargeData->Userorder();   
            echo json_encode($data);
            exit();
       }
       $this->display();
    }
    
    public function Config(){
       if(IS_AJAX){
            $RechargeData =  new \Center\Model\Recharge;
            $data = $RechargeData->Config(array('type'=>'0'));   
            echo json_encode($data);
            exit();
       }
       $this->display();
    }    
    
    public function ConfigAdd(){
        if(IS_POST){

            $post = I('post.');

            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 5145728; // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'mp3', 'mp4'); // 设置附件上传类型
            $upload->rootPath = './Uploads/'; // 设置附件上传根目录 
            $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            $post["img"] = "http://".$_SERVER["HTTP_HOST"]."/".$info["pic"]["savepath"].$info["pic"]["name"];
            
            $post["price"] = htmlspecialchars_decode($post["price"]);
            $items = new \Center\Model\ItemsModel;
            $addID = $items->AddData($post);
            if($addID){
                $this->success("添加成功!");
                exit();
            }
        }
        $this->display();
    }
    
    
    
    
}