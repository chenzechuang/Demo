<?php
namespace Center\Model;

use Think\Model;

class AdminMenuModel extends Model
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
