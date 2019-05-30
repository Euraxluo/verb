<?php
namespace core\lib;

class conf
{
    static public $confs = array();
    static public function get($name, $file)
    {
        /**
         *1. 判断配置文件是否存在
         *2. 判断配置是否存在
         *3. 缓存配置 
         */
         if(isset(self::$confs[$file])){
             return self::$confs[$file][$name];
         }else{
            $path = EURAXLUO . '/core/config/' . $file . '.php';//文件的路径
            if (is_file($path)) {//判断是否是一个文件
                $conf = include $path;//引入这个配置文件
                if (isset($conf[$name])) { //查看这个配置是否存在
                    self::$confs[$file] = $conf;//key是配置文件的名字或者路径
                    return $conf[$name];
                } else {
                    throw new \Exception('没有这个配置项' . $name);
                }
            } else {
                throw new \Exception('找不到这个配置文件' . $file);
            }
         }

    }

    static public function all($file){
        if(isset(self::$confs[$file])){
            return self::$confs[$file];
        }else{
           $path = EURAXLUO . '/core/config/' . $file . '.php';//文件的路径
           if (is_file($path)) {//判断是否是一个文件
                $conf = include $path;//引入这个配置文件
                self::$confs[$file] = $conf;//key是配置文件的名字或者路径
                return $conf;
           } else {
               throw new \Exception('找不到这个配置文件' . $file);
           }
        }
    }
}
?>