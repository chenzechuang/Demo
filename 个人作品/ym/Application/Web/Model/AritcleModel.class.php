<?php

namespace Web\Model;

use Think\Model;

class AritcleModel extends Model {
    public function articleListDefault() {
        $data = M('aritcle_channel')->join('ywx_aritcle ON ywx_aritcle_channel.id = ywx_aritcle.channel')
                ->field('ywx_aritcle_channel.id as topic_id,ywx_aritcle_channel.title as name,ywx_aritcle_channel.img_url as pic,count(ywx_aritcle.id) as count')
                ->group('ywx_aritcle_channel.id')
                ->select();
        $json_data["data"] = $data;
        $json_data["state"] = 0;
        $json_data['msg']='';
        return json_encode($json_data);
    }
    
    public function articleList($start,$channel,$openid) {
        $start = $start;
        $channel_data = M('aritcle_channel')->where(array('id' => $channel))->find();
        $aritcle_data = M('aritcle')->join('ywx_users_mcs ON ywx_users_mcs.id = ywx_aritcle.uid')
                ->join('ywx_users ON ywx_users_mcs.uid = ywx_users.id')
                ->field('ywx_aritcle.id,ywx_aritcle.audio,ywx_aritcle.uid,ywx_aritcle.title,ywx_aritcle.img_url,ywx_aritcle.log_time,ywx_aritcle.introduction,ywx_aritcle.listen,ywx_aritcle.praise,ywx_aritcle.comments,ywx_users.name,ywx_users.name_user,ywx_users.headimgurl,ywx_users.headimgurl_user')
                ->where(array('ywx_aritcle.channel' => $channel))
                ->order('ywx_aritcle.sort desc')
                ->limit($start, 10)
                ->select();
        foreach ($aritcle_data as $key => $value) {
            $aritcle_data[$key]['follow'] = M('follow')->where(array('uid' => $openid, 'mcs_id' => $value['uid']))->count();
        }
        $json_data['channel'] = $channel_data;
        $json_data['lists'] = $aritcle_data;
        
        return json_encode($json_data);
    }

}
