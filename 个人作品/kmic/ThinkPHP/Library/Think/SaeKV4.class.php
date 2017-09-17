<?php
namespace Think;
/*
 * imitate sina cloud SaeKV class
 *@Author weibo.com/yakeing
 *@version 4.0
 * Using json and not by string to store data
 * $kvfile absolute path dirname(__FILE__)
*/
class SaeKV4{
    public $kvfile = 'kvdb.txt';
    public $variable = array();
    function init(){
        $this->fileDir = dirname(__FILE__).'\\'.$this->kvfile;
        if(is_file($this->fileDir)){
            $this->variable = $this->readvariable();
            return true;
        }else{
            return file_put_contents($this->fileDir, ' ');
        }
    }
    function set($name, $value){
        if(is_object($value) || empty($value)){
            return false;
        }else if($name === false){
            $this->variable[] =$value;
        }else{
           $this->variable[$name] =$value;
           return true;
        }
    }
    function get($k){
        return empty($this->variable[$k]) ? false : $this->variable[$k];
    }
    function getall(){
        return $this->variable;
    }
    function delete($k, $is_index=false){
        if(array_key_exists($k, $this->variable)){
            unset($this->variable[$k]);
            if(is_int($k) && $is_index) $this->variable = array_merge($this->variable);
            return true;
        }else{
            return false;
        }
    }
    function discard(){
        $this->variable = array();
        return true;
    }
    function readvariable(){
        $variable = trim(file_get_contents($this->fileDir));
        if(!empty($variable)){
        	return json_decode($variable, true);
        }else{
            return array();
        }
    }
    function __destruct(){
        $varr = array_merge($this->variable);
        if(0 < count( $varr)){
            $ser = json_encode( $varr);
        }else{
            $ser = ' ';
        }
        file_put_contents($this->fileDir, $ser);
    }
}