<?php

namespace Play\Model;

use Think\Model;

class UsersModel extends Model{

    /**
     * 
     * @return type
     */
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
