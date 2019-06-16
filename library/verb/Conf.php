<?php
namespace verb;

use verb\util\AnnotationCleaner;

class Conf
{       
    private static $confs = array();//配置内容
    private static $conf_file;//文件路径

    /**
     * 设置配置
     *
     * @param array|string $conf
     * @param bool $add 是否需要增加新的设置
     * @return void
     */
    public static function setConf($conf,$add=false){
        if($add){
            self::$confs = array_merge_recursive(self::$confs, self::resolvConf($conf));
        }else{
            self::$confs = array_merge(self::$confs, self::resolvConf($conf));
        }
        self::$conf_file = (!is_array($conf)&&is_file($conf))?$conf:null;//设置配置文件路径
    }

    /**
     * 解析配置文件
     *
     * @param string|array $conf
     * @return array
     */
    public static function resolvConf($conf){
        $conf_content = array();
        if(is_array($conf)){//如果是array
            $conf_content = $conf;//直接使用array
        }else{//如果是file
            isTrue(is_file($conf), "$conf is not a conf file");
            if(strtolower(pathinfo($conf, PATHINFO_EXTENSION)) == 'php'){//判断是否是php文件
                $conf_content = include($conf);//直接include会得到内容
            }else{
                isTrue( false !== ($data = file_get_contents($conf)), "$conf open failed");
                $data = AnnotationCleaner::clean($data);
                isTrue(is_array($conf_content = json_decode($data,true)), "$conf json_decode failed with ".json_last_error());
            }
        }
        return $conf_content;
    }



    /**
     * 获取所有配置项
     *
     * @param string|array $conf
     * @return array
     */
    public static function getAllConf($conf=null){
        if(self::$confs!=null){
            return self::$confs;
        }else{
            self::setConf($conf);
            if (self::$confs!=null){ //查看这个配置项是否存在
                return self::$confs;
            }else {
                throw new \Exception('获取配置失败' . $conf);
            }
        }
        return self::$confs;
    }
    /**
     * 获取配置项,同时可以设置配置项
     *
     * @param string $name
     * @param array|string $conf
     * @return void
     */
    public static function getConfByName($name,$conf=''){
         if(isset(self::$confs[$name])){
            return self::$confs[$name];
        }else{
            self::setConf($conf);
            if (isset(self::$confs[$name])){ //查看这个配置项是否存在
                return self::$confs[$name];
            }else {
                throw new \Exception('没有这个配置项' . $name);
            }
        }
    }

    // static public function get($name, $file)
    // {
    //     /**
    //      *1. 判断配置文件是否存在
    //      *2. 判断配置是否存在
    //      *3. 缓存配置 
    //      */
    //      if(isset(self::$confs[$file])){
    //          return self::$confs[$file][$name];
    //      }else{
    //         $path = verb . '/core/config/' . $file . '.php';//文件的路径
    //         if (is_file($path)) {//判断是否是一个文件
    //             $conf = include $path;//引入这个配置文件
    //             if (isset($conf[$name])) { //查看这个配置是否存在
    //                 self::$confs[$file] = $conf;//key是配置文件的名字或者路径
    //                 return $conf[$name];
    //             } else {
    //                 throw new \Exception('没有这个配置项' . $name);
    //             }
    //         } else {
    //             throw new \Exception('找不到这个配置文件' . $file);
    //         }
    //      }

    // }

    // static public function all($name){
    //     if(isset(self::$confs[$name])){
    //         return self::$confs[$name];
    //     }else{
    //        $path = verb . '/core/config/' . $file . '.php';//文件的路径
    //        if (is_file($path)) {//判断是否是一个文件
    //             $conf = include $path;//引入这个配置文件
    //             self::$confs[$file] = $conf;//key是配置文件的名字或者路径
    //             return $conf;
    //        } else {
    //            throw new \Exception('找不到这个配置文件' . $file);
    //        }
    //     }
    // }
}
?>