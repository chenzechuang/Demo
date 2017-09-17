<?php

namespace Api\Model;

use Think\Model;

class Heart
{

    public function heartbeatUserStatus($uid){
        $userData = M('users')->where(array('id'=>$uid))->find();
        
        if($userData['role_id']){
            
        }        
    }

}
