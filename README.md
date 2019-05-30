# VERB
## It's a pity to abandon the tasteless food

### After a semester of studying php, write a small framework

```
verb                            [框架目录]
│
├── application                 [app目录]
│   │
│   ├── ctrl                    [控制器目录]
│   │  
│   ├── model                   [数据模型目录]
│   │   
│   ├── rsa                     [私钥公钥存放目录]│   
│   │ 
│   └── viws                    [视图目录]
│    
├── core                        [PHPMS核心框架目录]
│    │
│    ├── common                 [公共函数目录]
│    │   │
│    │   └──function.php        [自定义公共函数]
│    │
│    ├── config                 [配置目录]
│    │
│    ├── flight                 [flight 引擎目录]
│    │
│    ├── lib                    [驱动目录]
│    │   
│    └── phpmsframe.php         [框架类]
│
├── readme                      [PHPMS框架开发思路和笔记]
│    
└── public                      [公共资源目录]
.gitignore                      [git忽略文件配置]
.htaccess                       [伪静态文件]
api.php                         [api入口文件]  
favicon.ico                     [ico图标] 
index.php                       [后端入口文件]
LICENSE                         [lincese文件]
composer.json                   [composer配置文件]
composer.lock                   [composer lock文件]
README.md                       [readme文件]
w_start_web.bat                 [win下一键启动项目文件] 
```