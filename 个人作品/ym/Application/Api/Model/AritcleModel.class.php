<?php

namespace Api\Model;

use Think\Model;
header("Content-Type: text/html; charset=UTF-8");
class AritcleModel extends Model {

    public function articleListDefault() {
        $data = M('aritcle_channel')
                ->field('id as topic_id,'
                        . 'title as name,'
                        . 'img_url as pic,'
                        . 'article_row as count,'
                        . 'type')
                ->select();
        foreach ($data as $key => $value) {
            $data[$key]["pic"] = "http://" . $_SERVER['HTTP_HOST'] . str_replace("./", "/", $value['pic']);
        }
        $json_data["data"] = array('topic_list' => $data);
        $json_data["state"] = 0;
        $json_data['msg'] = '';
        return json_encode($json_data);
    }

    public function articleList($start, $channel, $openid) {
        $where = array();
        if($channel == "-1"){
            $where["ywx_aritcle.type"] = "1";
        }else if($channel == "0"){
            $where["ywx_aritcle.type"] = "0";
        }
        
        $version['id'] = array('in','12,14');
        $config = M('config')->field('value')->where($version)->select();

        if( $config[1]["value"] == "1" && $config[0]["value"] == $_SERVER["HTTP_VERSION"]){
            $where["ywx_aritcle.edition"] = "0";
        }
        
        $aritcle_data = M('aritcle')->join('ywx_users ON ywx_users.id = ywx_aritcle.uid')
                ->join('left join ywx_users_mcs ON ywx_users_mcs.uid = ywx_users.id')
                ->join('left join ywx_aritcle_channel ON ywx_aritcle.channel = ywx_aritcle_channel.id')
//                ->where('select * FROM ywx_users_mcs where ywx_users.id = ywx_users_mcs.uid')
                ->field('ywx_aritcle.id as topic_id,'
                        . 'IFNULL(ywx_users.gift_row,\'\') as gift_row,'
                        . 'ywx_aritcle.type as type,'
                        . 'ywx_aritcle.cover as cover,'
                        . 'ywx_aritcle.content as content,'
                        . 'ywx_users.role_id as role_id,'
                        . 'ywx_aritcle.pics as pics,'
                        . 'ywx_aritcle.audio,ywx_aritcle.uid as userid,'
                        . 'ywx_aritcle_channel.title as topic_name,'
                        . 'IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'unix_timestamp(ywx_aritcle.log_time) as create_time,'
                        . 'ywx_aritcle.listen as listen_count,'
                        . 'ywx_aritcle.praise as praise_count,'
                        . 'ywx_aritcle.comments as comment_count,'
                        . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'audio_lite,'
                        . 'audio_full,'
                        . 'ywx_users.voice_time')
                ->where($where)
                ->order('ywx_users.role_id desc ,ywx_aritcle.log_time desc')
                ->limit($start * 10, 10)
                ->select(); 
        
        foreach ($aritcle_data as $key => $value) {
            $message = M('message')->field(''
                    . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick, '
                    . 'IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                    . 'ywx_users.id as userid,'
                    . 'ywx_users.role_id as role_id,'
                    . 'ywx_message.type as comment_type,'
                    . 'ywx_message.article_id as commented_user,'
                    . 'ywx_message.content as comment_content,'
                    . 'ywx_message.audio as comment_mp3,'
                    . 'ywx_message.audio_time as comment_mp3_time,'
                    . 'unix_timestamp(ywx_message.log_time) as comment_time'
                    . '')
                    ->join('ywx_users on ywx_users.id = ywx_message.uid')
                    ->where(array('article_id'=>$value['topic_id']))
                    ->order('log_time desc')
                    ->limit(2)
                    ->select();
            
            foreach ($message as $m => $v) {
              if(!empty($v["comment_content"])){
                $message[$m]["comment_content"] = base64_decode($v["comment_content"]);
               }           
               $message[$m]["comment_user"]["nick"] =  base64_decode($v["nick"]);
               $message[$m]["comment_user"]["photo_url"] = $v["photo_url"];
               $message[$m]["comment_user"]["role_id"]  = $v["role_id"];
               $message[$m]["comment_user"]["userid"] = $v["userid"];
               unset($message[$m]["nick"]);
               unset($message[$m]["photo_url"]);
               unset($message[$m]["role_id"]);
               unset($message[$m]["userid"]);
            }
            
            

         //   $message["nick"] = base64_decode($message["nick"]);
            $aritcle_data[$key]['message'] = $message;
            $aritcle_data[$key]["pics"] = json_decode(htmlspecialchars_decode($value['pics']));
            $aritcle_data[$key]["nick"] = base64_decode($aritcle_data[$key]["nick"]);
            $aritcle_data[$key]["content"] = base64_decode($aritcle_data[$key]["content"]);
            $aritcle_data[$key]["cover"] = str_replace("./", "/", $value['cover']);
//            var_dump($openid,$value['userid'],M('follow')->where(array('uid' => $openid, 'mcs_id' => $value['userid']))->count());
            $aritcle_data[$key]['follow'] = M('follow')->where(array('uid' => $openid, 'mcs_id' => $value['userid']))->count() > 0 ? '1' : '0';
            $aritcle_data[$key]['praised'] = M('praise')->where(array('uid' => $openid, 'aritcle_id' => $value['topic_id']))->count() > 0 ? '1' : '0';
        }
        $json_data['data'] = array('dynamic_list' => $aritcle_data);
        $json_data["state"] = 0;
        $json_data['msg'] = '';
        return json_encode($json_data);
    }

