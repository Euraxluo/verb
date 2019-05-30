<?php
// namespace verb;

use verb\lib\exception\ClassNotFoundException;

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

    // 注册自动加载机制
    public static function register($autoload = '')
    {
        // 注册系统自动加载
        spl_autoload_register($autoload ?: 'verb\\Loader::autoload', true, true);
        // p(1);
        $rootPath = self::getRootPath();
        self::$composerPath = $rootPath . 'verb' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;

        //返回当前已经定义类的名字
        $declaredClass = get_declared_classes();


        //取出数组最后一个元素
        $composerClass = array_pop($declaredClass);
        // p($composerClass);

        // Composer自动加载支持
        if (is_dir(self::$composerPath)) {
            if (is_file(self::$composerPath . 'autoload.php')) {
                require self::$composerPath . 'autoload.php';
                {
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
        // 注册命名空间定义
        self::defineNamespace([
            'verb'  => dirname(__DIR__)
        ]);
    }


    

    //自动加载
    public static function autoload($class)
    {
    
        p(3);
        if (isset(self::$classAlias[$class])) { //为一个类创建别名
            p(self::$classAlias[$class], $class);
            return class_alias(self::$classAlias[$class], $class);
        }

        if ($file = self::findFile($class)) {
            // Win环境严格区分大小写
            if (strpos(PHP_OS, 'WIN') !== false && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME)) {
                return false;
            }
            __include_file($file);
            return true;
        }
    }

    // 注册classmap
    public static function addClassMap($class, $map = '')
    {
        if (is_array($class)) {
            self::$classMap = array_merge(self::$classMap, $class);
        } else {
            self::$classMap[$class] = $map;
        }
    }


    // 注册命名空间
    public static function defineNamespace($namespace)
    {
        foreach ($namespace as $k => $v) {
            define(strtoupper($k), $v, true);
        }
    }

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

        if (!is_file($path . DIRECTORY_SEPARATOR . 'think')) {
            $path = dirname($path);
        }
        return $path . DIRECTORY_SEPARATOR;
    }

    // 注册类别名
    public static function addClassAlias($alias, $class = null)
    {
        // p(array_merge(self::$classAlias, $alias));
        if (is_array($alias)) {
            self::$classAlias = array_merge(self::$classAlias, $alias);
        } else {
            self::$classAlias[$alias] = $class;
        }
    }


    // static public function load($class)
    // {
    //     //自动加载类库
    //     if (isset($classMap[$class])) { //判断是否已经引入过类
    //         return true;
    //     } else {

    //         $class = str_replace('\\', '/', $class); //将路由转化为路径
    //         $file = EURAXLUO . '/' . $class . '.php'; //将路径对应的文件拼接成文件

    //         if (is_file($file)) { //判断是不是php文件
    //             include $file; //引入类
    //             self::$classMap[$class] = $class; //如果之前map中没有，那么放进去
    //         } else {
    //             return false;
    //         }
    //     }
    // }


    //     include CORE.'/loadClass.php';
    // spl_autoload_register('\core\loadClass::load');//如果发生了new 一个空类就会触发loadClass去加载这个类
    // \core\loadClass::run();
    //     public static function register(){
    //         spl_autoload_register('\core\loadClass::load')

    //     }
}
