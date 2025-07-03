<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [

    // +----------------------------------------------------------------------
    // | 系统相关设置
    // +----------------------------------------------------------------------

    // 后台公共模板
    'admin_base_layout'  => root_path() . 'app/admin/view/layout.html',
    // 插件目录路径
    'plugin_path'        => root_path() . 'plugins/',
    // 数据包目录路径
    'packet_path'        => root_path()  . 'packet/',
    // 文件上传路径
    'upload_path'        => root_path()  . 'public' . DIRECTORY_SEPARATOR . 'uploads',
    // 文件上传临时目录
    'upload_temp_path'   => root_path()  . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'temp/',

    // // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => root_path() . 'app/admin/view/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => root_path() . 'app/admin/view/dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 用户相关设置
    // +----------------------------------------------------------------------

    // 最大缓存用户数
    'user_max_cache' => 1000,
    // 管理员用户ID
    'user_admin'     => 1,
    // 应用地址
    'app_host'         => env('APP_HOST', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 开启应用快速访问
    // 'app_express'    =>    true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ['common'],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,


    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
];
