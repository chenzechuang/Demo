<?php

namespace Center\Model;

use Think\Model;

class ExchangeListModel extends Model {

    /**
     * 
     * @return type
     */
    public function getData() {
        return $this->field("exchange_desc,exchange_name,need_jf,id")->select();
    }

    public function addData($arry) {
        $this->add($arry);
    }

}
