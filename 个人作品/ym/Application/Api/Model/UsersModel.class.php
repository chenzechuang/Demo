<?php

namespace Api\Model;

use Think\Model;

class UsersModel extends Model {

    /**
     * 
     * @return type
     */
    public function getUserId() {
        return $this->field('id')->getPk(12);
    }

    public function upDataInfo($arry) {
        if (!$arry["userid"]) {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        }
        $upData["name_user"] = base64_encode($arry["nick"]);
        $upData["sex"] = $arry["sex"];
        $upData["birthday"] = $arry["birthday"];
        $upData["city"] = $arry["city"];
        $upData["headimgurl_user"] = $arry["photo_url"];
        $upData["introduction"] = base64_encode($arry["introduction"]);
        $upData["signature"] = base64_encode($arry["signature"]);
        $upData["interest_ids"] = $arry["interest_ids"];
        $upData["language_ids"] = $arry["language_ids"];
        $upData["interest_topic_ids"] = $arry["interest_topic_ids"];

        $upData["experience_ids"] = $arry["experience_ids"];
        $upData["industry_ids"] = $arry["industry_ids"];
        $upData["follow_ids"] = $arry["follow_ids"];
        $upData["wx_id"] = $arry["wx_id"];

        $upData["voice_ids"] = $arry["voice_ids"];
        $upData["pics"] = $arry["pics"];
        $upData["voice_url"] = $arry["voice_url"];

        $upData["real_name"] = $arry["real_name"];
        $upData["job"] = $arry["job"];

        $upData["completeness"] = "1";
        $upData["can_video"] = $arry["can_video"];
        $upData["can_voice"] = $arry["can_voice"];
        $upData["video_fee"] = $arry["video_fee"];
        $upData["voice_fee"] = $arry["voice_fee"];
        $upData["shortest_time"] = $arry["shortest_time"];

        $result = $this->where(array('id' => $arry['userid']))->save($upData);

        $userData = M('users')->field('id as userid,'
                        . 'IFNULL(role_id,\'\') as role_id,'
                        . 'IFNULL(name_user,\'\') as nick,'
                        . 'IFNULL(experience_ids,\'\') as experience_ids,'
                        . 'IFNULL(industry_ids,\'\') as industry_ids,'
                        . 'IFNULL(follow_ids,\'\') as follow_ids,'
                        . 'IFNULL(sex,\'\') as sex,'
                        . 'IFNULL(birthday,\'0\') as birthday,'
                        . 'IFNULL(city,\'\') as city,'
                        . 'IFNULL(headimgurl_user,\'\') as photo_url,'
                        . 'IFNULL(mob,\'\') as phone_num,'
                        . 'IFNULL(wx_id,\'\') as wx_id ,'
                        . 'IFNULL(interest_topic_ids,\'\') as interest_topic_ids,'
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
                )->where(array('id' => $arry['userid']))->find();
        //  if ($result) {

        $userData["pics"] = json_decode(htmlspecialchars_decode($userData['pics']), TRUE);
        $userData['nick'] = base64_decode($userData['nick']);
        $userData['signature'] = base64_decode($userData['signature']);
        $userData['introduction'] = base64_decode($userData['introduction']);

        system("python py/rename.py " . $arry['userid'] . " " . $upData["name_user"] . " >> py/rename.txt 2>&1", $ret);
        return json_encode(array('state' => 0, 'msg' => '更新成功', 'data' => $userData));
        //   } else {
        //       return json_encode(array('state' => 1, 'msg' => '更新失败'));
        //   }
    }

