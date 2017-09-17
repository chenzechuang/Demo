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
    
    public function getAppConfig() {
        return M('app_config')->select();
    }
    
    public function UpdataAppConfig($data,$where) {

        return M('app_config')->where($where)->save($data);
    }
}
