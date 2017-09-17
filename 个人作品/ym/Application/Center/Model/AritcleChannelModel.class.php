<?php

namespace Center\Model;

use Think\Model;

class AritcleChannelModel extends Model
{
    /**
     * 
     * @return type
     */
    public function selectALL(){
        $data = $this->select();
        return $data;
    }
    

}
