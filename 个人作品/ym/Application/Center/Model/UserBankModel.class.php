<?php
namespace Center\Model;

use Think\Model;

class UserBankModel extends Model
{
    /**
     * 
     * @return type
     */
    public function selectALL(){
        $data = $this->select();
        return $data;
    }
    
    public function UpBankData($where,$array) {
        return $this->where($where)->save($array);
    }
    
    public function setIncBankData($where,$array) {
        $this->where($where)->setInc('deposit',$array["deposit"]);
        $this->where($where)->setInc('coin',$array["coin"]);
    }   

}
