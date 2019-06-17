<?php
namespace verb;

use verb\exception\ClassNotFoundException;

class Loader
{

    //类别名
    protected static $classAlias = [];
    //类map
    protected static $classMap = [];
    //composer路径
    private static $composerPath;
    //是否能成功加载composer
    private static $vendorFlag;
    //是否是Unix
    private static $Separator;
    /**
     * 注册自动加载机制
     *
     * @param boolean $debug 是否开启debug模式，默认关闭
     * @return void
     */
    public static function register($debug=false)
    {
        /**
         * 注册系统自动加载
         */
        spl_autoload_register('verb\\Loader::autoLoad', true, true);

        /**
         * 获取vendor目录:以启动composer
         */
        $rootPath = self::getRootPath();
        self::$composerPath = $rootPath . 'vendor' . DIRECTORY_SEPARATOR;
        self::$Separator= DIRECTORY_SEPARATOR=='\\'?'/':'\\';
        /**
         * composer自动加载支持
         */
        if (is_dir(self::$composerPath)) {
            if (is_file(self::$composerPath . 'autoload.php')) {
                require self::$composerPath . 'autoload.php'; 
                if($debug){
                    $whoops = new \Whoops\Run;
                    $errorTitle = 'verb framework error';
                    $option = new \Whoops\Handler\PrettyPageHandler();
                    $option->setPageTitle($errorTitle);
                    $whoops->pushHandler($option);
                    $whoops->register();
                }
                self::$vendorFlag = true;
            } else {
                self::$vendorFlag = false;
            }
        }
        /**
         * 注册命名空间
         */
        self::defineNamespace([
            'VERB'  => dirname(__DIR__),
            'COMPOSER' => self::$vendorFlag,
            'SEPARATOR' => self::$Separator
        ]);
        //get_declared_classes()：返回当前已经定义类的名字
        //array_pop($array)：取出数组最后一个元素
    }

    /**
     * 自动加载
     *
     * @param [type] $classname
     * @return bool
     */
    public static function autoLoad($classname)
    {
        //array_key_exists :检查数组中是否有指定的键名或索引
        //isset:检测变量是否已设置并且非NULL
        //array_keys：返回数组中部分或所有的键名
        //in_array:检查数组中是否存在某个值
        //property_exists:检查对象或类是否具有该属性
        if(array_key_exists($classname, self::$classMap)){
            $path = self::$classMap[$classname];
            require_once $path;
            return true;
        }else{
            $namesps = explode('\\',trim($classname,'\\'))[0];
            if($namesps!='verb' && $namesps!='Doctrine' && $namesps!='Peekmo'){//判断是否属于框架
                $path = ROOT . DIRECTORY_SEPARATOR . str_replace(SEPARATOR, DIRECTORY_SEPARATOR, $classname) . '.php'; //path
            }else{
                $path = VERB . DIRECTORY_SEPARATOR . str_replace(SEPARATOR, DIRECTORY_SEPARATOR, $classname) . '.php'; //path
            }
            if (file_exists($path)) {
                /**
                 * 如果path是一个php文件，那就加入到classMap中，并导入
                 */
                self::$classMap[$classname] = $path;
                require_once $path;
            }else{
                return false;
            }
        }

    }
    /**
     * 设置Class的解析map
     *
     * @param array $map
     * @param boolean $replace
     * @return void
     */
    public static function setClassMap($map, $replace=false){
        if($replace){
            self::$class_map = array_merge(self::$class_map, $map);
        }else{
            self::$class_map = array_merge($map, self::$class_map);
        }
    }

    /**
     * 设置常量-命名空间
     *
     * @param array $namespace
     * @return void
     */
    public static function defineNamespace($namespace)
    {
        foreach ($namespace as $k => $v) {
            define(strtoupper($k), $v, true);
        }
        
    }

    /**
     * 获取项目根目录
     *
     * @return void
     */
    public static function getRootPath()
    {
        //scriptName = '/verb/index.php'
        if ('cli' == PHP_SAPI) {
            $scriptName = realpath($_SERVER['argv'][0]);
        } else {
            $scriptName = $_SERVER['SCRIPT_FILENAME'];
        }
        //path = '/';
        $path = realpath(dirname($scriptName));
        return $path . DIRECTORY_SEPARATOR;
    }
}
