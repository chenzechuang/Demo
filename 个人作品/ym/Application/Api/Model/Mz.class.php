<?php

namespace Api\Model;

use Think\Model;

class Mz {

    public function comment($param) {
        if (!$param['userid']) {
            return json_encode(array('state' => 1, 'msg' => 'no user！'));
        }

        if (!$param['commented_uid']) {
            return json_encode(array('state' => 1, 'msg' => 'no commented_uid！'));
        }

        if (!$param['comment_content']) {
            return json_encode(array('state' => 1, 'msg' => 'no comment_content！'));
        }

        $addData["uid"] = $param["userid"];
        $addData["commented_uid"] = $param["commented_uid"];
        $addData["comment_content"] = base64_encode($param["comment_content"]);
        $addData["stars"] = $param["stars"];
        $addData["fluency"] = $param["fluency"];
        $addData["profession"] = $param["profession"];
        $addData["next_comm"] = $param["next_comm"];
        $addData["comm_time"] = $param["comm_time"];
        $addData["comment_time"] = time();

        M('mz_comment')->add($addData);
        M('users')->where(array('id' => $param["commented_uid"]))->setInc('video_times', 1);
        return json_encode(array('state' => 0, 'msg' => '操作成功！'));
    }

    public function commentlist($userid, $range) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        }

        if (!$range) {
            $range = 0;
        }

        $data["comment_list"] = M('mz_comment')->join("ywx_users on ywx_users.id = ywx_mz_comment.uid")
                        ->field('ywx_mz_comment.commented_uid,'
                                . 'ywx_mz_comment.comment_content,'
                                . 'ywx_mz_comment.stars,'
                                . 'ywx_mz_comment.fluency,'
                                . 'ywx_mz_comment.profession,'
                                . 'ywx_mz_comment.next_comm,'
                                . 'ywx_mz_comment.comm_time,'
                                . 'ywx_mz_comment.comment_time as create_time,'
                                . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                                . 'IFNULL(ywx_users.id,\'\') as uid,'
                                . 'IFNULL(headimgurl_user,headimgurl) as photo_url'
                        )->where(array('ywx_mz_comment.commented_uid' => $userid))
                        ->limit($range * 10, 10)->order('ywx_mz_comment.id desc')->select();
        foreach ($data["comment_list"] as $key => $value) {
            $data["comment_list"][$key]['nick'] = base64_decode($value["nick"]);
            $data["comment_list"][$key]['comment_content'] = base64_decode($value["comment_content"]);
        }
        return json_encode(array('state' => 0, 'msg' => '操作成功', 'data' => $data));
    }

    public function recommendlist() {

        $data = array();
        $data["mz_list"] = M('users')->field('id as userid,'
                        . 'IFNULL(praise_row,\'\') as praise_row,'
                        . 'IFNULL(role_id,\'\') as role_id,'
                        . 'IFNULL(name_user,\'\') as nick,'
                        . 'IFNULL(sex,\'\') as sex,'
                        . 'IFNULL(birthday,\'0\') as birthday,'
                        . 'IFNULL(city,\'\') as city,'
                        . 'IFNULL(headimgurl_user,\'\') as photo_url,'
                        . 'IFNULL(mob,\'\') as phone_num,'
                        . 'IFNULL(wx_id,\'\') as wx_id ,'
                        . 'IFNULL(introduction,\'\') as introduction,'
                        . 'IFNULL(interest_ids,\'\')as interest_ids,'
                        . 'IFNULL(language_ids,\'\')as language_ids,'
                        . 'IFNULL(voice_ids,\'\')as voice_ids,'
                        . 'IFNULL(pics,\'\')as pics,'
                        . 'IFNULL(voice_url,\'\')as voice_url,'
                        . 'IFNULL(check_status,\'\')as check_status,'
                        . 'IFNULL(check_msg,\'\')as check_msg,'
                        . 'IFNULL(real_name,\'\')as real_name,'
                        . 'IFNULL(job,\'\')as job,'
                        . 'IFNULL(completeness,\'\')as completeness,'
                        . 'IFNULL(can_video,\'\')as can_video,'
                        . 'IFNULL(can_voice,\'\')as can_voice,'
                        . 'IFNULL(video_fee,\'\')as video_fee,'
                        . 'IFNULL(voice_fee,\'\')as voice_fee,'
                        . 'IFNULL(shortest_time,\'\')as shortest_time,'
                        . 'IFNULL(video_times,\'\')as video_times,'
                        . 'IFNULL(video_time,\'\')as video_time,'
                        . 'IFNULL(signature,\'\')as signature'
                )->where(array('recommend' => '1', 'role_id' => "1"))->order('recommend desc,logintime desc')->select();
 
        foreach ($data["mz_list"] as $k => $value) {
            $data["mz_list"][$k]["introduction"] = base64_decode($data["mz_list"][$k]["introduction"]);
            $data["mz_list"][$k]["nick"] = base64_decode($data["mz_list"][$k]["nick"]);
            $data["mz_list"][$k]["pics"] = json_decode(htmlspecialchars_decode($value['pics']));
            $data["mz_list"][$k]["last_comment_users"] = M('users')->field('ywx_users.id as userid,'
                            . 'IFNULL(ywx_users.role_id,\'\') as role_id,'
                            . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                            . 'IFNULL(ywx_users.sex,\'\') as sex,'
                            . 'IFNULL(ywx_users.headimgurl_user,\'\') as photo_url'
                    )->join('ywx_mz_comment on ywx_mz_comment.uid = ywx_users.id')->where(array('ywx_mz_comment.commented_uid' => $value['userid']))
                    ->order('ywx_mz_comment.id desc')
                    ->limit(3)
                    ->select();

            $where['_string'] = 'callee_uid=' . $value['userid'] . ' or caller_uid=' . $value['userid'];
            $room = M('room')->where($where)->order('id desc')->find();

            if (count($room) > 0) {
                if($room['status']=="1"){
                     $data["mz_list"][$k]['mzline'] = "0"; //空闲
                }else{
                    $data["mz_list"][$k]['mzline'] = "1"; //通话中
                }
            } else {
                $data["mz_list"][$k]['mzline'] = "0"; //空闲
            }
        }
 
        return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $data));
    }

    public function mzlist($range, $sex, $comm_status, $comm_way) {
        $data = array();
        if (!$range) {
            $range = 0;
        }

        if ($sex > 0) {
            $where['sex'] = $sex;
        }

        if ($comm_way == "1") {
            $where['can_video'] = "1";  //视频
        } elseif ($comm_way == "0") {
            $where['can_voice'] = "1";  //音频
        }
        $where['robot'] = "0";
        $where['role_id'] = "1";

        $data["mz_list"] = M('users')->field('id as userid,'
                        . 'IFNULL(role_id,\'\') as role_id,'
                        . 'IFNULL(name_user,name) as nick,'
                        . 'IFNULL(sex,\'\') as sex,'
                        . 'IFNULL(birthday,\'0\') as birthday,'
                        . 'IFNULL(city,\'\') as city,'
                        . 'IFNULL(headimgurl_user,headimgurl) as photo_url,'
                        . 'IFNULL(mob,\'\') as phone_num,'
                        . 'IFNULL(wx_id,\'\') as wx_id ,'
                        . 'IFNULL(introduction,\'\') as introduction,'
                        . 'IFNULL(interest_ids,\'\')as interest_ids,'
                        . 'IFNULL(language_ids,\'\')as language_ids,'
                        . 'IFNULL(voice_ids,\'\')as voice_ids,'
                        . 'IFNULL(pics,\'\')as pics,'
                        . 'IFNULL(voice_url,\'\')as voice_url,'
                        . 'IFNULL(check_status,\'\')as check_status,'
                        . 'IFNULL(check_msg,\'\')as check_msg,'
                        . 'IFNULL(real_name,\'\')as real_name,'
                        . 'IFNULL(job,\'\')as job,'
                        . 'IFNULL(completeness,\'\')as completeness,'
                        . 'IFNULL(can_video,\'\')as can_video,'
                        . 'IFNULL(can_voice,\'\')as can_voice,'
                        . 'IFNULL(video_fee,\'\')as video_fee,'
                        . 'IFNULL(voice_fee,\'\')as voice_fee,'
                        . 'IFNULL(shortest_time,\'\')as shortest_time,'
                        . 'IFNULL(video_times,\'\')as video_times,'
                        . 'IFNULL(video_time,\'\')as video_time,'
                        . 'IFNULL(signature,\'\')as signature'
                )->where($where)
                ->order('recommend desc,logintime desc')
                ->limit($range * 10, 10)
                ->select();

        foreach ($data["mz_list"] as $k => $value) {
            $data["mz_list"][$k]["nick"] = base64_decode($data["mz_list"][$k]["nick"]);
            $data["mz_list"][$k]["introduction"] = base64_decode($data["mz_list"][$k]["introduction"]);
            $data["mz_list"][$k]["pics"] = json_decode(htmlspecialchars_decode($data["mz_list"][$k]["pics"]));

            $data["mz_list"][$k]["last_comment_users"] = M('users')->field('ywx_users.id as userid,'
                            . 'IFNULL(ywx_users.role_id,\'\') as role_id,'
                            . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                            . 'IFNULL(ywx_users.sex,\'\') as sex,'
                            . 'IFNULL(ywx_users.headimgurl_user,\'\') as photo_url'
                    )->join('ywx_room on ywx_room.caller_uid = ywx_users.id')->where(array('ywx_room.callee_uid' => $value['userid']))
                    ->order('ywx_room.id desc')
                    ->limit(3)
                    ->select();
            if (empty($comm_status)) {
                $comm_status = "0";
            }
        
            $comm_status_sql = "";

            if ($comm_status != "-1") { 
                $comm_status_sql = ' and status = 0'; 
            }

            $roomWhere['_string'] = 'callee_uid=' . $value['userid'] . ' or caller_uid=' . $value['userid'] . $comm_status_sql;

            $room = M('room')->where($roomWhere)->order('id desc')->find();
          
            if (count($room) > 0) { //如果大于0 则有通话，否则 表示空闲

                $data["mz_list"][$k]['mzline'] = "1"; //通话中

            } else {
                $data["mz_list"][$k]['mzline'] = "0";  //空闲
            }
        }
        $out = array();
        if ($comm_status != "-1") {
            foreach ($data["mz_list"] as $commval) {
                if ($commval['mzline'] == $comm_status) {
                    $out['mz_line'][] = $commval;
                }
            }
            $data["mz_list"] = $out['mz_line'];
        }


        return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $data));
    }

}
