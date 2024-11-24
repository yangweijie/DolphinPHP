<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use support\Request;

return [

    // +----------------------------------------------------------------------
    // | 系统相关设置
    // +----------------------------------------------------------------------

    // 后台公共模板
    'admin_base_layout'  => base_path() . DIRECTORY_SEPARATOR . 'app/admin/view/layout.html',
    // 插件目录路径
    'plugin_path'        => base_path() . DIRECTORY_SEPARATOR . 'plugins/',
    // 数据包目录路径
    'packet_path'        => base_path() . DIRECTORY_SEPARATOR  . 'packet/',
    // 文件上传路径
    'upload_path'        => base_path() . DIRECTORY_SEPARATOR  . 'public' . DIRECTORY_SEPARATOR . 'uploads',
    // 文件上传临时目录
    'upload_temp_path'   => base_path() . DIRECTORY_SEPARATOR  . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'temp/',

    // // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => base_path() . DIRECTORY_SEPARATOR . 'app/admin/view/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => base_path() . DIRECTORY_SEPARATOR . 'app/admin/view/dispatch_jump.tpl',

    'debug' => true,
    'error_reporting' => E_ALL,
    'default_timezone' => 'Asia/Shanghai',
    'request_class' => Request::class,
    'public_path' => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path' => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix' => '',
    'controller_reuse' => false,


    // 多应用
    'install'=>[
        //产品配置
        'install_product_name'   => 'DolphinPHP', //产品名称
        'install_website_domain' => 'http://www.dolphinphp.com', //官方网址
        'install_company_name'   => '广东卓锐软件有限公司', //公司名称
        'original_table_prefix'  => 'dp_', //默认表前缀

        // 安装配置
        'install_table_total' => 253, // 安装时，需执行的sql语句数量
    ]
];
