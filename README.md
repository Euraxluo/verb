# VERB
## It's a pity to abandon the tasteless food

### After a semester of studying php, write a small framework

### 文件结构 (directory structure)
```
.
├── app                                         [用户主目录]
│   ├── ctrls
│   │   ├── indexCtrl.php                       [测试demo]
│   │   └── testCtrl.php                        [测试demo]
│   ├── models
│   │   ├── AdminlogDao.php                     [model接口类]
│   │   └── impl
│   │       └── AdminlogImpl.php                [实现类]
│   ├── po
│   │   ├── Adminlog.php                        [实体类]
│   │   └── User.php                            [实体类]
│   └── views
│       ├── TemplateTest.html                   [模板引擎测试文件]
│       ├── layout.html                         [模板基]
│       └── TemplateTest.php                    [模板引擎测试文件]
├── library                                     [框架主目录]
│   ├── base.php                                [初始化文件]
│   ├── conf.json                               [框架配置文件]
│   ├── conf.php                                [框架配置文件]
│   ├── Doctrine                                [注解解析第三方包]│   
│   └── verb                                    [verb框架目录]
│       ├── Conf.php                            [配置解析类]
│       ├── Guider.php                          [引导注册类]
│       ├── Loader.php                          [自动加载类]
│       ├── Mould.php                           [模板引擎类]
│       ├── Route.php                           [路由初始化类]
|       ├── AutoGenerationClass.php             [自动类加载]
│       ├── cache
│       │   ├── CacheDriver.php                 [缓存驱动]
│       │   └── driver
│       │       ├── ApcCache.php                [APC驱动]
│       │       ├── FileCache.php               [文件缓存驱动]
│       │       └── RedisCache.php              [Redis缓存驱动]
│       ├── model
│       │   ├── driver
│       │   │   ├── MedooModel.php              [Medoo第三方句柄]
│       │   │   └── PdoModel.php                [返回PDO驱动句柄]
│       │   └── ModelDriver.php                 [数据库连接驱动]
│       ├── exception                           [异常类]
│       │   ├── BadRequest.php
│       │   ├── ClassNotFoundException.php
│       │   ├── Forbidden.php
│       │   └── NotFound.php
│       ├── route
│       │   ├── InitRoute.php                   [初始化路由类]
│       │   └── Tree.php                        [路由树类]
│       └── util
│           ├── AnnotationCleaner.php           [注解清理类]
│           ├── MetaInfo.php                    [注解解析类]
│           ├── AnnotationReader.php            [注解读取类]
│           ├── DocParser.php                   [第三方注解解析类]
│           ├── function.php                    [常用方法]
│           └── Logger.php                      [日志处理类]
├── LICENSE.txt                 [开源协议]
├── README.md                   [README]
├── vendor                      [composer资源文件]
├── composer.json               [composer配置文件]
├── composer.lock
└── index.php                   [入口文件]
```
### USE
#### 你如果想使用medoo，Twig以及其他，你应该先`composer install`和`composer update`
1. 找到框架配置文件，选择json或者php文件写入你的配置
2. 找到入口文件，**更改`define('APP', __DIR__ .DIRECTORY_SEPARATOR.'app');`定义app目录**
2. 找到初始化文件，**更改`define('CONF',__DIR__.DIRECTORY_SEPARATOR.'conf.php');`定义全局配置文件**
3. 找到初始化文件，**更改`Logger::register('debug','file');`设置日志级别和输出形式**
4. 找到初始化文件，更改`Loader::register();`注册自动类加载,传入true，开启错误的debug模式
5. 找到初始化文件，**查看确认，更改所有的register()函数**
6. 找到引导注册类，查看并确认31-45行代码（和json格式返回有关，不需要可以注释掉）
7. 开始正式写app,**注意在定义的app目录下写入**
8. **新建视图控制package名为*ctrl*(忽略大小写)**
9. **在*ctrl*中新建类xxx.php**
10. **注意申明命名空间**
11. 在类和方法体上可以申明注解**@route::请求方法("路由")**
    ```示例
    /**
     *@route("/Test")
     */
    class TestCtrl{
        /**
         *@route::get("/test") 
        */
        function test(){}
    }
    ```
12. route("")中的路由名也可以不加斜线
13. 或者**也可以和配置路由一起使用**，在配置文件中申明'route'(忽略大小写)项，按仿照默认的格式书写路由(**大小写严格**)
14. 现在function test(){}写上什么，打开你的浏览器，输入路由，看一看吧
15. 更多使用请查看示例文件：
```
ROOT\test.php 中写了一些缓存测试
APP\ctrls\indexCtrl.php 中示例了mysql的连接和使用()。以及模板引擎的使用方法
```
16. 关于模板引擎：通过composer使用的Twig，查看Twig官网
17. 静态资源请新建一个static放到ROOT下面，html的静态资源访问请使用/static/~
18. 现在可以使用verb\AutoGenerationClass::register(path,namespace)来连接数据库，自动生成实体类