    public function bannerList() {
        $data = array();
        $data['dynamic_banner_list'] = M('banner')->field('name,pic,url')->order('sort desc')->select();
        $newData = array();
        $k = 0;
//        foreach ($data['dynamic_banner_list'] as $k => $value) {
//            $data['dynamic_banner_list'][$k]["pic"] = "http://" . $_SERVER['HTTP_HOST'] . str_replace("./", "/", $value['pic']);
//        }
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function aritcleAdd($param) {
        if (strlen($param['content']) > 800) {
            return json_encode(array('state' => 1, 'msg' => '动态文字过长！'));
        }
        /*
          $pics = json_decode($param['pics'],TRUE);
          if(count($pics) == 0){
          return json_encode(array('state' => 1,'msg'=>'图片数量不能少于1张！'));
          }
         */
        \Think\Log::write(json_encode(I('post.')));
        $addData = array();
        $addData['uid'] = $param['userid'];
        $addData['channel'] = $param['topic_id'];
        $addData['cover'] = $param['cover'];
        $addData['pics'] = $param['pics']; 
        $addData['content'] = base64_encode($param['content']);
        $addData['type'] = $param['type'];
        $addData['log_time'] = date("Y-m-d H:i:s");

        M('users')->where(array('id' => $param['userid']))->setInc('article_row', 1);
        M('aritcle_channel')->where(array('id' => $param['topic_id']))->setInc('article_row', 1);

        $data = array();
        $data['aritcleId'] = M('aritcle')->add($addData);
        return json_encode(array('state' => 0, 'msg' => '发布成功！', 'data' => $data));
    }

    public function aritcleDel($param) {
        if (!$param['userid']) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }

        if (!$param['create_time']) {
            return json_encode(array('state' => 1, 'msg' => 'no time！'));
        }

        $create_time = date('Y-m-d H:i:s', $param['create_time']);
        M('aritcle')->where(array('log_time' => $create_time, 'uid' => $param['userid']))->delete();
        M('users')->where(array('id' => $param['userid']))->setDec('article_row', 1);
        M('aritcle_channel')->where(array('id' => $param['topic_id']))->setDec('article_row', 1);
        return json_encode(array('state' => 0, 'msg' => '操作成功！'));
    }

