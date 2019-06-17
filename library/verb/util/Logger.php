<?php
namespace verb\util;

use verb\Conf;

class Logger
{
    // 默认DEBUG
    static $default = 1;

    /**
     * 可以设置输出位置
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    public static $writer;

    /**
     * 直接echo输出
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    private static $echoLog;

    /**
     * 忽略输出
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    private static $emptyLog;

    /**
     * 输出到phplog
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    private static $log2phplog;
    /**
     * 输出到日志文件中
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    private static $log2file;

    /**
     * 输出到浏览器控制台
     *
     * @param int $level
     * @param string $message
     * @return void
     */
    private static $log2console;

    /**
     * 定义日志级别
     *
     * @var array
     */
    private static $levels = [
        'CLOSE' => 12, //关闭日志功能,或者强制输出。
        'DEBUG' => 1, //详细的调试信息。
        'INFO' => 2, //有趣的事件。用户登录，SQL日志。
        'WARNING' => 4, //非错误的异常事件。使用不推荐使用的API、API使用不当、不需要的东西这不一定是错的。
        'ERROR' => 8, //运行时错误不需要立即操作，但通常应记录和监控。
    ];
    /**
     * 定义输出到phplog的类型
     *
     * @var array
     */
    private static $debug_type = [
        'CLOSE' => E_USER_NOTICE,
        'DEBUG' => E_USER_NOTICE,
        'INFO' => E_USER_NOTICE,
        'WARNING' => E_USER_WARNING,
        'ERROR' => E_USER_ERROR,
    ];

    /**
     * 为不同的错误级别设置不同的颜色
     *
     * @var array
     */
    private static $Color = [
        'CLOSE' => "",
        'DEBUG' => "teal",
        'INFO' => "maroon",
        'WARNING' => "fuchsia",
        'ERROR' => "red"
    ];

    /**
     * $level初始化日志等级
     * CLOSE = 12;
     * DEBUG = 1;
     * INFO = 2;
     * WARNING = 4;
     * ERROR = 8;
     *
     * $type 设置输出类型
     * 'ECHO'直接echo,
     * 'LOG'通过php输出,
     * 'JS'通过js输出,
     * 'EMPTY'不输出,
     * 'FILE'文件输出
     * @param string|int $level
     * @param string $type
     * @return void
     */
    public static function register($level, $type = null)
    {
        self::setOption($level);
        Logger::$echoLog = function ($level, $message) {
            $title = array_search($level, self::$levels);
            $stackFrame = debug_backtrace()[2];
            $position = ' in ' . $stackFrame['file'] . ' (' . $stackFrame['line'] . ')';
            $data = date('Y-m-d H:i:s') . ' ==' . $title . '== ' . $message . $position;
            print_r(" <font color=\" " . self::$Color[$title] . " \"> " . $data . "</font><br>\n");
        };
        Logger::$log2console = function ($level, $message) {
            $title = array_search($level, self::$levels);
            $stackFrame = debug_backtrace()[2];
            $position = ' in ' . $stackFrame['file'] . ' (' . $stackFrame['line'] . ')';
            $data = date('Y-m-d H:i:s') . ' ==' . $title . '== ' . $message . $position;
            print_r("<script language='javascript'>console.log(\" " . $data . "\"); </script>");
        };
        Logger::$log2phplog = function ($level, $message) {

            $logLevel = array_search($level, self::$levels);
            $stackFrame = debug_backtrace()[2];
            $position = ' in <b>' . $stackFrame['line'] . "</b>\n<br />";
            $data = date('Y-m-d H:i:s') . ' ' . $message . $position;
            trigger_error(" <font color=\" " . self::$Color[$logLevel] . " \"> " . $data . "</font>", self::$debug_type[$logLevel]);
        };

        Logger::$log2file = function ($level, $message) {
            $title = array_search($level, self::$levels);
            if (!array_key_exists('LOG_PATH', Conf::getAllConf())) {//判断是否存在配置项log_path
                $logConf = Conf::getConfByName('LOG');//获取配置的log数组// $logConf =  Conf::getAllConf(CONF)['LOG'];
                $logConf =  array_change_key_case($logConf, CASE_UPPER);//转大写
                $logConf = array_change_key_case($logConf['OPTION'], CASE_UPPER);//获取option项
                Conf::setConf([//设置配置项log_path
                    "LOG_PATH" => $logConf['PATH']
                ]);
                $path = $logConf['PATH']; //文件存储位置
            } else {
                $path = Conf::getConfByName('LOG_PATH');// getAllConf()['LOG_PATH']; //文件存储位置
            }
            $stackFrame = debug_backtrace()[2];
            $position = ' in ' . $stackFrame['file'] . ' (' . $stackFrame['line'] . ')';
            $message = $message . $position;
            if (!is_dir($path.DIRECTORY_SEPARATOR. date('YmdH'))) { //判断这个目录是否存在，不存在就新建，每小时作为一个文件夹存放
                mkdir($path.DIRECTORY_SEPARATOR. date('YmdH'), '0777', true);
            }
            return  file_put_contents($path.DIRECTORY_SEPARATOR. date('YmdH').DIRECTORY_SEPARATOR.'log.php',$title.' '.date('H:i:s') .' '.
            json_encode( $message, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES ).PHP_EOL,FILE_APPEND);
        };

        Logger::$emptyLog = function ($level, $message) { };


        self::setWriter($type);
    }

