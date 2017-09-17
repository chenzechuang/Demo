<?php

namespace Play\Model;

use Think\Model;

class PlayThemeModel extends Model{

    /**
     * 
     * @return type
     */
    public function getData($where) {
        if($where){
            $data = $this->where($where)->find();
        }else{
            $data = $this->select();
        }
        return $data;
    }
 
}
