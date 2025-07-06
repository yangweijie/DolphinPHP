<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [//内置的系统事件
        'AppInit'  => [//应用初始化标签位

        ],
        'HttpRun'  => [//应用开始标签位
            'app\common\event\InitRoute',
            'app\common\event\Config',
            'app\common\event\Hook',
            'app\common\event\AdminAuth',
        ],
        'HttpEnd'  => [],//	应用结束标签位
        'LogWrite' => [],//日志write方法标签位
        'RouteLoaded' => [],//路由加载完成
        'LogRecord' => [],//日志记录
    ],

    'subscribe' => [
    ],
];