<?php
namespace verb;

class Conf
{   
    protected $metas; 
    protected $singletons = array();
    protected $conf = null;
    protected $dict = array();
    protected $conf_file;
    private $create_stack=array(); // 正在创建的类，用于检测循环依赖

    static public $confs = array();

    public  function __construct($conf)
    {
        if($conf === null){
            $this->conf = array();
        }elseif(is_array($conf)){
            $this->conf = $conf;
        }else{
            Verify::isTrue(is_file($conf), "$conf is not a valid file");
            if(strtolower(pathinfo($conf, PATHINFO_EXTENSION)) == 'php'){
                $this->conf = include($conf);
            }else{
                Verify::isTrue(false !== ($data = file_get_contents($conf)), "$conf open failed");
                $data = self::clearAnnotation($data);
                Verify::isTrue(is_array($this->conf = json_decode($data,true)), "$conf json_decode failed with ".json_last_error());
            }
            $this->conf_file = $conf;
        }
        if($dict !== null){
            $this->conf = $this->replaceByDict($this->conf, $dict);
        }
        $this->metas = $metas;
    }

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
            $path = verb . '/core/config/' . $file . '.php';//文件的路径
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
           $path = verb . '/core/config/' . $file . '.php';//文件的路径
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