<?php
namespace verb\model\driver;

use verb\Conf;

/**
 * 使用的原生PDO,会返回原生的句柄
 */
class PdoModel extends \PDO{
    public function __construct(){//连接数据库
        //获取配置
        $dbconf = Conf::getConfByName('DATABASE')['OPTION'];
        try{//连接数据库
            //由于是和medoo使用的公共配置，需要构造一下
            $dsn =  $dbconf['database_type'].":host=".$dbconf['server'].";dbname=".$dbconf['database_name'];
            parent::__construct($dsn,$dbconf['username'],$dbconf['password']);
        }catch(\PDOException $e){
            P($e->getMessage());
        }
    }
}
