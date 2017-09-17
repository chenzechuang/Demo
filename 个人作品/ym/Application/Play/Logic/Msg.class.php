<?php

namespace Play\Logic;

use Think\Model;

class Msg {

    /**
     * 
     * @return type
     */
    public function msgList($uid, $msgid, $themeid, $page) {
        $andwhere['ywx_play_msg.theme'] = $themeid;
        $where['ywx_play_msg.id'] = $msgid;
        $where['ywx_play_msg.son'] = $msgid;
        $where['_logic'] = 'OR';

        $Model = new Model();
        $sql = "SELECT ywx_play_msg.*,ywx_users.name,ywx_users.headimgurl FROM `ywx_play_msg` left join ywx_users on ywx_play_msg.confirm_id = ywx_users.id  
             WHERE ywx_play_msg.id = '".$msgid."' OR ywx_play_msg.son = '".$msgid."' and ywx_play_msg.theme = '".$themeid."' 
             LIMIT ".$page.",10";
         $data["lists"] = $Model->query($sql);
        /*
          $data["lists"] = M('play_msg')->join("left join ywx_users on ywx_play_msg.confirm_id = ywx_users.id")
          ->field('ywx_play_msg.*,ywx_users.name,ywx_users.headimgurl')
          ->where($where)
          ->where($andwhere)
          ->limit($page,10)
          ->select(false);
         * 
         */
        $theme = new \Play\Model\PlayThemeModel;
        $themeArr["id"] = $themeid;
        $data["theme"] = $theme->getData($themeArr);

        $userWhere["confirm_id"] = $uid;
        $userWhere["son"] = $msgid;
        $msgCount = new \Play\Model\PlayMsgModel;
        $data["count"] = $msgCount->userCount($userWhere);
        return $data;
    }

}
