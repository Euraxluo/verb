<?php
use core\ThirdParty;
require_once 

/*
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
@##########################################$!;;|&##########@
@##########################################$;;;|&##########@
@##########################################$;;;|&##########@
@#&%%%&####&%%%$#@$%%%%%%%%%%%&##&%%%&#####$;;;!|%%%%%%%&##@
@#@|;;!$##&|;;!&#&|;;;;;;;;;;;%##%;;;;;;;|@$;;;;;;;;;;;;%##@
@##&!;;|&@%;;;%##&|;;;$##@%;;;%##%;;;;;;;|@$;;;!$&&$|;;;%##@
@###$;;;||!;;|@##&|;;;;;;;;;;;%##%;;;%@####$;;;|&##@|;;;%##@
@###@|;;;;;;!&###&|;;;%&&&&&&&@##%;;;%@####$;;;|&##@|;;;%##@
@####&!;;;;!$####&|;;;;;;;;;;;%##%;;;%@####$;;;;;;;;;;;;%##@
@#####$!;;;%@####&|;;;;;;;;;;;%##%;;;%@####$!;;;;;;;;;;;%##@
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

*/

/**
 * 入口文件
 * 1. 定义常量
 * 2. 加载函数库
 * 3. 启动框架
 */
define('EURAXLUO',__DIR__); 
//todo 自定义路由，文件和路由绑定自动完成，只需要设置路由
define('CORE',EURAXLUO.'/core');
define('APP',EURAXLUO.'/app');
define('MODULE','app');
define('DEBUG', true);
define('DBPlugin','medoo');//这一项是用来确定数据库连接的类型，目前支持pdo和medoo

{//可以很方便的剥离第三方，你只需要删除ThirdParty.php或者vendor，不过最好还是通过composer来安装第三方
    define('THIRD_PARTY',EURAXLUO."/ThirdParty.php");
    //vendor中是引入的第三方类库，如果你不想使用他们，请删除ThirdParty.php或者vendor文件夹
    if(is_file(THIRD_PARTY)){
        include THIRD_PARTY;
        if(ThirdParty::init()){
            define('INCLUDE_COMPOSER', true);
        }else{
            define('INCLUDE_COMPOSER', false);
        }
    }else{
        define('INCLUDE_COMPOSER', false);
    }
}



if(DEBUG){//判断是否需要打开debug模式
    if(INCLUDE_COMPOSER){
        ThirdParty::initWhoops();//初始化Whoops
    }
    //打开display_error
    ini_set('display_error','On');
}else{
    ini_set('display_error','Off');
}

include CORE.'/common/function.php';//引入类以使用P()
include CORE.'/loadClass.php';
spl_autoload_register('\core\loadClass::load');//如果发生了new 一个空类就会触发loadClass去加载这个类
\core\loadClass::run();