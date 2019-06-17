<?php
namespace verb\model\driver;

use Medoo\Medoo;
use verb\Conf;

/**
 * 使用的是Medoo插件，方法同medoo
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
