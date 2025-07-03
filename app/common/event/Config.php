<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\common\event;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;
use think\facade\Config as FacadeConfig;
use think\facade\Env;
use think\facade\Request;
use think\facade\App;


/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\listener
 * @author CaiWeiMing <314013107@qq.com>
 */
class Config
{
    /** @var App */
    protected $app;
    /**
     * 模块名称
     * @var string
     */
    protected $appname;
    /**
     * 请求对象
     * @var string
     */
    protected $request;
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = request();
        $this->appname  = trim(strtolower(current(explode('/',  $this->request->pathinfo()))));
    }
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @return void
     */
    public function handle()
    {
        if ($this->appname === 'apidoc') return;
        //如果是安装操作，直接返回
        if (request()->appname === 'install') return;
        // 获取入口目录
        $base_file = Request::baseFile();
        $base_dir  = substr($base_file, 0, strripos($base_file, '/') + 1);

        Env::set('public_path', $base_dir);

        // 视图输出字符串内容替换
        $view_replace_str = [
            // 静态资源目录
            '__STATIC__'    => $base_dir . 'static',
            // 文件上传目录
            '__UPLOADS__'   => $base_dir . 'uploads',
            // JS插件目录
            '__LIBS__'      => $base_dir . 'static/libs',
            // 后台CSS目录
            '__ADMIN_CSS__' => $base_dir . 'static/admin/css',
            // 后台JS目录
            '__ADMIN_JS__'  => $base_dir . 'static/admin/js',
            // 后台IMG目录
            '__ADMIN_IMG__' => $base_dir . 'static/admin/img',
            // 前台CSS目录
            '__HOME_CSS__'  => $base_dir . 'static/home/css',
            // 前台JS目录
            '__HOME_JS__'   => $base_dir . 'static/home/js',
            // 前台IMG目录
            '__HOME_IMG__'  => $base_dir . 'static/home/img',
            // 表单项扩展目录
            '__EXTEND_FORM__' => $base_dir . 'extend/form',
            // 插件目录
            '__PLUGINS__' =>  $base_dir . 'plugins',

            // 定义模块资源目录
            '__MODULE__' => $base_dir . 'module/' . $this->appname . '',
            '__MODULE_CSS__' => $base_dir . 'module/' . $this->appname . '/css',
            '__MODULE_JS__' => $base_dir . 'module/' . $this->appname . '/js',
            '__MODULE_IMG__' => $base_dir . 'module/' . $this->appname . '/img',
            '__MODULE_LIBS__' => $base_dir . 'module/' . $this->appname . '/libs',
        ];

        $view_config =  FacadeConfig::get('view');
        $view_config['tpl_replace_string'] = $view_replace_str;
        $view_config['public_static_path'] = $base_dir . 'static/';
        FacadeConfig::set($view_config, 'view');



        // 如果是安装操作，直接返回
        if ($this->appname === 'install') return;

        // 静态文件目录

        $app_config = config('app');
        $app_config['public_static_path'] =   $base_dir . 'static/';
        config($app_config, 'app');

        // 读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            $system_config = ConfigModel::getConfig();
            // 所有模型配置
            $module_config = ModuleModel::where('config', '<>', '')->column('config', 'name');
            foreach ($module_config as $module_name => $config) {
                $system_config[strtolower($module_name) . '_config'] = json_decode($config, true);
            }
            // 非开发模式，缓存系统配置
            if ($system_config['develop_mode'] == 0) {
                cache($system_config, 'system_config');
            }
        }
        // 设置配置信息
        config($system_config, 'app');
    }
}
