<?php
namespace verb;
use verb\util\Logger;
use verb\cache\CacheDriver;
use verb\cache\driver\Redis;
use verb\cache\driver\File;
use verb\cache\driver\Apc;
use verb\route\Tree;

require __DIR__ . '/verb/util/Logger.php';//注册并设置为debug级别,日志显示方式为js
Logger::register('debug','js');
require __DIR__ . '/verb/util/function.php';//导入常用函数库
require __DIR__ . '/verb/Loader.php';//载入Loader类，并注册自动加载
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


// 注册引导程序
$guider = new  Guider(VERB.DIRECTORY_SEPARATOR.'conf.json');
// $guider->test(VERB.DIRECTORY_SEPARATOR.'conf.json',[1,2,3]);
// $router = $guider->create('verb\\RouterWithCache');

//初始化配置文件
// p(Conf::resolvConf(VERB.DIRECTORY_SEPARATOR.'conf.php'));
// Conf::setConf(VERB.DIRECTORY_SEPARATOR.'conf.php');
// p(Conf::getAllConf());
// Conf::setConf([    
//     "Cache" => [
//         "DRIVE"=>"FILE",
//         "OPTION"=>["PATH"=>"PATH"]
//     ]]);
// p(Conf::getConfByName('Database',VERB.DIRECTORY_SEPARATOR.'conf.php'));



// $tree = new Tree();
// $tree->insert(array('/'),'the /');
// $res = [];

// $tree->treeToArray($tree->arr,$res);
// p($res);


{
// $fileCache = new File();//使用File缓存
// $fileCache->del('file');//删除一个key
// $fileCache->inc('file',5);//递增
// $fileCache->dec('file',5);//递减
// $fileCache->set('file','test1',2);//设置key-value-过期时间
// $fileCache->getWriteTimes();//获取写入次数
// $fileCache->has('file');//判断缓存中是否有这个key
// $fileCache->pull('file');//拉取一个key
// $fileCache->setTag('file',"file1,file2,file3");//添加标记
// $fileCache->clear('tag');//清除缓存
//获取tag标记的缓存
// $tags =  $fileCache->getTagItem('test');
// foreach ($tags as $k=>$v){
//     p($fileCache->get($v));
// }
// $fileCache->remember('file4','11');//如果存在，返回已有的，不存在就新加
// $fileCache->getTagItem('file');//获取tag
// $fileCache->get('file');//获取缓存
}
{
// $redis =  new Redis();//连接数据库
// $redis->set('key',"Value");//设置k-v,以及过期时间
// $redis->inc('inc',1);//每次访问都自增
// $redis->dec('dec',2);//每get一次，自减少
// $redis->del('dec');//通过k删除对应的缓存
// $redis->getReadTimes();//获取读取次数
// $redis->getWriteTimes();//写入次数
// $redis->set('tag',1);
// $redis->has('tag');//判断有没有key，返回true或false
// $redis->pull('tag');//拉取k-v
// $redis->setTag('test3',['tag','tag2','tag3','t']);//添加一系列添加标签
// $redis->getTagItem('test');//获取标签
// $redis->handler()->keys('*');//通过句柄使用redis
// $redis->remember('redis','??');//todo
// $redis->clear();//清除keys
}


























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
