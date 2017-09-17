<?php

namespace Api\Model;

use Think\Model;

class WxModel {

    public function saveOrder($userid, $total_fee, $orderID, $tag) {
        $array = array();
        $array['uid'] = $userid;
        $array['type'] = $tag;
        $array['orderid'] = $orderID;
        $array['addtime'] = time();
        $array['price'] = $total_fee;

        $order = M('orders', 'center_')->where(array('uid' => $userid, 'type' => $tag, 'paid' => 0))->find();
        if (!$order) {
            M('orders', 'center_')->add($array);
        } else {
            M('orders', 'center_')->where(array('id' => $order['id']))->save(
                    array(
                        'addtime' => time(),
                        'price' => $total_fee,
                        'orderid' => $orderID,
                        'uid' => $userid
            ));
        }
    }

    public function findLastOrderIDByUser($userid) {
        $order = M('orders', 'center_')->field('orderid , paid')->where(array('uid' => $userid))->order('addtime desc')->find();
        return $order;
    }

    public function saveMoney($result) {
        try {
            $order = M('orders', 'center_')->where(array('orderid' => $result['out_trade_no']))->find();

            M('orders', 'center_')->where(array('orderid' => $result['out_trade_no']))->save(
                    array('paid' => 1,
                        'paidtime' => time(),
                        'transaction_id' => $result['transaction_id'],
                        'return_code' => $result['return_code'],
                        'mch_id' => $result['mch_id']));

            //拿出商品
            $item = M('items')->where(array('id' => $order['type']))->find();
            //把钱丢到账户里
            $bank = M('user_bank')->where(array('uid' => $result['attach']))->setInc('deposit', $item['amount']);
            //bill
            M('user_bill')->add(array('uid' => $result['attach'], 'create_time' => time(), 'deposit' => $item['amount'], 'describe' => '充值', 'type' => $order['type'], 'source' => "0", 'icon' => $item['icon']));
        } catch (Exception $ex) {
            
        }
    }

}
