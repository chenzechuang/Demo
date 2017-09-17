<?php

namespace Center\Model;

use Think\Model;

class ConfigModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getScope(){
        $model = M('config')->field('value')->where(array('id'=>13))->find();
        return $model['value'];
    }
}
