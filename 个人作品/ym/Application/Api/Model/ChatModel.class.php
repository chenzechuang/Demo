<?php

namespace Api\Model;

use Think\Model;

class ChatModel {

    //创建房间
    //扣第一分钟钱
    public function createRoom($channel_id, $callee_uid, $caller_uid, $type) {
        $array = array();
        $array['channel_id'] = $channel_id;
        $array['callee_uid'] = $callee_uid;
        $array['caller_uid'] = $caller_uid;
        $array['type'] = intval($type);
        $array['createtimeline'] = time();
        ///创建文章后，主叫1分钟后发心跳包，为主叫设定一个默认值，避免创建房间1分钟后没收到心跳包而已房间被关闭
        $array['callerupdatetimeline'] = $array['createtimeline'] + 60;
        $data = M('room')->add($array);
        \Think\Log::write("房间ID".$data);
        $json_data = array();

        if ($data) {


            $room = M('room')->where(array('id' => $data))->find();

            $check = $this->charge($channel_id, $room);
            if ($check == FALSE) {
                $json_data["state"] = 1;
                $json_data['msg'] = '不够麦子';
                return json_encode($json_data);
            } else {
                $json_data["state"] = 0;
                $json_data['msg'] = '';
                return json_encode($json_data);
            }
        } else {
            $json_data["state"] = 1;
            $json_data['msg'] = '房间创建失败';
            return json_encode($json_data);
        }
    }

    //心跳
    //写入心跳时间
    public function heartbeat($channel_id, $userid, $identity) {
        \Think\Log::write("心跳包".json_encode(I('post.')));
        $room = M('room')->where(array('channel_id' => $channel_id))->find();
        \Think\Log::write("房间".json_encode($room));
        if (!$room) {
            $json_data["state"] = 2;
            $json_data['msg'] = '房间不存在';
            return json_encode($json_data);
        }
        if ($room['status'] == 0) {
            $arr = array();
            $currentTime = time();
            if ($identity == 'callee') {
                //被叫
                $arr['calleeupdatetimeline'] = $currentTime;
            } else {
                //主叫
                $arr['callerupdatetimeline'] = $currentTime;
            }

            $roomSave = M('room')->where(array('channel_id' => $channel_id))->save($arr);
            //修改房间状态
            // M('room')->where(array('channel_id' => $channel_id))->save(array('status' => '1')); 
            
            $check = $this->counting($channel_id, $room);

            if ($check == FALSE) {
                $json_data["state"] = 1;
                $json_data['msg'] = '不够麦子';
                return json_encode($json_data);
            }

            $json_data["state"] = 0;
            $json_data['msg'] = '0';
            return json_encode($json_data);
        } else {
            $json_data["state"] = 99;
            $json_data['msg'] = '房间已经关闭';
            return json_encode($json_data);
        }
    }

    //计算扣不扣钱
    private function counting($channel_id, $room) {
        if ($room['status'] == 0) {
            //取出最大时间 暂时没用
//            $theBigerTime = '';
//            if ($room['calleeupdatetimeline'] > $room['callerupdatetimeline']) {
//                $theBigerTime = $room['calleeupdatetimeline'];
//                echo 'calleeupdatetimeline';
//            } else {
//                $theBigerTime = $room['callerupdatetimeline'];
//                echo 'callerupdatetimeline';
//            }
            //看看和最后一次更新时间差多少
            //如果大于50秒就扣钱
            if ((time() - $room['lastupdatecountingtime']) > 50) {
                $check = $this->charge($channel_id, $room);
                if ($check == FALSE) {
                    return FALSE;
                }else{
                    return TRUE;

                }       
            } else {
                    return TRUE;

            }
        }
    }

    //扣钱
    private function charge($channel_id, $room) {
        //钱包中扣钱
        $check = M("user_bank")->field('deposit')->where(array('uid' => $room['caller_uid']))->find();
        \Think\Log::write($room['caller_uid'] . '有' . $check['deposit'] . ' 要' . $this->fee($room, $channel_id));
        
        if ($check['deposit'] <= $this->fee($room, $channel_id)) {
            return FALSE;
        }
        
        M("user_bank")->where(array('uid' => $room['caller_uid']))->setDec('deposit', $this->fee($room, $channel_id)); //- 主叫
        M("user_bank")->where(array('uid' => $room['callee_uid']))->setInc('coin', $this->fee($room, $channel_id)); //+ 被叫
        M("room")->where(array('id' => $room['id']))->save(array('lastupdatecountingtime' => time()));
        M('room')->where(array('id' => $room['id']))->setInc('counting');
        return TRUE;
    }

    public function checkRoomStatus() {
        $rooms = M('room')->where(array('status' => 0))->select();
        $currentTime = time();
        foreach ($rooms as $room) {
            if ($currentTime - $room['calleeupdatetimeline'] > 60 * 2 || $currentTime - $room['callerupdatetimeline'] > 60 * 2) {
//                $json_data["status"] = 99;
//                $json_data['msg'] = '房间已经关闭';
                $this->charge($room['channel_id'], $room);
                echo $this->close($room['channel_id']);
//                return json_encode($json_data);
            } else {
                $json_data["state"] = 0;
                $json_data['msg'] = '';
                return json_encode($json_data);
            }
        }
    }

    public function close($channel_id) {
        $room = M('room')->where(array('channel_id' => $channel_id))->find();
        $json_data['data'] = time() - $room['createtimeline'];
        if (!$room) {
            $json_data["state"] = 2;
            $json_data['msg'] = '房间不存在';
            return json_encode($json_data);
        }
        if ($room['status'] == 0) {
            $json_data["state"] = 0;
            $json_data['msg'] = '';

            //账单记录
            $deposit = $room['counting'] * $this->fee($room, $channel_id);
            $deposit = $deposit - $deposit * 2; //变为负数
            $coin = $room['counting'] * $this->fee($room, $channel_id);
            M('user_bill')->add(array('create_time' => time(), 'uid' => $room['caller_uid'], 'deposit' => $deposit, 'source' => 2)); //主叫 扣钱
            M('user_bill')->add(array('create_time' => time(), 'uid' => $room['callee_uid'], 'coin' => $coin, 'source' => 2)); //被叫 加钱
            //修改房间状态
            M('room')->where(array('channel_id' => $channel_id))->save(array('status' => '1'));
            //通话时长
            M('users')->where(array('id' => $room['callee_uid']))->setInc('video_time', $json_data['data']); //+时间
            M('users')->where(array('id' => $room['callee_uid']))->setInc('video_times'); //+时间
            return json_encode($json_data);
        } else {
            $json_data["state"] = 99;
            $json_data['msg'] = '房间已经关闭';
            return json_encode($json_data);
        }
    }

    private function fee($room, $channel_id) {
        $fee = 0;
        $user = M('users')->field('video_fee,voice_fee')->where(array('id' => $room['callee_uid']))->find(); //被叫要多少收费//->cache($channel_id, 60 * 5)
//        \Think\Log::write($room['callee_uid'] . '被叫收收费' . json_encode($user));
        if ($room['type'] == 0) {
            $fee = intval($user['video_fee']);
        } else {
            $fee = intval($user['voice_fee']);
        }
        return $fee;
    }

}
