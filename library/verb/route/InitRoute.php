<?php
namespace verb\route;

use verb\util\MetaInfo;
use verb\IOCFactory;
use phpDocumentor\Reflection\Element;
use verb\cache\CacheDriver;
use verb\util\Logger;
use verb\Conf;

/**
 * 初始化APP目录下所有带有路由注解:route("//")
 */
class InitRoute
{
    /**
     * 视图文件
     *
     * @var array
     */
    private static $files = [];
    /**
     * 文件的注解信息
     *
     * @var array
     */
    private static $files_metaInfo = [];
    /**
     * 路由信息
     *
     * @var array
     */
    private static $route_info = [];
    private static $expire;

    /**
     * 初始化APP下的视图控制文件(*ctrl*)，缓存路由信息
     * 可以解析配置文件中的路由
     *
     * @param array|string $conf 配置的路由，格式参见conf.php
     * @param int $expire 路由时延时间;0表示永不过期(s),改动配置永不生效
     * @param bool $annotRoute 是否关闭注解路由(注解路由每次初始化时十分耗时)
     * @return void
     */
    public static function register($conf = CONF,$expire=60,$annotRoute=true)
    {
        self::$expire = $expire;//用来把过期时间传递给其他函数
        Logger::debug('the expire is '.$expire);
        $cacheHandel =  CacheDriver::register();
        if($expire != $cacheHandel->get('EXPIRE')){
            $cacheHandel->clear();
            self::$route_info = [];
            $cacheHandel->set('EXPIRE',$expire);
        }
        self::$route_info = $cacheHandel->get('route');
        if (self::$route_info == null) { //如果路由信息是空的，那么需要初始化

            if($annotRoute){//如果使用注解路由
                self::$files = read_sub_dir(APP)['dir']; //此接口极慢，建议缓存
                //获取视图控制文件
                foreach (self::$files as $dir => $file) {
                    if (strpos(strtoupper($dir), 'CTRL') !== false) { //如果是和ctrl/CTRL相关的文件夹
                        self::$files = $file;
                        break;
                    }
                }
                self::$files =  arr_foreach(self::$files); //遍历出*ctrl*下的全部文件
                // 筛选出php文件
                MetaInfo::testAnnotation();//先测试注解
                foreach (self::$files as $k => $file) {
                    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'php') { //判断是否是php文件
                        $namesp = getFileNamespace($file); //获取文件的命名空间
                        $info = pathinfo($file); //获取文件名
                        $file_name =  basename($file, '.' . $info['extension']);
                        if ($namesp != '') {
                            $file = $namesp . '\\' . $file_name;
                            self::$files[$k] = $file;
                            $data = MetaInfo::get($file); //在获取注解
                            self::$files_metaInfo[$file] = $data;
                        } else {
                            unset(self::$files[$k]);
                        }
                    } else {
                        unset(self::$files[$k]);
                    }
                }
                //初始化注解解析，对视图文件的路由进行解析
                //并存储在缓存中
                self::initRouteTree();
            }
            self::getConfRoute($conf);
            $cacheHandel->set('route', self::$route_info,$expire); //持久化路由==方法
        } else {
            return;
        }
    }

    /**
     * 获取配置文件中的路由
     *
     * @param string|array $conf
     * @return void
     */
    public static function getConfRoute($conf){
        $confRoute = Conf::getConfByName('ROUTE');//先获取配置文件中的
        // $confRoute =  Conf::getAllConf($conf)['ROUTE'];//先获取配置文件中的
        if(is_array($conf)){
            $confRoute =  array_merge($confRoute,$conf);
        }else{
            $routeInFile = Conf::resolvConf($conf);
            $tmp =  array_change_key_case($routeInFile, CASE_UPPER);//转大写
            if(array_key_exists('ROUTE',$tmp)){//如果存在route项，那么解析foreach=>route项
                $confRoute =  array_merge($confRoute,$tmp['ROUTE']);
            }else{
                $confRoute =  array_merge($confRoute,$routeInFile);
            }
        }
        foreach($confRoute as $route=>$routeInfo){
            self::setRouteInfo($route,$routeInfo);
        }
    }

    /**
     * 初始化接口，把试图注解信息转化为路由信息,存入缓存
     *
     * @return array
     */
    public static function initRouteTree()
    {
        self::$route_info = [];
        $tree = Tree::getTree();
        foreach (self::$files_metaInfo as $ctrlClass => $Annotas) {
            if (isset($Annotas)) {
                $ctrlClassRootRoute = '/';
                $routeType = 'GET'; //默认为get请求
                foreach ($Annotas as $Annota => $VorMethods) {
                    if (strpos($Annota, 'route') !== false) { //注解中包含route，需要解析
                        //获取整个文件的value
                        foreach ($VorMethods as $method => $value) {
                            if ($method == 'value') { //整个文件的设置
                                if (strpos($Annota, '::') !== false) { //解析出额外的请求类型参数
                                    $routeType = strtoupper(explode("::", $Annota)[1]);
                                }
                                $ctrlClassRootRoute = $value; //整个类的入口根路由
                            } else if (isset($value['value'])) { //方法的设置
                                $methodType = $routeType;
                                if (strpos($Annota, '::') !== false) { //解析出额外的请求类型参数
                                    $methodType = strtoupper(explode("::", $Annota)[1]);
                                }
                                if(empty( $value['value'])){
                                    $value['value'] = '*';//如果为空，表示是匹配任意路径
                                }
                                $methodRoute ='/'.$ctrlClassRootRoute .'/'. $value['value'];
                                $methodRoute = preg_replace('/\/(?=\/)/','',$methodRoute);//去掉多余的斜杠
                                // $methodRoute = str_replace("//", "/", $methodRoute); //去掉多余的斜杠
                                self::$route_info[$methodRoute] = [
                                    'class' => $ctrlClass,
                                    'method' => $method,
                                    'routeType' => $methodType
                                ];
                                Logger::info('insert a new route :'.$methodRoute);
                                $tree->insert(['/'] + explode("/", $methodRoute), $methodRoute,self::$expire);
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * 设置route=>[$routeInfo]
     *
     * @param string $route
     * @param array $routeInfo class=类,method=方法,routeType=GET/POST
     * @return bool
     */
    public static function setRouteInfo($route, $routeInfo)
    {
        if (isset($route)) {
            self::$route_info[$route] = $routeInfo;
        }
        //插入到tree中
        $tree = Tree::getTree();
        $tree->insert(['/'] + explode("/", $route), $route);
    }
    /**
     * 返回视图控制文件列表
     *
     * @return array
     */
    public static function getCtrlFiles()
    {
        return self::$files;
    }

    /**
     * 获取视图文件的注解信息
     *
     * @return array
     */
    public static function getCtrlFilesMetaInfo()
    {
        return self::$files_metaInfo;
    }
    /**
     * 获取路由信息
     *
     * @return array
     */
    public static function getRouteInfo()
    {
        return self::$route_info;
    }
}
