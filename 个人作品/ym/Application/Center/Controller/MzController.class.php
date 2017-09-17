<?php
namespace Center\Controller;
use Think\Controller;
 header("Content-Type: text/html; charset=UTF-8");
 
 //require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";

class MzController extends AdminController {

    public function mzExamine(){
        if(IS_AJAX){
           $mz = new \Center\Model\Mz;          
           if(I('post.action')=="mzExamine"){
               $arry = array();
               $arry["check"] = I('post.check');
               $arry["uid"] = I('post.userId');
               $arry["content"] = I('post.content');
               echo $mz->mzExamine($arry);
               exit();
           }              
           $list = $mz->mzExaminelist();
           echo json_encode($list);
           exit();
        }
        $this->display();
    }
    
     public function mzRepay(){
         if(IS_AJAX){
            $exchangeDataList = new \Center\Model\Mz;
            $outList = $exchangeDataList->mzRepayList();
            echo json_encode($outList);
            exit();
         }
         $this->display();
     }  
     
     public function rePayStatus() {
        $get = I('get.');
        $mzModel = new \Center\Model\Mz;
        $result = $mzModel->rePayStatus($get);
        if($result){
            $this->success("处理成功！");
        }
     }
            
  
}