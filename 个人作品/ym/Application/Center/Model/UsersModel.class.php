<?php

namespace Center\Model;

use Think\Model;

class UsersModel extends Model {
    /**
     * 
     * @return type
     */
    public function AddAllData($param) {
        return $this->AddALL($param);
    }
    
    public function saveUnionData($arr) {
        $dataSave = M('union_wx')->add(array('subscribe' => $arr['subscribe'],
            'openid' => $arr['openid'],
            //   'nickname' => $arr['nickname'],
            'sex' => $arr['sex'],
            'city' => $arr['city'],
            'country' => $arr['country'],
            'province' => $arr['province'],
            'language' => $arr['language'],
            'headimgurl' => $arr['headimgurl'],
            'subscribe_time' => $arr['subscribe_time'],
            'unionid' => $arr['unionid'],
            'remark' => $arr['remark'],
            'groupid' => $arr['groupid'],
            'tagid_list' => $arr['tagid_list']));
        if (!$dataSave) {
            return FALSE;
        }
    }

    public function saveMemberData($arr, $openid, $infoArr, $agent = null) {
        //如果openid出问题
        if (strlen($openid) < 1 || count($arr) < 1) {
            return FALSE;
        }
        $userdata = M('users')->where('`openid` = \'' . $openid . '\'')->find();
        if (!$userdata) {
            $arr = array(
                'addtime' => time(),
                'logintime' => time(),
                'name' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($infoArr->nickname)),
                'sex' => $infoArr->sex,
                'country' => $infoArr->country,
                'province' => $infoArr->province,
                'city' => $infoArr->city,
                'unionid' => $arr['unionid'],
                'openid' => $infoArr->openid,
                'issubscribe' => 1,
                'subscribe_time' => time(),
                'platform' => 'WX',
                'headimgurl' => $infoArr->headimgurl);
            if ($agent != null && strlen($agent) > 0) {
                $agentExp = explode('_', $agent)[1];
                $arr['agent'] = $agentExp;
            }
            if ($member = M('users')->add($arr)) {
                $this->ACManager($arr, $member);
            }
        } else {
            $member = M('users')->where('`openid` = \'' . $openid . '\'')->save(array('logintime' => time(),
                'name' => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', addslashes($infoArr->nickname)),
                'sex' => $infoArr->sex,
                'country' => $infoArr->country,
                'province' => $infoArr->province,
                'city' => $infoArr->city,
                'headimgurl' => $infoArr->headimgurl,
                'issubscribe' => 1,
                'subscribe_time' => time(),
                'platform' => 'WX'));
        }
    }

    public function unsubscribe($openid) {
        $data = M('users')->where('`openid` = \'' . $openid . '\'')->save(array('issubscribe' => 0, 'unsubscribe_time' => time()));
        if ($data) {
            return TRUE;
        }
    }

    private function ACManager($param, $uid) {
        $param['id'] = $uid;
        if (count($param) < 1) {
            exit();
        }
        $file = $_SERVER['DOCUMENT_ROOT'] . "/ACManager";
        if (!is_dir($file)) {
            mkdir($file, 0777);
        }
        $filename = $file . '/' . $uid . '.txt';
        $somecontent = json_encode($param);
        if (!$handle = fopen($filename, 'w')) {
            exit;
        }
        if (fwrite($handle, $somecontent) === FALSE) {
            exit;
        }
        fclose($handle);
    }

    public function Userorder() {
        return M('user_bill')->field(''
                . 'ifnull(ywx_users.headimgurl_user,ywx_users.headimgurl) as headimgurl,'
                . 'ywx_items.descarption,'
                . 'ywx_users.mob as mob,'
                . 'ywx_users.wx_id as wx_id,'
                . 'FROM_UNIXTIME(ywx_user_bill.create_time,\'%Y-%m-%d %H:%i:%s\') as create_time,'
                . 'ifnull(ywx_users.name_user,ywx_users.name) as name')
                ->join('ywx_items on ywx_user_bill.type = ywx_items.id')
                        ->join('ywx_users on ywx_user_bill.uid = ywx_users.id')
                        ->select();
    }
    
    public function userSlect($param) {
        return $this->field(''
                 . 'ywx_users.id as id,'
                . 'ywx_user_bank.deposit as deposit,'
                . 'ywx_user_bank.coin as coin,'
                . 'DATE_FORMAT(FROM_UNIXTIME(ywx_users.addtime),"%Y.%m.%d %H:%i:%s") as addtime,'
                . 'article_row,fans_row as fans_row,'
                . 'ywx_users.follow_ids as follow_ids,'
                . 'ywx_users.openid as openid,'
                
                . 'ywx_users.experience_ids as experience_ids,'
                . 'IFNULL(ywx_users.headimgurl,headimgurl_user) as headimgurl,'
                . 'ywx_users.industry_ids as industry_ids,'
                . 'ywx_users.initiative as initiative,'
                
                . 'ywx_users.interest_ids as interest_ids,'
                . 'ywx_users.introduction as introduction,'
                . 'ywx_users.job as job,'
                . 'ywx_users.sex as sex,'
                . 'ywx_users.city as city,'
                . 'ywx_users.role_id as role_id,'
                . 'ywx_users.locked as locked,'
                . 'ywx_users.pics as pics,'
                . 'ywx_users.audio as audio,'
                
                . 'date_format(from_unixtime(ywx_users.logintime),"%Y.%m.%d %H:%i:%s") as logintime,'
                . 'ywx_users.mob as mob,'
         
                . 'IFNULL(ywx_users.name,ywx_users.name_user) as name,'
                
                . 'ywx_users.pics as pics,'
                . 'ywx_users.voice_ids as voice_ids,'
                . 'ywx_users.video_times as video_times,'
                . 'ywx_users.platform as platform,'
                . 'ywx_users.recommend,'
                . 'ywx_users.real_name,'
                . 'ywx_apply_mcs.introduction as mc_introduction,'
                . 'date_format(from_unixtime(ywx_apply_mcs.log_time),"%Y.%m.%d %H:%i:%s") as mc_log_time'
                
                )->join('ywx_user_bank ON ywx_user_bank.uid = ywx_users.id')
                 ->join('left join ywx_apply_mcs ON ywx_apply_mcs.uid = ywx_users.id')
                ->where($param)->select(); 
    }
    
    public function userUpdata($where,$param) {
       return $this->where($where)->save($param);
    }
    
    public function userFind($where) {
       return $this->field(''
                 . 'ywx_users.id as id,'
                . 'DATE_FORMAT(FROM_UNIXTIME(ywx_users.addtime),"%Y.%m.%d") as addtime,'
                . 'ywx_users.follow_ids as follow_ids,'
                
                . 'ywx_users.experience_ids as experience_ids,'
                . 'IFNULL(ywx_users.name_user,name) as headimgurl,'
                . 'IFNULL(ywx_users.headimgurl,headimgurl_user) as headimgurl,'
                . 'ywx_users.industry_ids as industry_ids,'
                . 'ywx_users.initiative as initiative,'
                
                . 'ywx_users.interest_ids as interest_ids,'
                . 'ywx_users.introduction as introduction,'
                . 'ywx_users.job as job,'
                . 'ywx_users.sex as sex,'
                . 'ywx_users.city as city,'
                . 'ywx_users.role_id as role_id,'
                . 'ywx_users.locked as locked,'
                . 'ywx_users.pics as pics,'
                . 'ywx_users.audio as audio,'
                
                . 'date_format(from_unixtime(ywx_users.logintime),"%Y.%m.%d") as logintime,'
                . 'ywx_users.mob as mob,'
         
                . 'IFNULL(ywx_users.name,ywx_users.name_user) as name,'
                
                . 'ywx_users.pics as pics,'
                . 'ywx_users.voice_ids as voice_ids,'
                . 'ywx_users.video_times as video_times,'
                . 'ywx_users.platform as platform'               
                )->where($where)->find();
    }
    
    public function userSelect($where) {
       return $this->where($where)->select();
    }    
}
