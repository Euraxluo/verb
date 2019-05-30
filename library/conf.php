<?php
return [
    "Route" => [
        "properties" => [
            "export_apis" => true,
            "url_begin" => 1,
            "api_path" => __DIR__ . "/apis/",
            "default_strict_matching" => true,
        ],
        "default"=>[//路由规则
            'CTRL'=>'index',
            'ACTION'=>'index'
        ]
    ],
    "Database" => [// medoo和pdo的公共配置项,如果你要使用medoo,请composer install
        "database_type" => "mysql",
        "database_name" => "movie",
        "server" => "127.0.0.1",
        "username" => "movie",
        "password" => "6nDyGxz2SML2DSa7",
        "charset" => "utf8"
    ],
    "Log" => [
        "DRIVE" => "file",//驱动类型，todo：mysql
        "OPTION" => [//相关设定
            "PATH" => ROOT . "/log/"
        ]
    ]
];
