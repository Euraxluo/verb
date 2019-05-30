<?php
namespace verb;
use verb\util\Logger;
require __DIR__ . '/verb/util/Logger.php';
Logger::register('debug','js');//设置为debug级别,日志显示方式为js
Logger::debug('load function in '.__DIR__.DIRECTORY_SEPARATOR.'base.php');

require __DIR__ . '/verb/util/function.php';
/*
    'CLOSE' => 0, //关闭日志功能。
    'DEBUG' => 1, //详细的调试信息。
    'INFO' => 2, //有趣的事件。用户登录，SQL日志。
    'WARNING' => 4, //非错误的异常事件。使用不推荐使用的API、API使用不当、不需要的东西这不一定是错的。
    'ERROR' => 8, //运行时错误不需要立即操作，但通常应记录和监控。
    'ALL' => 12 //打开所有日志。
*/





//载入Loader类
Logger::debug('load Loader.php in '.__DIR__.DIRECTORY_SEPARATOR.'base.php');
require __DIR__ . '/verb/Loader.php';
Loader::register();

/* 用户常量参见
[   [ROOT] => /mnt/c/home/php/verb
    [APP] => /mnt/c/home/php/verb/app/
    [VERB] => /mnt/c/home/php/verb/library
    [COMPOSER] => 1
    [SEPARATOR] => /
]*/

//设置别名
// Loader::setClassAlias([
//     'Third'      => \verb\ThirdParty::class,
//     'Route'      => \verb\Route::class,
//     'Log'      => \verb\Log::class,
//     'Model'      => \verb\Model::class,
//     'Conf'      => \verb\Conf::class,
//     'Guider' => \verb\Guider::class
// ]);

//注册引导程序
Guider::register(VERB.DIRECTORY_SEPARATOR.'conf.php');



// class_alias('verb\Loader','verb\L');
// new Route();
// new Route();

// \verb\log::init();//启动配置
// \verb\log::log('loadClass run');
// $route = new Conf();//new一个不存在的类，会触发`spl_autoload_register('\core\loadClass::load');`
// $index = new app\ctrls\indexCtrl();

// p($route);
//加载控制器



//注册自动加载
//注册错误处理
//日志
//函数库







//错误
//log




/**
 * 入口文件
 * 1. 定义常量
 * 2. 加载函数库
 * 3. 启动框架
 */
//  define('EURAXLUO',__DIR__); 
//  //todo 自定义路由，文件和路由绑定自动完成，只需要设置路由
//  define('CORE',EURAXLUO.'/verb');
//  define('APP',EURAXLUO.'/app');
//  define('MODULE','app');
//  define('DEBUG', true);
//  define('DBPlugin','medoo');//这一项是用来确定数据库连接的类型，目前支持pdo和medoo
 
//  {//可以很方便的剥离第三方，你只需要删除ThirdParty.php或者vendor，不过最好还是通过composer来安装第三方
//      define('THIRD_PARTY',EURAXLUO."/ThirdParty.php");
//      //vendor中是引入的第三方类库，如果你不想使用他们，请删除ThirdParty.php或者vendor文件夹
//      if(is_file(THIRD_PARTY)){
//          include THIRD_PARTY;
//          if(ThirdParty::init()){
//              define('INCLUDE_COMPOSER', true);
//          }else{
//              define('INCLUDE_COMPOSER', false);
//          }
//      }else{
//          define('INCLUDE_COMPOSER', false);
//      }
//  }
 
 
//  if(DEBUG){//判断是否需要打开debug模式
//      if(INCLUDE_COMPOSER){
//          ThirdParty::initWhoops();//初始化Whoops
//      }
//      //打开display_error
//      ini_set('display_error','On');
//  }else{
//      ini_set('display_error','Off');
//  }
 
//  include CORE.'/common/function.php';//引入类以使用P()
//  include CORE.'/loadClass.php';
//  spl_autoload_register('\core\loadClass::load');//如果发生了new 一个空类就会触发loadClass去加载这个类
//  \core\loadClass::run();
