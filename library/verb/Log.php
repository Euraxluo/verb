<?php
namespace verb;
use verb\conf;
class Log{
    /**1. 确定日志的存储方式
     * 2. 写日志
     */
     static $class;
     static public function init(){
         //根据配置确定存储方式，获取驱动
         $drive = conf::get('DRIVE','log');
         $class = '\verb\lib\drive\log\\'.$drive;
         self::$class = new $class;
     }
     static public function log($name,$file='log'){
         P($name);
        //  self::$class->log($name,$file);
     }
}
?>