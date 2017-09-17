<?php

namespace Center\Model;

use Think\Model;

class UsersModel extends Model
{
    /**
     * 
     * @return type
     */  
    public function Userlevel($type,$id){
        if($type =="down"){
            $data["level"] = '-1';
        }else if($type == "up"){
            $data["level"] = '0';
        }
        
        $data = $this->where(array('id'=>$id))->save($data);
        return $data;
    }      
     
}
