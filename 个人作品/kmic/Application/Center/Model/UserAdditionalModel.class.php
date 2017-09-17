<?php

namespace Center\Model;

use Think\Model;

class UserAdditionalModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getUserImgInspect($uid){
        $data = $this->where(array('uid'=>$uid,'type'=>'1'))->count();
        return $data;
    }
     
}
