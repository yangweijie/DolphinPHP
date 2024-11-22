<?php
//定义切入方法区分大小写
use app\aspect\MultiAppDolphinAspect;
use app\controller\Index;

return [
    MultiAppDolphinAspect::class => [
        Index::class => [
            'index',
        ],
    ],
//    MysqlAspect::class => [
//        PDOConnection::class => [  //底层数据库执行方法切入例子
//           'getPDOStatement',//方法
//        ],
//    ],
];