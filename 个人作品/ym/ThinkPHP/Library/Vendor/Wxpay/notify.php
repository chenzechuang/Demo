<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once dirname(__FILE__)."/lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';
require_once 'WxLog.php';

//初始化日志
$logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
            \Think\Log::write('Queryorder');
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
                \Think\Log::write("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
                    try {
                        $order = M('orders','center_')->where(array('orderid'=>$result['out_trade_no']))->find();
                        if ($order) {
                            M('orders','center_')->where(array('orderid'=>$result['out_trade_no']))->save(
                            array('paid'=>1,
                                'paidtime'=>time(),
                                'transaction_id'=>$result['transaction_id'],
                                'return_code'=>$result['return_code'],
                                'mch_id'=>$result['mch_id']));
                        }else{
                            if ($result['total_fee'] == '1000') {
                                $level = 0;
                                $price = 10;
                            }else if ($result['total_fee'] == '2800') {
                                $level = 1;
                                $price = 28;
                            }else if ($result['total_fee'] == '5500') {
                                $level = 2;
                                $price = 55;
                            }else if ($result['total_fee'] == '10800') {
                                $level = 3;
                                $price = 108;
                            }
                            M('orders','center_')->add(array('paid'=>1,
                                'paidtime'=>time(),
                                'transaction_id'=>$result['transaction_id'],
                                'return_code'=>$result['return_code'],
                                'orderid'=>$result['out_trade_no'],
                                'mch_id'=>$result['mch_id'],
                                'type'=>2,
                                'level'=>$level,
                                'price'=>$price));
                        }
                        
                    } catch (Exception $ex) {
                        
                    }
                    
                    //会员时间 
                    $expire = 0;
                    
                    
                    $user = M('users')->where(array('openid'=>$result['openid']))->find();
                    if ($user['expire']!=null) {
                        if ($result['total_fee'] == '1000') {
                            $expire = strtotime("+1 month",$user['expire']);
                        }else if ($result['total_fee'] == '2800') {
                            $expire = strtotime("+3 month",$user['expire']); 
                        }else if ($result['total_fee'] == '5500') {
                            $expire = strtotime("+6 month",$user['expire']); 
                        }else if ($result['total_fee'] == '10800') {
                            $expire = strtotime("+1 year",$user['expire']); 
                        }
                    }else{
                        if ($result['total_fee'] == '1000') {
                            $expire = strtotime("+1 month");
                        }else if ($result['total_fee'] == '2800') {
                            $expire = strtotime("+3 month");
                        }else if ($result['total_fee'] == '5500') {
                            $expire = strtotime("+6 month");
                        }else if ($result['total_fee'] == '10800') {
                            $expire = strtotime("+1 year");
                        }
                    }
                    $userCenter = M('users','center_')->where(array('openid'=>$result['openid']))->find();
                    if ($user['not_first']==0) {
                        M('users')->where(array('openid'=>$result['openid']))->save(array('level'=>3,'expire'=>$expire,'not_first'=>1));
                        session('level',3);
                        //第一次给代理钱
                        //给第一个
                        $agent1 = M('users','center_')->field('id,agent,userinfo')->where(array('openid'=>$result['openid']))->find();
                        if ($agent1['agent'] != null) {
                            M('users','center_')->where(array('id'=>$agent1['agent']))->setInc('deposit',5);
                            $json = json_decode(stripslashes($agent1['userinfo']));
                            //发信息
                            M('messages')->add(array('fromuid'=>112,
                            'touid'=>$user['id'],
                            'msg'=>'您成为了会员的同时为['.$json->nickname.']赚了5元',
                            'timeline'=>  time()
                                ));
                            //
                            M('pay_log','center_')->add(array('uid'=>$agent1['agent'],'amout'=>5,'timeline'=>time(),'fromuid'=>$userCenter['id']));
                            //给第二个
                            $agent2 = M('users','center_')->field('id,agent,userinfo')->where(array('id'=>$agent1['agent']))->find();
                            if ($agent2['agent'] != null) {
                                M('users','center_')->where(array('id'=>$agent2['agent']))->setInc('deposit',3);
                                
                                $json = json_decode(stripslashes($agent2['userinfo']));
                                //发信息
                                M('messages')->add(array('fromuid'=>112,
                                'touid'=>$user['id'],
                                'msg'=>'您成为了会员的同时为['.$json->nickname.']赚了2元',
                                'timeline'=>  time()
                                    ));
                                //
                                M('pay_log','center_')->add(array('uid'=>$agent2['agent'],'amout'=>3,'timeline'=>time(),'fromuid'=>$userCenter['id']));
                            }
                        }
                    }else{
                        M('users')->where(array('openid'=>$result['openid']))->save(array('level'=>3,'expire'=>$expire));
                        session('level',3);
                        //不是第一次
                    }
                    
                    
                    return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
            \Think\Log::write('NotifyProcess');
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
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
