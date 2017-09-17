<?php

namespace Center\Controller;

use Think\Controller;

header("Content-Type: text/html; charset=UTF-8");

//require_once "vendor/leancloud/leancloud-sdk/src/autoload.php";

class DbappController extends Controller {
    

    
    public function UsersIndex() {
        $this->display();
    }

    public function Users() {
        if(I('get.')){
            $start = strtotime(I('get.start'));
            $end   = strtotime(I('get.end'));
            $action  = I('get.action');
        }else{
           echo "请选择日期！";
        }
        
        $DbData = new \Center\Model\Dbapp();
        $data = $DbData->UsersAPP($start,$end);

        $addData = array();
        foreach ($data as $k => $value) {
            if ($value["id_source"] == "0") {
                $addData[$k]["mob"] = $value["id"];
                $addData[$k]["openid"] = "";
                $addData[$k]["platform"] = "IOS";
            } else if ($value["id_source"] == "1") {
                $addData[$k]["mob"] = "";
                $addData[$k]["openid"] = $value["id"];
                $addData[$k]["platform"] = "WX";
            }

            $addData[$k]["name_user"] = $value["nick"];
            $addData[$k]["id_source"] = $value["id_source"];
            $addData[$k]["sex"] = $value["sex"];
            $addData[$k]["headimgurl_user"] = $value["photo_url"];
            $addData[$k]["role_id"] = $value["role_id"];

            if ($addData[$k]["mob"] == "") {
                $addData[$k]["mob"] = $value["phone_num"];
            }
            $addData[$k]["id"] = $value["uid"];
            $addData[$k]["wx_id"] = $value["wx_id"];
            $addData[$k]["introduction"] = $value["introduction"];
            $addData[$k]["interest_ids"] = $value["interest_ids"];
            $addData[$k]["language_ids"] = $value["language_ids"];
            $addData[$k]["voice_ids"] = $value["voice_ids"];
            $addData[$k]["pics"] = $value["pics"];
            $addData[$k]["voice_url"] = $value["voice_url"];
            $addData[$k]["voice_time"] = $value["voice_time"];
            $addData[$k]["check_status"] = $value["check_status"];
            $addData[$k]["check_msg"] = $value["check_msg"];
            $addData[$k]["real_name"] = $value["real_name"];
            $addData[$k]["job"] = $value["job"];
            $addData[$k]["completeness"] = $value["completeness"];
            $addData[$k]["addtime"] = $value["create_time"];
            $addData[$k]["logintime"] = $value["create_time"];
            $addData[$k]["can_video"] = $value["can_video"];
            $addData[$k]["can_voice"] = $value["can_voice"];
            $addData[$k]["video_fee"] = $value["video_fee"];
            $addData[$k]["signature"] = $value["signature"];
            $addData[$k]["locked"] = $value["locked"];
            $addData[$k]["follow_row"] = $value["follow"];
            $addData[$k]["passive_count"] = $value["passive_count"];
            $addData[$k]["article_row"] = $value["article_row"];
        }
        if($action == "1"){
            $ymData = new \Center\Model\UsersModel;
            $addALL = $ymData->AddAllData($addData);  
        }
         echo json_encode($addData);
    }

    
    public function AritcleIndex() {
        if(IS_AJAX){
            $get = I('get.');
            if($get["action"]=="getData"){
                $DbData = new \Center\Model\Dbapp();
                $data = $DbData->ArticleLiset("getData",$get["start"],$get["end"]);
                echo json_encode($data);
                exit();
            }
            if($get["action"]=="tbData"){
                $DbData = new \Center\Model\Dbapp();
                $data = $DbData->ArticleLiset("tbData",$get["start"],$get["end"]);
                echo json_encode($data);
                exit();               
            }
        }
        $this->display();
    }
    
    public function ChannelIndex() {
        if(IS_AJAX){
            $get = I('get.');
            $DbData = new \Center\Model\Dbapp();
            if($get["action"] == "getData"){
                $data = $DbData->Channel();
                echo json_encode($data);
                exit();
            }else if($get["action"] == "tbData"){
                $DbData->Channel("tbData");
            }
        }

        $this->display();
    }    

    public function Fllow() {
        $get = I('get.');
        if($get["check"]=="1"){
             $DbData = new \Center\Model\Dbapp();
             $addid = $DbData->Fllow();
             if ($addid) {
                 $this->success("同步成功！");
                 exit();
             }
        }
        if($get["check"]=="2"){
             $DbData = new \Center\Model\Dbapp();
             $addid = $DbData->message();
             if ($addid) {
                 $this->success("同步成功！");
                 exit();
             }
        }    
        if($get["check"]=="3"){
             $DbData = new \Center\Model\Dbapp();
             $addid = $DbData->praise();
             if ($addid) {
                 $this->success("同步成功！");
                 exit();
             }
        } 
        if($get["check"]=="4"){
             $DbData = new \Center\Model\Dbapp();
             $data = $DbData->userMb();
             $this->success("同步成功！");
             exit();  
        }
         if($get["check"]=="5"){
             $DbData = new \Center\Model\Dbapp();
             $data = $DbData->bill();
             $this->success("同步成功！");
             exit();  
        }
        if($get["check"]=="6"){
             $DbData = new \Center\Model\Dbapp();
             $data = $DbData->mzcomm();
             $this->success("同步成功！");
             exit();  
        }       
        $this->display();
    }
    

    
}
