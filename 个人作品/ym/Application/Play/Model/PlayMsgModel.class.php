<?php

namespace Play\Model;

use Think\Model;

class PlayMsgModel extends Model{
    /**
     * 
     * @return type
     */
    public function addData($data) {
        $data = $this->add($data);
        return $data;
    }
    
    public function saveData($msgid,$data) {
        $where['id'] = $msgid;
        $data = $this->where($where)->save($data);
        return $data;
    }
    
    public function findData($where) {
        $data = $this->where($where)->find();
        return $data;
    }
    
    public function getData($where) {
        $data = $this->where($where)->select();
        return $data;
    }
    
    public function userCount($where) {
        $data = $this->where($where)->count();
        return $data;
    }    
}
