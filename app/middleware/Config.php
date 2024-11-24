<?php

namespace app\middleware;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class Config implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        if(defined('BIND_MODULE') && BIND_MODULE === 'install') goto done;
        // 获取入口目录
        if(!defined('PUBLIC_PATH'))
            define('PUBLIC_PATH', '');
        $app = request()->app;
        if($app == ''){
            $app = 'index';
            request()->app = $app;
        }

        // 视图输出字符串内容替换
        $view_replace_str = [
            // 静态资源目录
            '__STATIC__'    => PUBLIC_PATH. 'static',
            // 文件上传目录
            '__UPLOADS__'   => PUBLIC_PATH. 'uploads',
            // JS插件目录
            '__LIBS__'      => PUBLIC_PATH. 'static/libs',
            // 后台CSS目录
            '__ADMIN_CSS__' => PUBLIC_PATH. 'static/admin/css',
            // 后台JS目录
            '__ADMIN_JS__'  => PUBLIC_PATH. 'static/admin/js',
            // 后台IMG目录
            '__ADMIN_IMG__' => PUBLIC_PATH. 'static/admin/img',
            // 前台CSS目录
            '__HOME_CSS__'  => PUBLIC_PATH. 'static/home/css',
            // 前台JS目录
            '__HOME_JS__'   => PUBLIC_PATH. 'static/home/js',
            // 前台IMG目录
            '__HOME_IMG__'  => PUBLIC_PATH. 'static/home/img',
            // 表单项扩展目录
            '__EXTEND_FORM__' => PUBLIC_PATH.'extend/form'
        ];
        // 定义模块资源目录
        $view_replace_str = array_merge($view_replace_str, [
            '__MODULE_CSS__'=>PUBLIC_PATH. 'static/'. $app .'/css',
            '__MODULE_JS__'=>PUBLIC_PATH. 'static/'. $app .'/js',
            '__MODULE_IMG__'=>PUBLIC_PATH. 'static/'. $app .'/img',
            '__MODULE_LIBS__'=>PUBLIC_PATH. 'static/'. $app .'/libs',
        ]);
        $view_options = [
            'tpl_replace_string'=>$view_replace_str,
        ];
        config($view_options, 'view.options');
//
//        // 静态文件目录
        config(['public_static_path'=>PUBLIC_PATH. 'static/'], 'app');

        // 读取系统配置
//        $system_config = cache('system_config');
//        if (!$system_config) {
            $ConfigModel   = new ConfigModel();
            $system_config = $ConfigModel->getConfigs();
            // 所有模型配置
            $module_config = ModuleModel::where('config', '<>', '')->column('config', 'name');
            foreach ($module_config as $module_name => $config) {
                $system_config[strtolower($module_name).'_config'] = json_decode($config, true);
            }
            // 非开发模式，缓存系统配置
            if ($system_config['develop_mode'] == 0) {
//                cache('system_config', $system_config);
            }
//        }

//        var_dump('system_config');
//        var_dump($system_config);
        // 设置配置信息
        config($system_config, 'app');
        done:
        return $handler($request);
    }
}