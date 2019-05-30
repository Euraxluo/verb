<?php
namespace core\lib\drive\models;

use \core\lib\drive\models\pdoModel;
use \core\lib\drive\models\medooModel;

class modelFactory{//工厂模式
    public static function getdb($dbconf)
    {
        //根据db插件的类型，自动返回实例，支持原生pdo和medoo
        switch (strtolower($dbconf)) {
            case 'pdo':
                return new pdoModel();
                break;
            case 'medoo':
                return new medooModel();
                break;
        }
    }
}