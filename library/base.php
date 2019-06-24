<?php
namespace verb;
use verb\util\Logger;
use verb\route\InitRoute;
use verb\cache\CacheDriver;

/**
 * 配置文件地址
 */
define('CONF',__DIR__.DIRECTORY_SEPARATOR.'conf.php');//配置文件目录[请自定义]，你也可以选择json文件作为配置文件
/**
 * 初始化
 */
require __DIR__ . '/verb/util/Logger.php';//注册并设置为debug级别,日志显示方式为js
Logger::register('debug','file');//设置日志级别和输出形式
require __DIR__ . '/verb/util/function.php';//导入常用函数库
require __DIR__ . '/verb/Loader.php';//载入Loader类，并注册自动加载
require __DIR__ . '/verb/route/InitRoute.php';//初始化路由
Loader::register();//注册自动类加载,传入true，开启错误的debug模式
CacheDriver::register();//注册缓存，默认文件缓存
InitRoute::register(CONF,2,true);//初始化路由的配置，设置配置时延(0为永不更新)(当打开路由注解时，配置路由和注解路由都会受影响，关闭时，配置路由不受影响[秒出结果]),是否打开注解路由
Guider::register();// 注册引导程序,默认成功时返回code为1，todo 添加更多的状态 And。。。
AutoGenerationClass::register(APP.'/eo','app\\eo');//自动生成实体类
/* 用户常量参见
[   [ROOT] => /mnt/c/home/php/verb   项目root目录
    [APP] => ROOT./app/              app目录
    [VERB] => ROOT./library          框架root目录
    [COMPOSER] => 1                  是否使用composer
    [SEPARATOR] => /
    [CONF] => ROOT./library/conf.php 配置文件地址
]*/


