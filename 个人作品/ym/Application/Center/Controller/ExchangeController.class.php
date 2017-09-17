<?php

namespace Center\Controller;

use Think\Controller;

header("Content-Type: text/html; charset=UTF-8");

class ExchangeController extends AdminController {

    public function exchangeList() {
        if (IS_AJAX) {
            $data = new \Center\Model\ExchangeListModel;
            $jsonData = $data->getData();     
            echo json_encode($jsonData);
            exit();
        }
        $this->display();
    }
    
    public function exchangeAdd() {
        if(IS_POST){
           $post = I('post.');
           $data =  new \Center\Model\ExchangeListModel;
           $add  = $data->addData($post);
           if($add){
               $this->success("添加成功");
           }
        }
        $this->display();
    }
       

}
