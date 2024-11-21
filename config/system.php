<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

return [
    // 拒绝ie访问
    'deny_ie'       => false,
    // 模块管理中，不读取模块信息的目录
    'except_module' => ['common', 'admin', 'index', 'extra', 'user', 'install'],
    // 函数过滤方式，black_list：黑名单，white_list：白名单
    'function_filter' => 'white_list',
    // 函数黑名单，在黑名单内的函数将不会被执行
    'function_black_list' => [
        'eval',
        'passthru',
        'exec',
        'system',
        'chroot',
        'chgrp',
        'popen',
        'ini_alter',
        'ini_restore',
        'dl',
        'openlog',
        'syslog',
        'readlink',
        'symlink',
        'popepassthru',
        'phpinfo',
        'shell_exec',
        'fopen',
        'fclose',
        'fread',
        'fwrite',
        'file_get_contents',
        'file_put_contents',
        'unlink',
        'rename',
        'copy',
        'file',
        'file_exists',
        'mkdir',
        'rmdir',
        'opendir',
        'readdir',
        'scandir',
        'chdir',
        'chroot',
        'dir',
        'closedir',
        'getenv',
        'putenv',
        'get_current_user',
        'get_cfg_var',
        'getmyuid',
        'getmypid',
        'getmyinode',
        'getlastmod',
        'fsockopen',
        'pfsockopen',
        'socket_create',
        'socket_bind',
        'socket_listen',
        'socket_accept',
        'socket_connect',
        'socket_strerror',
        'stream_socket_server',
        'proc_open',
        'proc_close',
        'proc_terminate',
        'proc_get_status',
        'proc_nice',
        'assert',
        'php_uname',
        'getrusage',
        'get_include_path',
        'set_include_path',
        'ini_set',
        'pcntl_exec',
        'posix_kill',
        'posix_mkfifo',
        'posix_setpgid',
        'posix_setsid',
        'posix_setuid',
        'posix_seteuid',
        'posix_setegid',
        'posix_setgid',
        'posix_uname',
        'fileatime',
        'filectime',
        'fileinode',
        'is_dir',
        'is_executable',
        'is_writable',
        'filegroup',
        'fileowner',
        'is_file',
        'is_writeable',
        'stat',
        'fileperms',
        'is_link',
        'parse_ini_file',
        'readfile'
    ],
    // 函数白名单，在白名单内的函数才会被执行，空则所有函数都不执行
    'function_white_list' => []
];
