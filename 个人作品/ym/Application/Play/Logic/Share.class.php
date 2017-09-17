<?php
namespace Play\Logic;
use Think\Model;

class Share extends Model {

    /**
     * 
     * @return type
     */
    public function userData($where) {
        $playMsg = new \Play\Model\PlayMsgModel;
        $data = $playMsg->findData($where);
        if($data["confirm_id"]){
          $data["info"] =  M('users')->where(array('id'=>$data["confirm_id"]))->find();
          $data["theme"] = M('play_theme')->where(array('id'=>$data["theme"]))->find();
        }
        return $data;
    }

}
