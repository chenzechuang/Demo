<?php

namespace Api\Model;

use Think\Model;

class UsersModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getUserId(){
        return $this->field('id')->getPk(12);
    }
}
