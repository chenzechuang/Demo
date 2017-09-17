<?php

ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ERROR);

require_once dirname(__FILE__) . "/lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'WxLog.php';

//初始化日志
$logHandler = new CLogFileHandler("./logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify {

    public function Queryorder($transaction_id) {
        \Think\Log::write('Queryorder:' . $transaction_id);
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApiApp::orderQuery($input);
        Log::DEBUG("query:" . json_encode($result));
        \Think\Log::write("query:" . json_encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            try {
                $order = M('orders', 'center_')->where(array('orderid' => $result['out_trade_no']))->find();
                if ($order) {
                    M('orders', 'center_')->where(array('orderid' => $result['out_trade_no']))->save(
                            array('paid' => 1,
                                'paidtime' => time(),
                                'transaction_id' => $result['transaction_id'],
                                'return_code' => $result['return_code'],
                                'mch_id' => $result['mch_id']));
                } else {
                    M('orders', 'center_')->add(array('paid' => 1,
                        'uid' => $result['attach'],
                        'paidtime' => time(),
                        'transaction_id' => $result['transaction_id'],
                        'return_code' => $result['return_code'],
                        'orderid' => $result['out_trade_no'],
                        'mch_id' => $result['mch_id'],
                        'type' => $result['tag'],
                        'price' => $result['cash_fee']));
                }
                //拿出商品
                $item = M('items')->where(array('id' => $order['type']))->find();
                //把钱丢到账户里
                $bank = M('user_bank')->where(array('uid'=>$result['attach']))->setInc('deposit', $item['amount']);
                //bill
                M('user_bill')->add(array('uid'=>$result['attach'],'create_time'=> time(),'deposit'=>$item['amount'],'describe'=>'充值','type'=>$order['type'],'source'=>"0",'icon'=>$item['icon']));
            } catch (Exception $ex) {
                
            }
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        \Think\Log::write('NotifyProcess');
        Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }

}

//
//Log::DEBUG("begin notify");
//$notify = new PayNotifyCallBack();
//$notify->Handle(false);
