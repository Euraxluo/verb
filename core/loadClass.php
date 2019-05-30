<?php
namespace core;
class loadClass {
    public $tVar = array();
    public static $classMap = array();
    static function run(){
        \core\lib\log::init();//启动配置
        \core\lib\log::log('loadClass run');
        $route =  new \core\lib\route();//new一个不存在的类，会触发`spl_autoload_register('\core\loadClass::load');`
//加载控制器

        \core\lib\log::log($_SERVER,'server');
        //把之前提取的ctrl和action拿出来解析构造成class
        $ctrlClass = $route->ctrl;
        $action = $route->action;


        //很关键的代码，可以重构
        //构成为文件路径，用于判断文件是否存在
        $ctrlFile = APP.'/ctrls/'.$ctrlClass.'Ctrl.php';
        //构造成class
        $ctrlClass = '\\'.MODULE.'\ctrls\\'.$ctrlClass.'Ctrl';



        if(is_file($ctrlFile)){//如果文件存在，那么去加载文件中的类
            include $ctrlFile;
            $ctrl = new $ctrlClass();
            $ctrl->$action();//运行类中用户需要的action()方法
        }else{
            //如果文件不存在，抛出异常
            throw new \Exception('找不到控制器'.$ctrlClass);
        }
    }
     static public function load($class){
         //自动加载类库
         if(isset($classMap[$class])){//判断是否已经引入过类
             return true;
         }else{
            
            $class = str_replace('\\','/',$class);//将路由转化为路径
            $file = EURAXLUO.'/'.$class.'.php';//将路径对应的文件拼接成文件
            
            if(is_file($file)){//判断是不是php文件
                include $file;//引入类
                self::$classMap[$class] = $class;//如果之前map中没有，那么放进去
            }else{
                return false;
            } 
         }
         
     }
     public function assign($name,$value=''){//接受一对K,V
        if(is_array($name)){
            $this->tVar = array_merge($this->tVar,$name);
        }else{
            $this->tVar[$name] = $value;
        }
         
     }
     public function display($file){//会include一个文件
         $file = APP.'/views/'.$file;//构造成一个file
         if(is_file($file)){
            if(INCLUDE_COMPOSER){//如果引入了第三方插件就使用twig模板引擎
             $loader = new \Twig\Loader\FilesystemLoader(APP.'/views');
             $twig = new \Twig\Environment($loader,[
                 'cache'=>EURAXLUO.'/log/twig',
                 'debug'=>DEBUG
             ]);
            //解析模板文件
             $template = $twig->loadTemplate('layouts\layout.html');
             $template->display($this->tVar?$this->tVar:array());
            
            }else{//否则就只能在html中插入php代码的方式
                extract($this->tVar);//将assign数组中的内容打散
                include $file;
            }
         }
     }
}
