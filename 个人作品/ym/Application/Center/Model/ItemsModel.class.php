<?php

namespace Center\Model;

use Think\Model;

class ItemsModel extends Model
{
    /**
     * 
     * @return type
     */
    public function AddData($arry){
       return $this->add($arry);
        
    }
    

}
