<?php
namespace verb\exception;

class BadStrType extends \Exception
{
    protected $var;
    /**
     * 字符串不匹配异常
     *
     * @param string $message 错误信息或者栈帧
     * @param string $var 字符串或者栈帧
     */
    public function __construct($message,$var=''){
        $this->message = $message;
        $this->var = $var;
    }
    public function getVar(){
        return $this->var;
    }
}