    public function getCurrency($param) {
        $where["uid"] = $param;
        if ($where["uid"]) {
            $Data = M('user_bank')->field('IFNULL(deposit,\'0\') as deposit,IFNULL(coin,\'0\') as coin')->where($where)->find();
            if ($Data["deposit"] == null) {
                $Data["deposit"] = 0;
            }
            if ($Data["coin"] == null) {
                $Data["coin"] = 0;
            }
            $out = array();
            $out["items"][1] = $Data["deposit"];
            $out["coin"] = $Data["coin"];
            return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $out));
        } else {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        } 
    }

    public function getBill($uid, $range) {
        $out = array();
        $where["uid"] = $uid;
        $where["coin"] = "0";
        // $where["deposit"] = array('neq','0');
        if ($where["uid"]) {
            $data = M('user_bill')->where($where)
                    ->limit($range * 10, 10)
                    ->order('id desc')
                    ->select();

            if (count($data) > 0) {
                foreach ($data as $key => $value) {

                    //$out["bill_list"][$key] = $value;
                    if ($value["source"] == "0") {
                        $data[$key]["pay_type"] = "充值";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_recharge@2x.png";
                    } else if ($value["source"] == "1") {
                        $data[$key]["pay_type"] = "提现";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_withdrawals@2x.png";
                    } else if ($value["source"] == "2") {
                        $data[$key]["pay_type"] = "连线麦主";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_call@2x.png";
                    } else if ($value["source"] == "3") {
                        $data[$key]["pay_type"] = "送礼";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_gift@2x.png";
                    } else if ($value["source"] == "4") {
                        $data[$key]["pay_type"] = "收礼";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_gift@2x.png";
                    }

                    // settype($value, "string");
                }
                $out["bill_list"] = $data;
                if (count($out["bill_list"]) == 0) {
                    $out["bill_list"][] = null;
                }

                return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $out));
            } else {
                return json_encode(array('state' => 0, 'msg' => 'no data'));
            }
        } else {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        }
    }

    public function getcoinbill($uid, $range) {
        $where["uid"] = $uid;
        $where["deposit"] = "0";
        if ($where["uid"]) {
            $data = M('user_bill')->where($where)
                    ->limit($range * 10, 10)
                    ->order('id desc')
                    ->select();

            if (count($data) > 0) {
                $out = array();
                foreach ($data as $key => $value) {

                    //$out["bill_list"][$key] = $value;
                    if ($value["source"] == "0") {
                        $data[$key]["pay_type"] = "充值";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_recharge@2x.png";
                    } else if ($value["source"] == "1") {
                        $data[$key]["pay_type"] = "提现";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_withdrawals@2x.png";
                    } else if ($value["source"] == "2") {
                        $data[$key]["pay_type"] = "连线麦主";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_call@2x.png";
                    } else if ($value["source"] == "3") {
                        $data[$key]["pay_type"] = "送礼";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_gift@2x.png";
                    } else if ($value["source"] == "4") {
                        $data[$key]["pay_type"] = "收礼";
                        $data[$key]["icon"] = "http://" . $_SERVER["HTTP_HOST"] . "/Public/img/purse_icon_gift@2x.png";
                    }
                }
                $out["bill_list"] = $data;
                if (count($out["bill_list"]) == 0) {
                    $out["bill_list"][] = null;
                }

                return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $out));
            } else {
                return json_encode(array('state' => 0, 'msg' => 'no data'));
            }
        } else {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        }
    }

    public function exchange($uid, $exchange_id, $zfb_id, $phone_captcha) {
        $whereExchange["exchange_id"] = $exchange_id;
        $where["id"] = $uid;

        if (!$where["id"]) {
            return json_encode(array('state' => 1, 'msg' => 'no userid'));
        }

        $userData = M('users')->where($where)->find();
        if (!$userData["mob"]) {
            //查用户电话号码
            return json_encode(array('state' => 1, 'msg' => '没电话号码'));
        }

        $captchaData = M('verify', 'center_')->where(array('mob' => $userData["mob"], 'code' => $phone_captcha))->find();

        if (count($captchaData) == 0) {
            //查验证码
            return json_encode(array('state' => 1, 'msg' => '验证码出错'));
        } else {
            M('verify', 'center_')->where(array('mob' => $userData["mob"], 'code' => $phone_captcha))->delete();
        }

        $exchangeData = M('exchange_list')->where($whereExchange)->find();
        if (count($exchangeData) == 0) {
            //查况换列表
            return json_encode(array('state' => 1, 'msg' => '没对兑物品'));
        } else {
            $gold = M('user_bank')->where(array('uid' => $uid))->find();
            if ($exchangeData["need_jf"] > $gold["coin"]) { //比较金币
                return json_encode(array('state' => 1, 'msg' => '金币不足'));
            } else {
                //减去用户金币数量
                M('user_bank')->where(array('uid' => $uid))->setDec('coin', $exchangeData["need_jf"]);

                //添加金币流水
                $coinAdd["uid"] = $uid;
                $coinAdd["create_time"] = time();
                $coinAdd["deposit"] = "0";
                $coinAdd["coin"] = "-" . $exchangeData["need_jf"];
                $coinAdd["source"] = "1";
                $coinAdd["icon"] = $exchangeData["icon"];

                M('user_bill')->add($coinAdd);

                //添加申请
                $addData["uid"] = $uid;
                $addData["exchange_id"] = $exchangeData["id"];
                $addData["zfb_id"] = $zfb_id;
                $addData["create_time"] = time();
                $addData["status"] = "0";
                M('user_exchange_data')->add($addData);
                return json_encode(array('state' => 0, 'msg' => '提现成功'));
            }
        }
    }

    public function exchangelist($userid, $range) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no user'));
        }
        $data["exchange_list"] = M('user_exchange_data')->join("ywx_exchange_list on ywx_exchange_list.id = ywx_user_exchange_data.exchange_id")->field('ywx_user_exchange_data.status, ywx_user_exchange_data.exchange_id, ywx_exchange_list.exchange_name, ywx_exchange_list.exchange_desc, ywx_user_exchange_data.create_time, ywx_exchange_list.need_jf')->where(array('uid' => $userid))->order('create_time desc')->limit($range * 10, 10)->select();

        if ($data["exchange_list"] == 0) {
            $data["exchange_list"][] = NULL;
        }
        return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $data));
    }

    public function mzauth($param) {
        if (!$param["userid"]) {
            return json_encode(array('state' => 1, 'msg' => 'no user'));
        }

        if (strlen($param["introduction"]) > 800) {
            return json_encode(array('state' => 1, 'msg' => '介绍超出长度'));
        }

        $userData = M('users')->where(array('id' => $param['userid']))->find();
        if ($userData["role_id"] == 1) {
            return json_encode(array('state' => 1, 'msg' => '你已经是麦主了'));
        }

        $applyData = M('mc_apply')->where(array('id' => $param['userid']))->order('id desc')->find();
        if ($applyData['status'] == 1) {
            return json_encode(array('state' => 1, 'msg' => '正在审核，不能再次申请'));
        }

        if ($param["phone_num"]) {

            
            $phonenum = preg_replace('/[ ]/', '', $param["phone_num"]);
            $phonenumCount = M('users')->where(array('mob' => $phonenum))->find();
            if (count($phonenumCount) > 1) { //电话对应 ID 确保是同一条数据
                if($phonenumCount['id'] != $param['userid']){
                     return json_encode(array('state' => 1, 'msg' => '手机号，已被占用！'));
                }
            }

            if ($userData["mob"] != "") {  // 区分是用手机注册，还是用微信登录
                if ($userData["mob"] != $phonenum) {
                    return json_encode(array('state' => 1, 'msg' => '手机号，与注册手机号码不相符！'));
                }
            }

            if ($param["phone_captcha"]) {
                $codeCount = M('verify', 'center_')->where(array('mob' => $phonenum, 'code' => $param["phone_captcha"]))->count();
                if ($codeCount == 0) {
                    return json_encode(array('state' => 1, 'msg' => '验证码出错！'));
                } else {
                    M('verify', 'center_')->where(array('mob' => $phonenum))->delete();
                }
            }
        }

        //插入申请数据
        $applyData = array();
        $applyData['uid'] = $param["userid"];
        $applyData["create_time"] = time();
        $applyData["status"] = '0';
        M('mc_apply')->add($applyData);

        //更新用户麦主信息
        $upData['audio'] = $param["voice_url"];
        $upData['voice_time'] = $param["voice_time"];
        $upData['pics'] = $param["pics"];
        $upData['voice_ids'] = $param["voice_ids"];
        $upData['introduction'] = base64_encode($param["introduction"]);
        $upData['wx_id'] = $param["wx_id"];
        $upData['mob'] = $phonenum;
        $upData['real_name'] = $param["real_name"];
        $upData['check_status'] = "3";
        $upData['check_msg'] = "";
        M('users')->where(array('id' => $param["userid"]))->save($upData);
        return json_encode(array('state' => 0, 'msg' => '申请成功！')); 
    }

    public function userInfoSimple($uid) {

        if (!$uid) {
            return json_encode(array('state' => 1, 'msg' => 'no user！'));
        }

        $userData = M('users')->where(array('id' => $uid))->field('id as userid,'
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
                        . 'IFNULL(industry_ids,\'\')as industry_ids,'
                        . 'IFNULL(interest_topic_ids,\'\')as interest_topic_ids,'
                        . 'IFNULL(follow_ids,\'\')as follow_ids,'
                        . 'IFNULL(experience_ids,\'\')as experience_ids,'
                        . 'IFNULL(follow_row,\'\')as follow_row,'
                        . 'IFNULL(passive_count,\'\')as passive_count,'
                        . 'IFNULL(article_row,\'\')as article_row,'
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
                        . 'IFNULL(video_times,\'\')as video_times,'
                        . 'IFNULL(video_time,\'\')as video_time,'
                        . 'IFNULL(shortest_time,\'\')as shortest_time,'
                        . 'IFNULL(signature,\'\')as signature'
                )->find();

        $userData["pics"] = json_decode(htmlspecialchars_decode($userData['pics']), TRUE);

        $userData["nick"] = base64_decode($userData["nick"]);
        $userData["signature"] = base64_decode($userData["signature"]);
        $userData["introduction"] = base64_decode($userData["introduction"]);

        if ($_SERVER["HTTP_USERID"] != $uid) {
            $userData['follow'] = M('follow')->where(array('uid' => $_SERVER["HTTP_USERID"], 'mcs_id' => $uid))->count() > 0 ? '1' : '0';
        } else {
            $userData['follow'] = 0;
        }


        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $userData));

    }

    public function userInfoBatch($userids) {
        if (!$userids) {
            return json_encode(array('state' => 1, 'msg' => 'no users！'));
        }

        $useridArry = json_decode($userids, TRUE);
        $useridStr = implode(',', $useridArry);
        $where['id'] = array('in', $useridStr);
        $data = array();
        $data['user_list'] = M('users')->filed('id as uid ,nick as name_user,photo_url as headimgurl_user')->where($where)->select();
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function followAdd($userid, $mcid) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }

        if (!$mcid) {
            return json_encode(array('state' => 1, 'msg' => 'no mcid！'));
        }
        $count = M('follow')->where(array('uid' => $userid, 'mcs_id' => $mcid,))->count();
        if ($count == 0) {
            M('follow')->add(array('uid' => $userid, 'mcs_id' => $mcid, 'log_time' => date('Y-m-d H:i:s')));
            M('users')->where(array('id' => $userid))->setInc('follow_row', 1); //我关注数
            M('users')->where(array('id' => $mcid))->setInc('passive_count', 1); //麦主被关注数

            return json_encode(array('state' => 0, 'msg' => '关注成功！'));
        } else {
            return json_encode(array('state' => 1, 'msg' => '已关注！'));

        }
    }

    public function followDel($userid, $mcid) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }

        if (!$mcid) {
            return json_encode(array('state' => 1, 'msg' => 'no mcid！'));
        }

        M('follow')->where(array('uid' => $userid, 'mcs_id' => $mcid))->delete();
        M('users')->where(array('id' => $userid))->setDec('follow_row', 1); //我们注的
        M('users')->where(array('id' => $mcid))->setDec('passive_count', 1); ///删除麦主被关注数
        return json_encode(array('state' => 0, 'msg' => '取消关注成功！'));
    }

    public function attentionlist($userid, $range) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }
        $data = array();
        $data['attention_list'] = M('follow')->field('IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.id as userid,'
                        . 'ywx_users.introduction,'
                        . 'ifnull(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'unix_timestamp(ywx_follow.log_time) as create_time,'
                        . 'ywx_users.role_id as role_id,'
                        . 'sex as sex,'
                        . 'ywx_users.praise_row as praise'
                
                      /*  . 'ywx_aritcle.content,'
                        . 'unix_timestamp(ywx_aritcle.log_time) as aritcle_create_time,'
                        . 'ywx_aritcle.cover,'
                        . 'ywx_aritcle.listen,'
                        . 'ywx_aritcle.praise,'
                        . 'ywx_aritcle.comments,'
                        . 'ywx_aritcle.channel'*/
                       )
                ->join('ywx_users on ywx_users.id = ywx_follow.mcs_id')
              //  ->join('right join (select * from ywx_aritcle limit 10) as ywx_aritcle on ywx_aritcle.uid = ywx_users.id')
                ->where(array('ywx_follow.uid' => $userid))
              //  ->order('ywx_aritcle.log_time desc')
                ->order('ywx_follow.id desc')
                ->limit($range * 10, 10)
                ->select();

        foreach ($data['attention_list'] as $k => $value) {
            $data['attention_list'][$k]["nick"] = base64_decode($value["nick"]);
            $data['attention_list'][$k]["content"] = base64_decode($value["content"]);
            $data['attention_list'][$k]["introduction"] = base64_decode($value["introduction"]);
        }

        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function beattentionlist($userid, $range) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }
        $data = array();
        $data['beattention_list'] = M('follow')->field('IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.id as userid,'
                        . 'ywx_users.introduction,'
                        . 'ywx_users.headimgurl_user as photo_url,'
                        . 'unix_timestamp(ywx_follow.log_time) as create_time,'
                        . 'ywx_users.role_id as role_id,'
                        . 'ywx_users.initiative as initiative')
                ->join('ywx_users on ywx_users.id = ywx_follow.uid')
                ->where(array('ywx_follow.mcs_id' => $userid))
                ->limit($range * 10, 10)
                ->order('ywx_follow.id desc')
                ->select();
        foreach ($data['beattention_list'] as $k => $value) {
            $data['beattention_list'][$k]["nick"] = base64_decode($data['beattention_list'][$k]["nick"]);
            $data['beattention_list'][$k]["introduction"] = base64_decode($data['attention_list'][$k]["introduction"]);
            // $data['attention_list'][$k]["introduction"] = base64_decode($data['attention_list'][$k]["introduction"]);
        }
        if (count($data) == 0) {
            $data = 0;
        }
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function attentioncount($userid) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }

        $data = array();
        $data = M('users')
                ->field("follow_row as initiative_count,article_row as dynamic_count,gift_row,passive_count")
                ->where(array('id' => $userid))
                ->find();

        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }
    
    public function bdlist($type) {
        if($type == '0'){  //麦主收入榜单
            $data = M('users')->field('IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.id as userid,'
                        . 'ywx_users.introduction,'
                        . 'ifnull(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'ywx_users.role_id as role_id,'
                        . 'sex as sex,'
                        . 'gift_row')
                    ->where(array('role_id'=>'1'))
                    ->order('gift_row desc')
                    ->limit(3)
                    ->select();
        }elseif($type == '1'){  //麦主收入榜单
            $data = M('users')->field('IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.id as userid,'
                        . 'ywx_users.introduction,'
                        . 'ifnull(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'ywx_users.role_id as role_id,'
                        . 'sex as sex,'
                        . 'gift_row')
                    ->where(array('role_id'=>'1'))
                    ->order('gift_row desc')
                    ->limit(10)
                    ->select();
        }elseif ($type =="2") { //用户送礼榜单
            $data = M('users')->field('IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.id as userid,'
                        . 'ywx_users.introduction,'
                        . 'ifnull(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'ywx_users.role_id as role_id,'
                        . 'sex as sex,'
                        . 'IFNULL(give_gift_row,\' 0\') as give_gift_row')
                    ->where(array('role_id'=>'1'))
                    ->order('give_gift_row desc')
                    ->limit(10)
                    ->select();           
        }
        foreach ($data as $key => $value) {
            $data[$key]['nick'] = base64_decode($value["nick"]);
            $data[$key]['introduction'] = base64_decode($value["introduction"]);
        }
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }
}
