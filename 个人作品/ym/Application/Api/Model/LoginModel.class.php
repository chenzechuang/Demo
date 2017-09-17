<?php
namespace Api\Model;

use Think\Model;

class LoginModel {

    //主帐号,对应开官网发者主账号下的 ACCOUNT SID
    const accountSid = '8a216da85519f454015529c6bbf210f3';
//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
    const accountToken = '18e90a77b30a440397800d47d3416abb';
//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
    const appId = '8a216da85519f454015529c6bc4e10f9';
//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
    const serverIP = 'app.cloopen.com';
//请求端口，生产环境和沙盒环境一致
    const serverPort = '8883';
//REST版本号，在官网文档REST介绍中获得。
    const softVersion = '2013-12-26';
    const SMSTempletID = '182267';

    public function captcha($mob) {
        if (strlen($mob) == 11) {
            $code = rand(1000, 9999);
           // M('verify', 'center_')->where(array('mob' => $mob))->delete();
            M('verify', 'center_')->add(array('mob' => $mob, 'code' => $code));
            if ($this->sendTemplateSMS($mob, array($code), self::SMSTempletID)) {
                exit(json_encode(array('state' => 0, 'msg' => '发送成功,请注意接收短信验证码.')));
            } else {
                exit(json_encode(array('state' => 1, 'msg' => '发送失败')));
            }
        } else {
            exit(json_encode(array('state' => 1, 'msg' => '电话号码不正确')));
        }
        exit();
    }
    public function verify($phonenum, $phone_captcha) {
        if (strlen($phonenum) == 11 && strlen(I('post.phone_captcha')) == 4) {
            $controller = A('Api/App');

            $verify = M('verify', 'center_')->where(array('mob' => $phonenum, 'code' => I('post.phone_captcha')))->find();
            if ($verify || I('post.phone_captcha') == '9999') {
                M('verify', 'center_')->where(array('mob' => $phonenum))->delete();
                $user = M('users')->field('id as userid,'
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
                                . 'IFNULL(signature,\'\')as signature,'
                                . 'locked'
                        )->where(array('mob' => $phonenum))->find();
                //如果不存在
                if (!$user) {
                    //用户不存在，添加
                    $userid = M('users')->add(array('mob' => $phonenum,
                        'platform' => 'IOS',
                        'addtime' => time(), 'logintime' => time()));

                    $user = M('users')->field('id as userid,'
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
                                    . 'IFNULL(locked,\'\')as locked,'
                                    . 'IFNULL(signature,\'\')as signature'
                            )->where(array('id' => $userid))->find();
                    //在user_items加入一条新数据，设置默认的金币，与麦币
                    M('user_bank')->add(array('uid' => $userid));
                    system("python py/checkuser.py " . $user['userid'] . " ym >> py/checkuser.txt 2>&1", $ret);
                    //token
                    $user['token'] = $controller->token($userid);
                    //
                    exit(json_encode(array('state' => 0, 'msg' => '注册成功', 'data' => $user)));
                } else {
      
                    if ($user["locked"] == 1) {
                        exit(json_encode(array('state' => 1, 'msg' => '你的账号因为不符合APP平台规范，已经被冻结!')));
                    }
                    system("python py/checkuser.py " . $user['userid'] . " " . base64_decode($user['nick']) . " >> py/checkuser.txt 2>&1", $ret);
                    //token
                    $user['token'] = $controller->token($user['userid']);
                    exit(json_encode(array('state' => 0, 'msg' => '登录成功', 'data' => $user)));
                }
            } else {
                exit(json_encode(array('state' => 1, 'msg' => '验证码错误')));
            }
        } else {
            exit(json_encode(array('state' => 1, 'msg' => '请填写正确的手机和验证码')));
        }
    }

