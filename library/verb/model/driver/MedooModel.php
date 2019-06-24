<?php
namespace verb\model\driver;

use verb\Conf;
use verb\Medoo\Medoo;

/**
 * 使用的是Medoo插件，方法同medoo
 * 由于直接下载了medoo，所以可以配置好后直接使用
 * medoo十分好用，下一步希望移植一个pyhon版
 */
class MedooModel extends Medoo{
    public function __construct(){
        // 获取配置
        $dbconf = Conf::getConfByName('DATABASE')['OPTION'];
        try { //连接数据库
            parent::__construct($dbconf);
        } catch (\PDOException $e) {
            P($e->getMessage());
        }
    }
}
