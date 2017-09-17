<?php

namespace Api\Model;

use Think\Model;

class ConfigModel extends Model
{
    /**
     * 
     * @return type
     */
    public function getVersion(){
        $model = M('config','center_')->field('value')->where(array('id'=>12))->find();
        return $model['value'];
    }
}
