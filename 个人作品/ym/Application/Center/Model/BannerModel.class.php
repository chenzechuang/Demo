<?php

namespace Center\Model;

use Think\Model;

class BannerModel extends Model
{
    /**
     * 
     * @return type
     */
    public function selectALL(){
        $data = $this->select();
        return $data;
    }
    
    public function AddData($array){
        $data = $this->add($array);
        return $data;
    }
    
    public function findData($where){
        $data = $this->where($where)->find();
        return $data;
    }
}
