<?php
namespace core\lib\drive\models;

use core\lib\conf;

class pdoModel extends \PDO{
    public function __construct(){//连接数据库
        \core\lib\log::log('pdoModel');
        $dbconf = conf::all('database');
        try{//连接数据库
            //由于是和medoo使用的公共配置，需要构造一下
            $dsn =  $dbconf['database_type'].":host=".$dbconf['server'].";dbname=".$dbconf['database_name'];
            parent::__construct($dsn,$dbconf['username'],$dbconf['password']);
        }catch(\PDOException $e){
            P($e->getMessage());
        } 
    }
}