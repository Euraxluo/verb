<?php
//文件系统
namespace core\lib\drive\log;

use core\lib\conf;

class file{
    public $path;//文件存储位置
    public function __construct(){//初始化
        $logPath = conf::get('OPTION','log');//获取配置文件中设置的log文件存储目录
        $this->path = $logPath['PATH'];
    }
    public function log($message,$file='log'){
        /**
         *1. 确定文件是否存在，不存在就新建
         *2. 写入日志
         */
        if(!is_dir($this->path.date('YmdH'))){//判断这个目录是否存在，不存在就新建，每小时作为一个文件夹存放
            mkdir($this->path.date('YmdH'),'0777',true);
        }
        //写入格式
        return file_put_contents($this->path.date('YmdH').'/'.$file.'.php',date('Y-m-d H:i:s') .' '.json_encode($message).PHP_EOL,FILE_APPEND);
    }
}

?>