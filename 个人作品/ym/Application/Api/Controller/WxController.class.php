<?php

namespace Api\Controller;

use Think\Controller;
use Think\WxpayApp\lib\WxPayConfig;
use Think\WxpayApp\lib\WxPayApiApp;
use Think\WxpayApp\lib\Log;
use Think\WxpayApp\lib\CLogFileHandler;

require_once 'Public/vendor/autoload.php';

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

header("Content-Type: text/html; charset=UTF-8");
Vendor('WxpayApp.WxPayJsApiPay');

class WxController extends Controller {

    public function sendWxSMS() {
        $indexA = A('Center/Index');
        $http = new \Think\Http;
        echo $indexA->getToken(TRUE);
        $px = $http->httpPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $indexA->getToken(TRUE), '{
                    "touser":"oph7UwFoEjKD4jzUtMDhUbBShihA",
                    "msgtype":"text",
                    "text":
                    {
                         "content":"11111111"
                    }
                }');
        print_r($px);
    }

    public function order() {
        if (I('get.cache') == "1") {
            print_r(S('wx_notify_url'));
            exit();
        } else if (I('get.cache') == "0") {
            S('wx_notify_url', NULL);
            exit();
        }

        \Think\Log::write("ORDER_POST" . json_encode(I('post.')));
        $controller = A('Api/App');
        $controller->encryptionAPI(array('userid', 'buyby', 'type'), I('post.token'), I('post.timestamp'));
        if (I('post.buyby') != 'cash') {
            //目前只有 微信支付
            return;
        }
        ////拿出商品
        $item = M('items')->where(array('id' => I('post.type')))->find();
        $price = json_decode($item['price']);
        $price = $price->cash;
        //order
        $orderID = time() . I('post.userid');
        $model = D('Api/Wx');
        $model->saveOrder(I('post.userid'), $price, $orderID, I('post.type'));

        // S('wx_notify_url',NULL);
        if (!S('wx_notify_url')) {
            $AppConfigData = M('app_config')->cache('wx_notify_url')->where(array('id' => '17'))->find();
        } else {
            $cacheConfigData = S('wx_notify_url');
            $wx_notify_url = $cacheConfigData['val'];
        }

        ////
//        $input = new \WxPayUnifiedOrder();
        $current = time();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($item['name']);
        $input->SetAttach(I('post.userid'));
        $input->SetOut_trade_no($orderID);
        $input->SetTotal_fee($price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", $current + 1600));
        $input->SetGoods_tag(I('post.type'));
        $input->SetNotify_url($wx_notify_url . 'index.php/notifyapp/');
        $input->SetTrade_type("APP");
//        $input->SetOpenid($openId);
        $order = \WxPayApiApp::unifiedOrder($input);
        $order['timestamp'] = $current;

        $strA = "appid=" . $order["appid"] . "&noncestr=" . $order["nonce_str"] . "&package=Sign=WXPay&partnerid=" . $order["mch_id"] . "&prepayid=" . $order["prepay_id"] . "&timestamp=" . $current . "&key=56151da9b93f5755a4231f7725b290eb";
        $sign = strtoupper(md5($strA));
        $order['sign'] = $sign;
        $json_data = array();
        $json_data["state"] = 0;
        $json_data['msg'] = '0';
        $json_data['data'] = $order;
        echo json_encode($json_data);
//        $jsApiParameters = $tools->GetJsApiParameters($order);
    }

    public function notifyapp() {
        vendor('WxpayApp.notify');
        //初始化日志
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }

    public function checkOrder() {
        $model = D('Api/Wx');
        $order = $model->findLastOrderIDByUser($_SERVER['HTTP_USERID']);
        if ($order == null) {
            $json_data["state"] = 0;
            $json_data['msg'] = '没有该订单';
        } else {
            if ($order['paid'] == '1') {
                $json_data["state"] = 0;
                $json_data['msg'] = '已经支付成功';
            } else {
                $input = new \WxPayOrderQuery();
                $input->SetOut_trade_no($order['orderid']);
                $array = \WxPayApiApp::orderQuery($input);
                if ($array['trade_state']=='NOTPAY') {
                    $json_data["state"] = 0;
                    $json_data['msg'] = '用户没有进行支付';
                }else if ($array['trade_state']=='REFUND') {
                    $json_data["state"] = 0;
                    $json_data['msg'] = '用户已退款';
                }else if ($array['trade_state']=='SUCCESS'){
                    //用户支付了
                    $order = $model->saveMoney($array);
                }
            }
            $json_data['data'] = $order;
        }
        echo json_encode($json_data);
    }

}