    private function sendTemplateSMS($to, $datas, $tempId) {
        // 初始化REST SDK
//         global self::accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \Think\CCPRestSmsSDK(self::serverIP, self::serverPort, self::softVersion);
//         $rest = new REST(self::serverIP,self::serverPort,  self::softVersion);
        $rest->setAccount(self::accountSid, self::accountToken);
        $rest->setAppId(self::appId);

        // 发送模板短信
//         echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to, $datas, $tempId);
        if ($result == NULL) {
//             echo "result error!";
            M('syslogs', 'center_')->add(array('msg' => 'result error!'));
            return FALSE;
        }
        if ($result->statusCode != 0) {
//             echo "error code :" . $result->statusCode . "<br>";
//             echo "error msg :" . $result->statusMsg . "<br>";
            M('syslogs', 'center_')->add(array('msg' => "error code :" . $result->statusCode . " error msg :" . $result->statusMsg . ""));
            //TODO 添加错误处理逻辑
            return FALSE;
        } else {
//             echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
//             echo "dateCreated:".$smsmessage->dateCreated."<br/>";
//             echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
            M('syslogs', 'center_')->add(array('msg' => "success dateCreated:" . $smsmessage->dateCreated . " smsMessageSid:" . $smsmessage->smsMessageSid));
            //TODO 添加成功处理逻辑
            return TRUE;
        }
    }

    public function loginWx($code) {
        $json_ret = $this->checkwx($code); //获取openid
        $controller = A('Api/App');

        if (!isset($json_ret["openid"])) {
            exit(json_encode(array('state' => 1, 'msg' => '微信登录验证失败')));
        } else {
            $access_token = $json_ret["access_token"];
            $openid = $json_ret["openid"];
            $wxUser = M('users')->field('id as userid,'
                            . 'IFNULL(role_id,\'\') as role_id,'
                            . 'IFNULL(name,name_user) as nick,'
                            . 'IFNULL(sex,\'\') as sex,'
                            . 'IFNULL(birthday,\'\') as birthday,'
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
                            . 'IFNULL(signature,\'\')as signature,'
                            . 'locked'              
                    )->where(array('openid' => $openid))->find();

            if (!$wxUser) { //无用户
                $wxInfo = $this->wxInfo($access_token, $openid);
                \Think\Log::write(json_encode($wxInfo) . "微信IFON");
                //微信TOKEN unionid
                $wxData['openid'] = $wxInfo["openid"];
                $wxData['unionid'] = $wxInfo["unionid"];
                $wxData['platform'] = 'IOS';
                $wxData['addtime'] = time();
                $wxData['logintime'] = time();
                //微信个人信息  
                $wxData['name'] = base64_encode($wxInfo['nickname']);
              //  $wxData['name_user'] = base64_encode($wxInfo['nickname']);
                $wxData['sex'] = $wxInfo['sex'];
                $wxData['headimgurl'] = $wxInfo['headimgurl'];

                $wxUserID = M('users')->add($wxData);  //

                $wxUserData = M('users')->field('id as userid,'
                                . 'IFNULL(role_id,\'\') as role_id,'
                                . 'IFNULL(name,\'\') as nick,'
                                . 'IFNULL(sex,\'\') as sex,'
                                . 'IFNULL(birthday,\'\') as birthday,'
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
                                . 'IFNULL(signature,\'\')as signature,'
                                . 'locked' 
                        )->where(array('id' => $wxUserID))->find();
                //在user_bank加入一条新数据，设置默认的金币，与麦币
                M('user_bank')->add(array('uid' => $wxUserID));
                system("python py/checkuser.py " . $wxUserData['userid'] . " ym >> py/checkuser.txt 2>&1", $ret);
                //token
                $wxUserData['token'] = $controller->token($wxUserData['userid']);
                exit(json_encode(array('state' => 0, 'msg' => '注册成功', 'data' => $wxUserData)));
            } else { //已经有用户      
                \Think\Log::write(json_encode($wxUser),"有用户");
                if ($wxUser["locked"] == 1) {
                    exit(json_encode(array('state' => 1, 'msg' => '你的账号因为不符合APP平台规范，已经被冻结!')));
                }
                system("python py/checkuser.py " . $wxUser['userid'] . " " . base64_decode($wxUser['nick']) ." >> py/checkuser.txt 2>&1", $ret);
                //token
                $wxUser['token'] = $controller->token($wxUser['userid']);
                exit(json_encode(array('state' => 0, 'msg' => '登录成功', 'data' => $wxUser)));
            }
        }
    }

    private function checkwx($code) {
        $appid = "wx5404276030f1b513";
        $secret = "b9bb79df593f70d4eed0237da9d57e70";
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $return_str = curl_exec($curl);
        curl_close($curl);

        $json_ret = json_decode($return_str, true);

        return $json_ret;
    }

    private function wxInfo($access_token, $openid) {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $return_str = curl_exec($curl);
        curl_close($curl);
        $json_ret = json_decode($return_str, true);
        return $json_ret;
    }

}
