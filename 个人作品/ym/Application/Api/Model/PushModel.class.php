<?php
namespace Api\Model;

use Think\Model;

class PushModel {

    public function saveID($registration_id) {
        //已经登陆
        if (isset($_SERVER['HTTP_USERID'])) {
            $data = M('tokens')->where(array('token' => $registration_id, 'uid' => $_SERVER['HTTP_USERID']))->find();
            if ($data) {
                $json_data["state"] = 0;
                $json_data['msg'] = '成功';
                return json_encode($json_data);
            } else {
                try {
                    $save = M('tokens')->add(array('token' => $registration_id, 'uid' => $_SERVER['HTTP_USERID'], 'agent' => $_SERVER['HTTP_USER_AGENT']));
                    $json_data["state"] = 0;
                    $json_data['msg'] = '成功';
                    
                    M('tokens')->where(array('token' => $registration_id,'uid'=>0))->delete();
                    
                    return json_encode($json_data);
                } catch (Exception $exc) {
                    $json_data["state"] = 2;
                    $json_data['msg'] = '数据库记录不成功';
                    return json_encode($json_data);
                }
            }
        } else {
            //没有登录
            $data = M('tokens')->where(array('token' => $registration_id,'uid'=>0))->find();
            if ($data) {
                $json_data["state"] = 0;
                $json_data['msg'] = '成功';
                return json_encode($json_data);
            } else {
                try {
                    $save = M('tokens')->add(array('token' => $registration_id, 'agent' => $_SERVER['HTTP_USER_AGENT']));
                    $json_data["state"] = 0;
                    $json_data['msg'] = '成功';
                    return json_encode($json_data);
                } catch (Exception $exc) {
                    $json_data["state"] = 2;
                    $json_data['msg'] = '数据库记录不成功';
                    return json_encode($json_data);
                }
            }
        }
    }

    public function getRegistrationId($userid) {
        $data = M('tokens')->field('token')->where(array('uid' => $userid))->select();
        $arr = array();
        foreach ($data as $value) {
            $arr[] = $value['token'];
        }
        return $arr;
    }
}
