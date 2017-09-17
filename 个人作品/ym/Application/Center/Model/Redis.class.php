<?php

namespace Center\Model;

use Think\Model;

class DbApp {

    protected $connection = array(
        'db_type' => 'mysql',
        'db_user' => 'jeiry',
        'db_pwd' => '1qazXSW@3edc',
        'db_host' => '120.76.221.4',
        'db_port' => '3306',
        'db_name' => 'db_test',
    );
    public function UsersCountAPP() {
        return M('user_info', 't_', $this->connection)->count();
    }
    
    public function UsersAPP($start,$end) {
        $where['t_user_info.create_time'] = array('between',array($start,$end));
        $userData = M('user_info', 't_', $this->connection)->field(''
                        . 't_user_login.id,'
                        . 't_user_login.id_source,'
                        . 'from_base64(t_user_info.nick) as nick,'
                        . 't_user_info.uid,'
                        . 't_user_info.sex,'
                        . 't_user_info.city,'
                        . 't_user_info.photo_url,'
                        . 't_user_info.role_id,'
                        . 't_user_info.phone_num,'
                        . 't_user_info.wx_id,'
                        . 't_user_info.introduction,'
                        . 't_user_info.interest_ids,'
                        . 't_user_info.language_ids,'
                        . 't_user_info.voice_ids,'
                        . 't_user_info.pics,'
                        . 't_user_info.voice_url,'
                        . 't_user_info.voice_time,'
                        . 't_user_info.check_status,'
                        . 't_user_info.check_msg,'
                        . 't_user_info.real_name,'
                        . 't_user_info.job,'
                        . 't_user_info.completeness,'
                        . 'FROM_UNIXTIME(t_user_info.create_time,"%Y-%m-%d") as create_time,'
                        . 't_user_info.can_video,'
                        . 't_user_info.can_voice,'
                        . 't_user_info.video_fee,'
                        . 't_user_info.voice_fee,'
                        . 't_user_info.signature,'
                        . 't_user_info.locked')
                        ->join("t_user_login on t_user_login.uid = t_user_info.uid")
                        ->where($where)->select();
        
        foreach ($userData as $key => $value) {
            $userData[$key]["follow"] = M('attention','t_', $this->connection)->where(array('uid'=>$value['uid']))->count();
        }
        return $userData;
    }
    
    public function Channel() {
        $channelData = M('dynamic_topic', 't_', $this->connection)->select();
        
    }    

}
