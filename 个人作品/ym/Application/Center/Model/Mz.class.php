<?php

namespace Center\Model;

use Think\Model;

class Mz {

    /**
     * 
     * @return type
     */
    public function mzExaminelist() {

        $data = M('mc_apply')->field(''
                        . 'IFNULL(headimgurl_user,headimgurl) as headimgurl ,'
                        . 'pics ,'
                        . 'ywx_users.id as id,'
                        . 'IFNULL(name_user,name) as name,'
                        . 'sex,'
                        . 'introduction,'
                        . 'wx_id,'
                        . 'audio,'
                        . 'check_status,'
                        . 'check_msg,'
                        . 'real_name,'
                        . 'video_fee,'
                        . 'voice_fee,'
                        . 'signature,'
                        . 'role_id,'
                        . 'interest_ids,'
                        . 'language_ids,'
                        . 'mob,'
                        . 'city,'
                        . 'state')
                ->join("ywx_users on ywx_users.id = ywx_mc_apply.uid")
                ->where(array('ywx_mc_apply.state'=>'0'))
                ->group('ywx_users.id')
                ->order("ywx_users.id")
                ->select();
        foreach ($data as $k => $value) {
            $data[$k]["name"] = base64_decode($value["name"]);
            $data[$k]["introduction"] = base64_decode($value["introduction"]);
             $data[$k]["signature"] = base64_decode($value["signature"]);
            $data[$k]["pics"] = json_decode(htmlspecialchars_decode($value["pics"]), TRUE);
        }
        return $data;
    }

    public function mzExamine($arry) {
        if ($arry["check"] == "0") {
            $updata["check_msg"] = $arry["content"];
            $updata["role_id"] = "2";
            $updata["check_status"] = "2";
        } else {
            $conData = M('app_config')->select();
            foreach ($conData as $value) {
                if($value["name"] == "video_fee"){
                    $video_fee = $value["val"];
                }
                 if($value["name"] == "voice_fee"){
                    $voice_fee = $value["val"];
                }               
            }
            $updata["can_voice"] = "1";
            $updata["can_video"] = "1";
            $updata["video_fee"] = $video_fee;
            $updata["voice_fee"] = $voice_fee;
            $updata["role_id"] = "1";
            $updata["check_status"] = "1";
            $updata["check_msg"] = "";
        }
        M('mc_apply')->where(array('uid' => $arry['uid']))->save(array('state'=>$arry["check"]));
        return M('users')->where(array('id' => $arry['uid']))->save($updata);
    }

    public function mzRepayList() {
        $get = I('get.');
        if($get["userid"]){
            $where["ywx_user_exchange_data.uid"] = $get["userid"];
        }
        
        if($get["start"] && $get["end"]){
            $start = strtotime($get["start"]);
            $end = strtotime($get["end"]);
            $where["ywx_user_exchange_data.create_time"] = array('between',"$start,$end");
        }
        
        $mzListData = M('user_exchange_data')->field(
                                 "ywx_users.id as uid,"
                                . "IFNULL(ywx_users.name_user,ywx_users.name) as name,"
                                . "IFNULL(ywx_users.headimgurl,ywx_users.headimgurl_user) as headimgurl_user,"
                                . "ywx_users.wx_id,"
                                . "ywx_users.mob,"
                                . "ywx_user_exchange_data.id,"
                                . "ywx_user_exchange_data.zfb_id,"
                                . "ywx_user_exchange_data.status,"
                                . "ywx_user_exchange_data.create_time,"
                                . "ywx_user_exchange_data.finish_time,"
                                . "ywx_exchange_list.exchange_name,"
                                . "ywx_exchange_list.need_jf,"
                                . "ywx_exchange_list.exchange_desc,"
                                . "ywx_user_bank.deposit,"
                                . "ywx_user_bank.coin"
                        )->join('left join ywx_exchange_list on ywx_exchange_list.id = ywx_user_exchange_data.exchange_id')
                        ->join('left join ywx_users on ywx_users.id = ywx_user_exchange_data.uid')
                        ->join('left join ywx_user_bank on ywx_users.id = ywx_user_bank.uid')
                        ->where($where)
                        ->select();
        foreach ($mzListData as $k => $value) {
            $mzListData[$k]['name'] = base64_decode($value["name"]);
            $mzListData[$k]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
            if(!empty($value['finish_time'])){
                 $mzListData[$k]['finish_time'] = date("Y-m-d H:i:s",$value['finish_time']);
            
            }
        }
        echo json_encode($mzListData);
        exit();
    }
    
    public function rePayStatus($param) {
       if($param["status"] == "2"){
           M('user_bank')->where(array('uid'=>$param["uid"]))->setInc('coin',$param["price"]);
       }
           M('user_exchange_data')->where(array('id' => $param["id"]))->save(array('status' => $param["status"], 'finish_time' => time()));
        return TRUE;
    }

}
