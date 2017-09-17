<?php

namespace Api\Model;

use Think\Model;

class ConfigModel extends Model {

    /**
     * 
     * @return type
     */
    public function getVersion() {
        $model = M('config')->field('value')->where(array('id' => 12))->find();
        return $model['value'];
    }

    public function getConfig($id) {
        $model = M('config')->field('value')->where(array('id' => $id))->find();
        return $model['value'];
    }

    public function getAppConfig() {
        S("cache",NULL); 
        if (S("cache")) {
            $ret = S("cache");
        } else {
            $channelData = M('aritcle_channel')->field('id as topic_id,title as name,content,img_url as pic,sort as order_id,mp3,type')->select();

            foreach ($channelData as $k => $value) {
                $channelData[$k]['pic'] = "http://" . $_SERVER['HTTP_HOST'] . str_replace("./", "/", $value['pic']);
            }

            $ret = array();
            $ret["topic_list"] = $channelData;
            $interestData = M('interest')->select();

            foreach ($interestData as $k => $value) {
                $ret['interest_list'][$value['id']] = $value['name'];
            }

            $languageData = M('language')->select();
            foreach ($languageData as $k => $value) {
                $ret['language_list'][$value['id']] = $value['name'];
            }

            $experienceData = M('experience')->select();
            foreach ($experienceData as $k => $value) {
                $ret['experience_list'][$value['id']] = $value['name'];
            }

            $industryData = M('industry')->select();
            foreach ($industryData as $k => $value) {
                $ret['industry_list'][$value['id']] = $value['name'];
            }

            $followtagData = M('followtag')->select();
            foreach ($followtagData as $k => $value) {
                $ret['followtag_list'][$value['id']] = $value['name'];
            }

            $voiceData = M('voice')->select();
            foreach ($voiceData as $k => $value) {
                $ret['voice_list'][$value['id']] = $value['name'];
            }

            $interestTopicData = M('interest_topic')->select();
            foreach ($interestTopicData as $k => $value) {
                $ret['interest_topic'][$value['id']] = $value['name'];
            }

            $configData = M('app_config')->select();

            $count = M('items')->field('id as commodity_id , img, name as commodity_name ,descarption as commodity_desc ,price')
                    ->where(array('type' => '1'))
                    ->count();

            if ($count > 0) {
                $count = ceil($count / 8);
                for ($i = 0; $i < $count; $i++) {

                    $model[$i] = M('items')->field('id as commodity_id , img, name as commodity_name ,descarption as commodity_desc ,price')
                                    ->where(array('type' => '1'))->limit($i * 8, 8)->select();
                    foreach ($model[$i] as $key => $value) {
                        $json = json_decode($value['price'], TRUE);
                        if ($model[$i][$key]['cash']) {
                            $model[$i][$key]['cash'] = $json['cash'] / 100;
                        }
                        $model[$i][$key]['mz'] = $json['mz'];
                        $model[$i][$key]['coin'] = $json['coin'];
                        settype($model[$i][$key]['cash'], "string");
                        unset($model[$i][$key]['price']);
                    }
                }
                //   print_r($model);
                //   exit();
            }

            $ret["giftlist"] = $model;
            $conWhere['id'] = array('in', '12,14');
            $appver = M('config')->where($conWhere)->select();

            $ret["appver"] = $appver[0]['value'];
            $ret["appverSwitch"] = $appver[1]['value'];

            foreach ($configData as $value) {
                $ret[$value['name']] = $value['val'];
            }
            S("cache", $ret); 
        }
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $ret));
    }

    public function commodity() {
        $model = M('items')->field('id as commodity_id ,name as commodity_name ,descarption as commodity_desc ,price')->where(array('type' => '0'))->select();
        foreach ($model as $key => $value) {
            $json = json_decode($value['price'], TRUE);
            $model[$key]['cash'] = $json['cash'] / 100;
            $model[$key]['mz'] = $json['mb'];
            $model[$key]['coin'] = $json['coin'];
            unset($model[$key]['price']);
        }
        $data = array();
        $data["commodity_list"] = $model;
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

    public function exchangelist() {
        $data = array();
        $exchangeList = M('exchange_list')->field('id as exchange_id,exchange_type,exchange_name,create_time,need_jf,exchange_desc')->select();
        $data["exchange_list"] = $exchangeList;
        return json_encode(array('state' => 0, 'msg' => '获取成功', 'data' => $data));
    }

    public function getGift() {
        $count = M('items')->field('id as commodity_id , img, name as commodity_name ,descarption as commodity_desc ,price')
                ->where(array('type' => '1'))
                ->count();
        if ($count > 0) {
            $count = ceil($count / 8);
            for ($i = 0; $i < $count; $i++) {

                $model[$i] = M('items')->field('id as commodity_id , img, name as commodity_name ,descarption as commodity_desc ,price')
                                ->where(array('type' => '1'))->limit($i * 8, 8)->select();
                foreach ($model[$i] as $key => $value) {
                    $json = json_decode($value['price'], TRUE);
                    $model[$i][$key]['cash'] = $json['cash'] / 100;
                    $model[$i][$key]['mz'] = $json['mb'];
                    $model[$i][$key]['coin'] = $json['coin'];
                    unset($model[$i][$key]['price']);
                }
            }
            //   print_r($model);
            //   exit();
        }
        /*
          $model = M('items')->field('id as commodity_id , img, name as commodity_name ,descarption as commodity_desc ,price')
          ->where(array('type' => '1'))->select();
          foreach ($model as $key => $value) {
          $json = json_decode($value['price'], TRUE);
          $model[$key]['cash'] = $json['cash'] / 100;
          $model[$key]['mz'] = $json['mb'];
          $model[$key]['coin'] = $json['coin'];
          unset($model[$key]['price']);
          } */
        $data = array();
        $data["commodity_list"] = $model;
        return json_encode(array('state' => 0, 'msg' => '获取成功！', 'data' => $data));
    }

}
