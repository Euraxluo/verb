<?php
namespace core\lib\drive\models;

use Medoo\Medoo;
use core\lib\conf;

class medooModel extends Medoo{
    public function __construct(){
        \core\lib\log::log('medooModel');
        $dbconf = conf::all('database');
        try { //连接数据库
            parent::__construct($dbconf);
        } catch (\PDOException $e) {
            P($e->getMessage());
        }
    }
}
