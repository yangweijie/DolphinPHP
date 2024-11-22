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
        define('PUBLIC_PATH', '');

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
        config('template.tpl_replace_string', $view_replace_str);
        $app = request()->app;
        if($app == ''){
            $app = 'index';
            request()->app = $app;
        }

        // 如果定义了入口为admin，则修改默认的访问控制器层
//        if(defined('ENTRANCE') && ENTRANCE == 'admin') {
//            define('ADMIN_FILE', substr($base_file, strripos($base_file, '/') + 1));
//
//            if ($module == '') {
//                header("Location: ".$base_file.'/admin', true, 302);exit();
//            }
//
//            if (!in_array($module, config('module.default_controller_layer'))) {
//                // 修改默认访问控制器层
//                config('url_controller_layer', 'admin');
//                // 修改视图模板路径
//                config('template.view_path', app_path(). $module. '/view/admin/');
//            }
//
//            // 插件静态资源目录
//            config('template.tpl_replace_string.__PLUGINS__', '/plugins');
//        } else {
//            if ($module == 'admin') {
//                header("Location: ".$base_dir.ADMIN_FILE.'/admin', true, 302);exit();
//            }
//
//            if ($module != '' && !in_array($module, config('module.default_controller_layer'))) {
//                // 修改默认访问控制器层
//                config('url_controller_layer', 'home');
//            }
//        }

        // 定义模块资源目录
        config('template.tpl_replace_string.__MODULE_CSS__', PUBLIC_PATH. 'static/'. $app .'/css');
        config('template.tpl_replace_string.__MODULE_JS__', PUBLIC_PATH. 'static/'. $app .'/js');
        config('template.tpl_replace_string.__MODULE_IMG__', PUBLIC_PATH. 'static/'. $app .'/img');
        config('template.tpl_replace_string.__MODULE_LIBS__', PUBLIC_PATH. 'static/'. $app .'/libs');
        // 静态文件目录
        config('public_static_path', PUBLIC_PATH. 'static/');

        // 读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            $ConfigModel   = new ConfigModel();
            $system_config = $ConfigModel->getConfig();
            // 所有模型配置
            $module_config = ModuleModel::where('config', 'neq', '')->column('config', 'name');
            foreach ($module_config as $module_name => $config) {
                $system_config[strtolower($module_name).'_config'] = json_decode($config, true);
            }
            // 非开发模式，缓存系统配置
            if ($system_config['develop_mode'] == 0) {
                cache(['system_config'=> $system_config]);
            }
        }

        // 设置配置信息
        config(['app'=>$system_config]);
        done:
        return $handler($request);
    }
}