    public function userAritcleList($userid,$uid, $range) {
        $where = array();
        
        $version['id'] = array('in','12,14');
        $config = M('config')->field('value')->where($version)->select();

        if( $config[1]["value"] == "1" && $config[0]["value"] == $_SERVER["HTTP_VERSION"]){
            $where["ywx_aritcle.edition"] = "0";
        }
        
        $where["ywx_users.id"] = $uid;
        $aritcle_data = M('aritcle')->join('ywx_users ON ywx_users.id = ywx_aritcle.uid')
               // ->join('left join ywx_users_mcs ON ywx_users_mcs.uid = ywx_users.id')
//                ->where('select * FROM ywx_users_mcs where ywx_users.id = ywx_users_mcs.uid')
                ->field('ywx_aritcle.id as topic_id,'
                        . 'ywx_aritcle.channel as channel,'
                        . 'ywx_aritcle.type as type,'
                        . 'ywx_aritcle.cover as cover,'
                        . 'ywx_aritcle.content as content,'
                        . 'ywx_users.role_id as role_id,'
                        . 'ywx_aritcle.pics,'
                        . 'ywx_aritcle.audio,ywx_aritcle.uid as userid,'
                        . 'ywx_aritcle.title as topic_name,'
                        . 'IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                        . 'unix_timestamp(ywx_aritcle.log_time) as create_time,'
                        . 'ywx_aritcle.listen as listen_count,'
                        . 'ywx_aritcle.praise as praise_count,'
                        . 'ywx_aritcle.comments as comment_count,'
                        . '.ywx_aritcle.audio_lite as audio_lite,'
                        . '.ywx_aritcle.audio_full as audio_full,'
                        . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.voice_time')
                ->where($where)
                ->order('ywx_aritcle.log_time desc')
                ->limit($range * 10, 10)
                ->select();
        foreach ($aritcle_data as $key => $value) {
            $aritcle_data[$key]["nick"] = base64_decode($aritcle_data[$key]["nick"]);
            $aritcle_data[$key]["content"] = base64_decode($aritcle_data[$key]["content"]);
            
            $aritcle_data[$key]["pics"] = json_decode(htmlspecialchars_decode($value['pics']));
            $aritcle_data[$key]["cover"] =str_replace("./", "/", $value['cover']);
            $aritcle_data[$key]['follow'] = M('follow')->where(array('uid' => $_SERVER["HTTP_USERID"], 'mcs_id' => $value['userid']))->count() > 0 ? '1' : '0';
            $aritcle_data[$key]['praised'] = M('praise')->where(array('uid' => $userid, 'aritcle_id' => $value['topic_id']))->count() > 0 ? '1' : '0';
        }
        $json_data = array('dynamic_list' => $aritcle_data);
        $json_data["state"] = 0;
        $json_data['msg'] = '';
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $json_data));
    }

    public function piccover() {
        $data = array();
        $data = M('aritcle_channel')->field('img_url as dynamic_pic_cover, article_row as dynamic_pic_count ,title as name')->find();
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function Articleinfo($userid,$uid,$create_time) {
        $create_time = date('Y-m-d H:i:s', $create_time);
        $data = array();
        $data = M('aritcle')->join('ywx_users ON ywx_users.id = ywx_aritcle.uid')
                ->join('left join ywx_aritcle_channel ON ywx_aritcle.channel = ywx_aritcle_channel.id')
//                ->where('select * FROM ywx_users_mcs where ywx_users.id = ywx_users_mcs.uid')
                ->field('ywx_aritcle.id as topic_id,'
                        . 'ywx_aritcle.type as type,'
                        . 'ywx_aritcle.audio_lite as audio_lite,'
                        . 'ywx_aritcle.audio_full as audio_full,'
                        . 'ywx_aritcle.cover as cover,'
                        . 'ywx_aritcle.content as content,'
                        . 'ywx_users.role_id as role_id,'
                        . 'ywx_aritcle.pics as pics,'
                        . 'ywx_aritcle.uid as userid,'
                        . 'ywx_aritcle_channel.title as topic_name,'
                        . 'IFNULL(ywx_users.headimgurl,ywx_users.headimgurl_user) as photo_url,'
                        . 'unix_timestamp(ywx_aritcle.log_time) as create_time,'
                        . 'ywx_aritcle.listen as listen_count,'
                        . 'ywx_aritcle.praise as praise_count,'
                        . 'ywx_aritcle.comments as comment_count,'
                        . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.voice_time')
                ->where(array('ywx_aritcle.uid' => $uid, 'ywx_aritcle.log_time' => $create_time))
                ->find();
            $data["follow"] = M('follow')->where(array('uid'=>$userid,'mcs_id'=>$uid))->count();
            $data["nick"] = base64_decode($data['nick']);   
            $data["content"] = base64_decode($data['content']);
            $data["pics"] = json_decode(htmlspecialchars_decode($data['pics']));
            $data["cover"] =  str_replace("./", "/", $data['cover']);

        $json_data = array('dynamic_list' => $data);
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $json_data));
    }

    public function listenclick($userid, $create_time) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }
        if (!$create_time) {
            return json_encode(array('state' => 1, 'msg' => 'no time！'));
        }

        $create_time = date('Y-m-d H:i:s', $create_time);
        M('aritcle')->where(array('uid' => $userid, 'log_time' => $create_time))->setInc('listen', 1);
        return json_encode(array('state' => 0, 'msg' => '操作成功'));
    }

    public function attentionlist($userid, $range) {
        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no user！'));
        }

        if (!$range) {
            $range = 0;
        }

        $aritcle_data = M('follow')->field('ywx_aritcle.id as topic_id,'
                        . 'ywx_aritcle.type as type,'
                        . 'ywx_aritcle.cover as cover,'
                        . 'ywx_aritcle.content as content,'
                        . 'ywx_users.role_id as role_id,'
                        . 'ywx_aritcle.img_url as pics,'
                        . 'ywx_aritcle.audio,ywx_aritcle.uid as userid,'
                        . 'ywx_aritcle.title as topic_name,'
                        . 'IFNULL(ywx_users.headimgurl,ywx_users.headimgurl_user) as photo_url,'
                        . 'unix_timestamp(ywx_aritcle.log_time) as create_time,'
                        . 'ywx_aritcle.listen as listen_count,'
                        . 'ywx_aritcle.praise as praise_count,'
                        . 'ywx_aritcle.comments as comment_count,'
                        . 'IFNULL(ywx_users.name_user,ywx_users.name) as nick,'
                        . 'ywx_users.voice_time')
                ->join('ywx_users on ywx_users.id = ywx_follow.uid')
                ->join('ywx_aritcle on ywx_aritcle.uid = ywx_users.id')
                ->where(array('ywx_follow.uid' => $userid))
                ->limit($range * 10, 10)
                ->select();

        foreach ($aritcle_data as $key => $value) {
            $aritcle_data[$key]["pics"] = json_decode(htmlspecialchars_decode($value['pics']));
            $aritcle_data[$key]["cover"] = str_replace("./", "/", $value['cover']);
        }
        $json_data['data'] = array('dynamic_list' => $aritcle_data);
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $json_data));
    }

    public function comment() {
        $post = I('post.');
        if (!$post['userid']) {
            return json_encode(array('state' => 1, 'msg' => 'no user'));
        }
        if (!$post['commented_uid']) {
            return json_encode(array('state' => 1, 'msg' => 'no commented uid'));
        }
        
        
        $PostData['uid'] = $post['userid'];
        $PostData['cover_uid'] = $post['commented_uid'];
        $PostData['type'] = $post['comment_type'];
        $PostData['log_time'] = date('Y-m-d H:i:s',$post['create_time']);
        $PostData['content'] = base64_encode($post['comment_content']);
        $PostData['audio'] = $post['comment_mp3'];
        $PostData['audio_time'] = $post['comment_mp3_time'];
        $aritcle = M('aritcle')->where(array('log_time'=>$PostData['log_time'],'uid'=>$post['commented_uid']))->find();
        $PostData['article_id'] = $aritcle['id'];
        $PostData['log_time'] = date('Y-m-d H:i:s', time()); //添加评论时间

        M('message')->add($PostData);
        M('aritcle')->where(array('id'=>$aritcle['id']))->setInc('comments',1);
        return json_encode(array('state' => 0, 'msg' => '操作成功！'));
    }

    public function praise($userid, $uid, $create_time) {

        if (!$userid) {
            return json_encode(array('state' => 1, 'msg' => 'no userid！'));
        }
        if (!$uid) {
            return json_encode(array('state' => 1, 'msg' => 'no uid！'));
        }
        if (!$create_time) {
            return json_encode(array('state' => 1, 'msg' => 'no time！'));
        }
      /*  if($userid == $uid){
            return json_encode(array('state' => 1, 'msg' => '不能给自己赞！'));
        }*/
        $create_time = date("Y-m-d H:i:s", $create_time);
        $aritcleData = M('aritcle')->where(array('uid' => $uid, 'log_time' => $create_time))->find();
        $count = M('praise')->where(array('uid' => $userid, 'aritcle_id' => $aritcleData['id']))->count();
        if($count > 0){
            return json_encode(array('state' => 1, 'msg' => '你已经点赞过了！'));
        }
        M('aritcle')->where(array('id'=>$aritcleData["id"]))->setInc('praise',1);
        M('praise')->add(array('uid' => $userid,'cover_uid'=>$uid, 'aritcle_id' => $aritcleData['id'], 'log_time' => date("Y-m-d H:i:s")));
        return json_encode(array('state' => 0, 'msg' => '操作成功！'));
    }

    public function commentlist($range, $uid, $create_time) {
        if (!$range) {
            $range = 0;
        }
        if (!$uid) {
            return json_encode(array('state' => 1, 'msg' => 'no uid！'));
        }
        if (!$create_time) {
            return json_encode(array('state' => 1, 'msg' => 'no time！'));
        }else{
            $create_time = date("Y-m-d H:i:s",$create_time);
        }

         $aritcleData = M('aritcle')->where(array('uid' => $uid, 'log_time' => $create_time))->find();

        if (!$aritcleData['id']) {
            return json_encode(array('state' => 1, 'msg' => '无文章！'));
        }

       $msgData = M('message')
                ->field('ywx_users.id as id,'
                        . 'IFNULL(name_user,name) as nick, '
                        . 'IFNULL(headimgurl_user,headimgurl) as photo_url,'
                        . 'role_id,'
                        . 'ywx_message.article_id as commented_user,'
                        . 'ywx_message.content as comment_content,'
                        . 'IFNULL(ywx_message.type,"0") as comment_type,'
                        . 'ywx_message.audio as comment_mp3,'
                        . 'ywx_message.audio_time as comment_mp3_time,'
                        . 'unix_timestamp(ywx_message.log_time) as comment_time')
                ->join('ywx_users on ywx_users.id = ywx_message.uid')
                ->where(array('ywx_message.article_id' => $aritcleData['id']))->order('ywx_message.id desc')->limit(10*$range,10)->select();

      foreach ($msgData as $k => $value) {
              $msgData[$k]["comment_user"]["nick"] = base64_decode($value["nick"]);
              $msgData[$k]["comment_user"]["photo_url"] = $value["photo_url"];
              $msgData[$k]["comment_user"]["role_id"] = $value["role_id"];
              $msgData[$k]["comment_user"]["userid"] = $value["id"];
          
              if($value["comment_type"] == "0"){
                 $msgData[$k]["comment_content"] = base64_decode($value["comment_content"]); 
              }
              unset($msgData[$k]["photo_url"]);
              unset($msgData[$k]["nick"]);
              unset($msgData[$k]["role_id"]);
              
         }

        
        $out = array();
        $out['comment_list'] = $msgData;
        \Think\Log::write(json_encode($msgData));
      //   print_r($msgData);
      //  var_dump($out); echo json_encode($out);
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $out));
    }

    public function relatedlist($range, $userid, $related_type) {
        if(!$range){
            $range = 0;
        }
        
        if(!$userid){
            return json_encode(array('state' => 1, 'msg' => 'no user！'));
        } 
        
        $data = array();
        if($related_type == "2"){

            $selectData  = M('message')->field(
                                     'ywx_users.id as userid,'
                                    . 'IFNULL(ywx_users.role_id,\'\') as role_id,'
                                    . 'IFNULL(ywx_users.name_user,\'\') as nick,'
                                    . 'IFNULL(ywx_users.sex,\'\') as sex,'
                                    . 'IFNULL(ywx_users.birthday,\'\') as birthday,'
                                    . 'IFNULL(ywx_users.city,\'\') as city,'
                                    . 'IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url,'
                                    . 'IFNULL(ywx_users.mob,\'\') as phone_num,'
                                    . 'IFNULL(ywx_users.wx_id,\'\') as wx_id ,'
                                    . 'IFNULL(ywx_users.introduction,\'\') as introduction,'
                                    . 'IFNULL(ywx_users.interest_ids,\'\')as interest_ids,'
                                    . 'IFNULL(ywx_users.language_ids,\'\')as language_ids,'
                                    . 'IFNULL(ywx_users.voice_ids,\'\')as voice_ids,'
                                    . 'IFNULL(ywx_users.pics,\'\')as pics,'
                                    . 'IFNULL(ywx_users.voice_url,\'\')as voice_url,'
                                    . 'IFNULL(ywx_users.check_status,\'\')as check_status,'
                                    . 'IFNULL(ywx_users.check_msg,\'\')as check_msg,'
                                    . 'IFNULL(ywx_users.real_name,\'\')as real_name,'
                                    . 'IFNULL(ywx_users.job,\'\')as job,'
                                    . 'IFNULL(ywx_users.completeness,\'\')as completeness,'
                                    . 'IFNULL(ywx_users.can_video,\'\')as can_video,'
                                    . 'IFNULL(ywx_users.can_voice,\'\')as can_voice,'
                                    . 'IFNULL(ywx_users.video_fee,\'\')as video_fee,'
                                    . 'IFNULL(ywx_users.voice_fee,\'\')as voice_fee,'
                                    . 'IFNULL(ywx_users.shortest_time,\'\')as shortest_time,'
                                    . 'IFNULL(ywx_users.video_times,\'\')as video_times,'
                                    . 'IFNULL(ywx_users.video_time,\'\')as video_time,'
                                    . 'IFNULL(ywx_users.signature,\'\')as signature,'
                                    . 'unix_timestamp(ywx_message.log_time) as create_time' 
                            )->join('ywx_users on ywx_users.id  = ywx_message.uid')
                        ->where(array('ywx_message.cover_uid' => $userid))
                        ->order('ywx_message.id desc')
                        ->limit(10 * $range, 10)
                        ->select();
     
                foreach ($selectData as $k => $value) {
                   
                    $data["comment_list"][$k]["create_time"] = $value["create_time"];
                    $data["comment_list"][$k]["related_type"] = "2";
                    $data["comment_list"][$k]["initiativer"] = $value;
                    $data["comment_list"][$k]["initiativer"]["nick"] = base64_decode($value["nick"]);
                    unset($data["comment_list"][$k]["initiativer"]["create_time"]);
                }
          
        }
    
        if($related_type == 1){

                $selectData = M('praise')->field(
                                    'ywx_users.id as userid,'
                                    . 'IFNULL(ywx_users.role_id,\'\') as role_id,'
                                    . 'IFNULL(ywx_users.name_user,\'\') as nick,'
                                    . 'IFNULL(ywx_users.sex,\'\') as sex,'
                                    . 'IFNULL(ywx_users.birthday,\'0\') as birthday,'
                                    . 'IFNULL(ywx_users.city,\'\') as city,'
                                    . 'IFNULL(ywx_users.headimgurl_user,\'\') as photo_url,'
                                    . 'IFNULL(ywx_users.mob,\'\') as phone_num,'
                                    . 'IFNULL(ywx_users.wx_id,\'\') as wx_id ,'
                                    . 'IFNULL(ywx_users.introduction,\'\') as introduction,'
                                    . 'IFNULL(ywx_users.interest_ids,\'\')as interest_ids,'
                                    . 'IFNULL(ywx_users.language_ids,\'\')as language_ids,'
                                    . 'IFNULL(ywx_users.voice_ids,\'\')as voice_ids,'
                                    . 'IFNULL(ywx_users.pics,\'\')as pics,'
                                    . 'IFNULL(ywx_users.voice_url,\'\')as voice_url,'
                                    . 'IFNULL(ywx_users.check_status,\'\')as check_status,'
                                    . 'IFNULL(ywx_users.check_msg,\'\')as check_msg,'
                                    . 'IFNULL(ywx_users.real_name,\'\')as real_name,'
                                    . 'IFNULL(ywx_users.job,\'\')as job,'
                                    . 'IFNULL(ywx_users.completeness,\'\')as completeness,'
                                    . 'IFNULL(ywx_users.can_video,\'\')as can_video,'
                                    . 'IFNULL(ywx_users.can_voice,\'\')as can_voice,'
                                    . 'IFNULL(ywx_users.video_fee,\'\')as video_fee,'
                                    . 'IFNULL(ywx_users.voice_fee,\'\')as voice_fee,'
                                    . 'IFNULL(ywx_users.shortest_time,\'\')as shortest_time,'
                                    . 'IFNULL(ywx_users.video_times,\'\')as video_times,'
                                    . 'IFNULL(ywx_users.video_time,\'\')as video_time,'
                                    . 'IFNULL(ywx_users.signature,\'\')as signature,'
                                    . 'unix_timestamp(ywx_praise.log_time) as create_time'
                            )->join('ywx_users on ywx_users.id  = ywx_praise.uid')
                        ->where(array('ywx_praise.cover_uid' => $userid))
                        ->order('ywx_praise.id desc')
                        ->limit(10 * $range, 10)
                        ->select();

            foreach ($selectData as $k => $value) {
                
                $data["comment_list"][$k]["create_time"] = $value['create_time'];
                $data["comment_list"][$k]["related_type"] = "2";
                $data["comment_list"][$k]["initiativer"] = $value;
                $data["comment_list"][$k]["initiativer"]["nick"] = base64_decode($value["nick"]);
                unset($data["comment_list"][$k]["initiativer"]["create_time"]);
            }
        }

        if(count($data)==0){
            $data["comment_list"][] = NULL;
        }
       
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }
    
    public function share($uid,$create_time) { 
            $aritcle_data = M('aritcle')->join('ywx_users ON ywx_users.id = ywx_aritcle.uid')
                            ->field('ywx_aritcle.type,ywx_aritcle.pics,ywx_aritcle.audio_full,ywx_aritcle.audio_lite,ywx_aritcle.comments,ywx_aritcle.id as aid,ywx_aritcle.content,ywx_aritcle.cover,DATE_FORMAT(ywx_aritcle.log_time,\'%m-%d\') as log_time,ywx_aritcle.listen,ywx_aritcle.audio,ywx_users.name,name_user,ywx_users.headimgurl,ywx_users.headimgurl_user')
                            ->where(array('ywx_aritcle.uid'=>$uid,'ywx_aritcle.log_time'=>$create_time))
                            ->find();
            $aritcle_data["error"] = "0";
            $pics = htmlspecialchars_decode($aritcle_data["pics"]);
            $pics = json_decode($pics,TRUE);
            
            $aritcle_data["audio"] = $pics;
            $aritcle_data["content"]= base64_decode($aritcle_data["content"]);
            $aritcle_data["name_user"]= base64_decode($aritcle_data["name_user"]);
            return $aritcle_data;
    }
    
    public function shareMsgList($uid,$create_time,$range) {
            $findData = M('aritcle')->where(array('uid'=>$uid,'log_time'=>$create_time))->find();
            $aritcle_data["lists"] = M('message')->join('ywx_users ON ywx_message.uid = ywx_users.id')
                    ->field('ywx_message.audio_time,ywx_message.audio,ywx_message.id as msid,ywx_message.content,ywx_message.log_time,ywx_users.name,ywx_users.name_user,ywx_users.headimgurl,ywx_users.headimgurl_user')
                    ->where(array('ywx_message.article_id' => $findData['id']))
                    ->limit($range, 10)
                    ->order('ywx_message.id desc')
                    ->select();
            foreach ($aritcle_data["lists"] as $k=>$value) {
                $aritcle_data["lists"][$k]["name_user"] = base64_decode($value["name_user"]);

                if(!empty($value["content"])){
                     $aritcle_data["lists"][$k]["content"] = base64_decode($value["content"]);
                }
                
            }
            return $aritcle_data;  
    }    

    public function exchangeGift($userid,$uid,$commodity_id,$topic_id,$row) {
        
            if(empty($userid)){
                 return json_encode(array('state' => 1, 'msg' => 'no Userid！'));
            }
            
            if(empty($uid)){
                 return json_encode(array('state' => 1, 'msg' => 'no Uid！'));
            }
            
            if(empty($uid)){
                 return json_encode(array('state' => 1, 'msg' => 'no commodity！'));
            }         
            if(empty($row)){
                $row = "1";
            }
            
            $userBank = M('user_bank')->where(array('uid'=>$userid))->find();
            $commodity = M('items')->where(array('id'=>$commodity_id))->find();
            $commodity["price"] = json_decode($commodity["price"],TRUE);
            $describe = '';
            if($row){
                $commodity["price"]["mz"] = $commodity["price"]["mz"] * $row; //价钱 = 价钱 * 数量
              //  $describe = "x".$row;
            }
            
            if($userBank["deposit"] < $commodity["price"]["mz"]){  // 查看用户是否够钱
                return json_encode(array('state' => 2, 'msg' => '您的货币不足！'));
            }
            
            M('user_bank')->where(array('uid'=>$userid))->setDec('deposit',$commodity["price"]["mz"]);
            //送礼物的减钱
            $jf_rate = M('app_config')->field('val')->where(array('id' => 12))->find();    //添加比例

            $coin = $commodity["price"]["mz"] * ($jf_rate["val"]/100);  
            
            M('user_bank')->where(array('uid'=>$uid))->setInc('coin',$coin);
            //收礼物加钱
            $data[0]["uid"] = $userid;
            $data[0]["deposit"] = "-".$commodity["price"]["mz"];
            $data[0]["coin"] = "0";
            $data[0]["create_time"] = time();
            $data[0]["describe"] = $commodity["name"];
            $data[0]["type"] = $commodity["id"];
            $data[0]["source"] = "3";
            $data[0]["icon"] = $commodity["icon"];
            //送礼减钱账单
            
            $data[1]["uid"] = $uid;
            $data[1]["deposit"] = "0";
            $data[1]["coin"] = $coin;
            $data[1]["create_time"] = time();
            $data[1]["describe"] = $commodity["name"];
            $data[1]["type"] = $commodity["id"];
            $data[1]["source"] = "4";  
            $data[1]["icon"] = $commodity["icon"];
            //收礼的账单           
            M('user_bill')->AddAll($data);
            
            $useridData = M('users')->field('IFNULL(name_user,name) as name')->where(array('id'=>$userid))->find(); //送礼物人的信息
            $pushName = base64_decode($useridData['name']);
            $apiCrl = A('Api/Push');
            $sms = $apiCrl->pushMsg($uid,$pushName."给你送了个礼物，快来看下吧","","");//userid 和 内容
            \Think\Log::write("SMS礼物".$sms.$uid.$useridData['name']);
            
            $giftData["userid"] = $userid;
            $giftData["uid"] = $uid;
            $giftData["aritcle_id"] = $topic_id;
            $giftData["commodity_id"] = $commodity["id"];
            $giftData["row"] = $row;
            $giftData["price"] = $commodity["price"]["mz"];
            $giftData["create_time"] = time();
            $giftData["describe"] = $commodity["name"];
            $giftData["icon"] = $commodity["icon"];
            $giftData["img"] = $commodity["img"];
            //送礼记录
            M('user_gift')->add($giftData);
            //添加送礼总数

            M('users')->where(array('id'=>$uid))->setInc('gift_row',$coin); 
            M('users')->where(array('id'=>$userid))->setInc('give_gift_row',$coin); //记录用户送多少礼物
            return json_encode(array('state' => 0, 'msg' => '送礼成功！'));

    } 
    
    public function userGift($aritcle_id,$type,$range,$userid) {
        if($type == "0"){
            $where["ywx_user_gift.uid"] = $userid;
             $userGift["giftList"] = M('user_gift')
                    ->field('ywx_user_gift.describe,ywx_user_gift.price,ywx_user_gift.create_time,ywx_user_gift.row,IFNULL(ywx_users.name_user,ywx_users.name) as nick,IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url')
                    ->join('ywx_users on ywx_users.id = ywx_user_gift.userid')
                    ->where($where)
                    ->order('ywx_user_gift.id desc')
                    ->limit(3)
                    ->select();
        }else{
            $where["ywx_user_gift.uid"] = $userid; // 收到的礼物
            $userGift["giftList"] = M('user_gift')
                    ->field('ywx_users.role_id,ywx_user_gift.icon,ywx_user_gift.describe,ywx_user_gift.img,ywx_user_gift.price,ywx_user_gift.create_time,ywx_user_gift.row,IFNULL(ywx_users.name_user,ywx_users.name) as nick,IFNULL(ywx_users.headimgurl_user,ywx_users.headimgurl) as photo_url')
                    ->join('ywx_users on ywx_users.id = ywx_user_gift.userid')
                    ->where($where)
                    ->order('ywx_user_gift.id desc')
                    ->limit($range*10,10)
                    ->select();           
        }

        foreach ($userGift["giftList"] as $key => $value) {
            $userGift["giftList"][$key]['nick'] = base64_decode($value['nick']);
        }
        
        return json_encode(array('state' => 0, 'msg' => '获取成功！','data'=>$userGift));
    }
    
}
