<?php
namespace Api\Controller;
use Think\Controller;


class ShareController extends Controller {

    public function index() {
        if(IS_AJAX){
            $uid = I('get.uid');
            $create_time = date("Y-m-d H:i:s",I('get.create_time'));      
            $range  = I('get.start');
            if(I('get.action') =="aritcle"){
                $Aritcle = D('Api/Aritcle');
                $AritcleData["data"] = $Aritcle->share($uid,$create_time);
                $AritcleData["state"] = "0";
                echo json_encode($AritcleData);
                exit();                
            }

            if(I('get.action') =="msg"){
                $Aritcle = D('Api/Aritcle');
                $data = $Aritcle->shareMsgList($uid,$create_time,$range); 
                echo json_encode($data);
                exit();
            }
        }
        
        $centerCrl = A('Center/Index');
        $centerCrl->getJsapi_ticket();
        $this->assign('signature', wxSign());
        $this->display();
    }

    public function player() {
        $this->display();
    }

}
