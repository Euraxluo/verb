<?php

namespace verb\util;
/**
 * 进行一些检验，并抛出异常
 */
class Check{
    /**
     * 如果判断不为true,抛出异常
     * @param boolean $var
     * @param string|Exception $msg
     * @param number $code
     * @throws \Exception
     * @return unknown
     */
	static public function isTrue($var, $msg = null)
    {
        if (!$var) {
            if($msg === null || is_string($msg)){
                throw new \Exception($msg);
            }else{
                throw $msg;
            }
        } else {
            return $var;
        }
    }

     /**
      * 用于抛出不同类型的异常
      *
      * @param [type] $e
      * @return void
      */
    static public function e($e){
        p('Verify:'.$e);
        if ($e === null || is_string($e)) {
            throw new \Exception($e);
        } else {
            throw $e;
        }
    }
}