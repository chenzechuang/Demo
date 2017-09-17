<?php

namespace Center\Model;

use Think\Model;

class UserGiftModel extends Model {

    /**
     * 
     * @return type
     */
    public function selectALL() {
        $data = $this->select();
        return $data;
    }

    public function findData($where) {
        $data = $this->where($where)->find();
        return $data;
    }

    public function userGiftList() {
        $data = $this->field("ywx_user_gift.id,ywx_user_gift.create_time,ywx_user_gift.describe,ywx_user_gift.price,ifnull(f.name,f.name_user) as f_name,f.id as f_id ,t.id as t_id ,ifnull(t.name,t.name_user) as t_name")
                ->join("ywx_users as f ON f.id = ywx_user_gift.userid")
                ->join("ywx_users as t ON t.id = ywx_user_gift.uid")
                ->select();
        foreach ($data as $key => $value) {
            $data[$key]["f_name"] = base64_decode($value["f_name"]);
            $data[$key]["t_name"] = base64_decode($value["t_name"]);
            $data[$key]["create_time"] = date("Y-m-d H:i:s",$value["create_time"]);
        }
        
        return $data;
    }

}
