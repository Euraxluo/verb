<?php
return [
    "route" => [ //建议配置，也可以同时使用注解，配置时延由过期时间决定
        '/' => [
            'class' => 'app\\TestCtrls\\test',
            'method' => 'index',
            'routeType' => 'GET'
        ]
    ],
    "database" => [ //如果要使用model，必须配置
        "driver" => "medoo",
        "OPTION" => [ // medoo和pdo的公共配置项,如果你要使用medoo,请composer install
            "database_type" => "mysql",
            "database_name" => "online_exam",
            "server" => "127.0.0.1",
            "username" => "online_exam",
            "password" => "53yGd7zctsJMfxDP",
            "charset" => "utf8"
        ]
    ],
    "log" => [ //如果使用文件输出日志，必须配置
        "driver" => "file", //驱动类型，todo：mysql
        "OPTION" => [ //相关设定
            "LEVEL" =>'debug',//日志级别
            "PATH" => ROOT . "/log" //path to log
        ]
    ],
    "cache" => [
        "driver" => "file",
        "OPTION" =>[
            'host'       => '127.1', //string,host,用于Redis缓存
            'port'       => 6379, //int,post,用于Redis缓存
            'password'   => '', //string,passwd,用于Redis缓存
            'select'     => 0, //int,选择数据库,用于Redis缓存
            'timeout'    => 10, //int,超时时间(秒),用于Redis缓存
            'persistent' => false, //false/true,是否长连接,用于Redis缓存
            'cache_subdir'  => true, //是否使用缓存子目录,用于文件缓存
            'path'          => ROOT . '/tmp', //string,缓存目录,用于文件缓存
            'hash_type'     => 'md5', //string,哈希函数,用于文件缓存
            'data_compress' => false, //false/true,是否压缩,用于文件缓存
            'prefix'     => 'verb_', //string, 默认前缀,通用
            'serialize'  => true, //true/false,是否序列化,通用
            'expire'     => 0, //int,默认过期时间(秒),通用
        ]
    ]
];
