<?php
namespace core\lib;
use core\lib\drive\models\modelFactory;

class model{
    static $model=null;

    final protected function __construct(){//单例模式
    }
    static public  function getModel(){//加锁怎么加？
        if(self::$model == null){//加锁
            if(self::$model==null){
                self::$model=modelFactory::getdb(DBPlugin);
            }
        }
        return self::$model;
    }
}
?>

