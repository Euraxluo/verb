<?php

/**
 * $Id: Verify.php 57435 2014-12-21 15:04:22Z caoyangmin $
 * @author caoym(caoyangmin@gmail.com)
 * @brief
 */
namespace verb\util;
/**
 * if(false) throw ;
 * @param boolen $var 判断条件
 * @param string $msg 异常消息
 * @throws Exception
 * @return unknown
 */
class Verify{
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
     * 
     * @param \Exception|string $e
     * @throws unknown
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