    /**
     * 可以设置颜色支持html的字体颜色
     *
     * @param string|array $level
     * @param string|null $color
     * @return void
     */
    public static function setColor($level, $color = '')
    {
        if (is_array($level)) {
            self::$Color = array_merge(self::$Color, $level);
        } else {
            self::$Color[$level] = $color;
        }
    }



    /**
     * 设置输出的类型
     * 'ECHO'直接输出echoLog,
     * 'LOG'通过trigger_error输出,
     * 'EMPTY'不输出
     * 'JS'控制台输出
     * 'FILE'文件输出
     * @param string $type
     * @return void
     */
    public static function setWriter($type = null)
    {
        if($type==null){//如果没有传$typem,那么是通过配置获取？

        }
        $type = strtoupper($type);
        switch ($type) {
            case 'ECHO':
                Logger::$writer = Logger::$echoLog;
                break;
            case 'LOG':
                Logger::$writer = Logger::$log2phplog;
                break;
            case 'EMPTY':
                Logger::$writer = Logger::$emptyLog;
                break;
            case 'JS':
                Logger::$writer = Logger::$log2console;
                break;
            case 'FILE':
                Logger::$writer = Logger::$log2file;
                break;
            default:
                Logger::$writer = Logger::$log2phplog;
        }
    }

    /**
     * 设置default的log级别
     *
     * @param string|int $level
     * @return void
     */
    public static function setOption($level)
    {
        $level = strtoupper($level);
        if (is_string($level)) {
            if (array_key_exists($level, self::$levels)) {
                self::$default =  self::$levels[$level];
            }
        }
        if (is_int($level)) {
            self::$default = $level;
        }
    }

    /**
     * debug log
     * @param string $msg
     * @return void
     */
    public static function debug($msg)
    {
        if (self::$default <= self::$levels['DEBUG']) {
            call_user_func(Logger::$writer, self::$levels['DEBUG'], $msg);
        }
    }
    /**
     * info log
     * @param string $msg
     * @return void
     */
    public static function info($msg)
    {
        if (self::$default <= self::$levels['INFO']) {
            call_user_func(Logger::$writer, self::$levels['INFO'], $msg);
        }
    }
    /**
     * warning log
     * @param string $msg
     * @return void
     */
    public static function warning($msg)
    {
        if (self::$default <= self::$levels['WARNING']) {
            call_user_func(Logger::$writer, self::$levels['WARNING'], $msg);
        }
    }
    /**
     * error log
     * @param string $msg
     * @return void
     */
    public static function error($msg)
    {
        if (self::$default <= self::$levels['ERROR']) {
            call_user_func(Logger::$writer, self::$levels['ERROR'], $msg);
        }
    }
    /**
     * close log,即便关闭日志也会进行输出消息
     * @param string $msg
     * @return void
     */
    public static function close($msg)
    {
        if (self::$default <= self::$levels['CLOSE']) {

            call_user_func(Logger::$writer, self::$levels['CLOSE'], "<mark>" . $msg . "</mark>");
        }
    }
}
/*
　　call_user_func()：调用一个回调函数处理字符串,
　　可以用匿名函数，可以用有名函数，可以传递类的方法，
　　用有名函数时，只需传函数的名称
　　用类的方法时，要传类的名称和方法名
　　传递的第一个参数必须为函数名，或者匿名函数，或者方法
　　其他参数，可传一个参数，或者多个参数，这些参数会自动传递到回调函数中
　　而回调函数，可以通过传参，获取这些参数
　　返回回调函数处理后的结果

    trigger_error():
    产生一个用户级别的 error/warning/notice 信息

    debug_backtrace():
    产生一条 PHP 的回溯跟踪(backtrace)。
    debug_backtrace ([ int $options = DEBUG_BACKTRACE_PROVIDE_OBJECT [, int $limit = 0 ]]): array
    
    array_search('green', $array)
    通过value取key

*/