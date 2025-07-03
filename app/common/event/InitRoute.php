<?php

/**
 * @copyright 2022 https://www.sapixx.com All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 * @link https://www.sapixx.com
 * @author pillar<ltmn@qq.com>
 * API访问全局入口控制
 */

declare(strict_types=1);

namespace app\common\event;

use think\App;
use think\facade\Config;
use think\facade\Env;

/**
 * API解析到指定的应用
 */
class InitRoute
{

    /** @var App */
    protected $app;

    /**
     * 应用名称
     * @var string
     */
    protected $appname;

    /**
     * 应用三方库
     * @var string
     */
    protected $composer;

    /**
     * 应用路径
     * @var string
     */
    protected $path;

    /**
     * 路径入口判断
     * @var string
     */
    protected $module;

    /**
     * API版本
     * @var string
     */
    protected $version;

    /**
     * 控制器
     * @var string
     */
    protected $controller;
    /**
     * 方法
     * @var string
     */
    protected $action;
    /**
     * 请求对象
     * @var string
     */
    protected $request;


    /**
     * 路径入口文件判断
     * @var string
     */
    protected $import;
    /**
     * pathinfo
     * @var string
     */
    protected $pathinfo;
    public function __construct(App $app)
    {
        $this->app				= $app;
        $this->request			= request();
        $path				= $this->app->request->pathinfo();
        $pathinfo			= explode('/', $path);
        $this->composer 		= request()->appname	= $this->appname	= trim(strtolower(current($pathinfo))); //应用名称
        $this->controller       = trim(strtolower(next($pathinfo) ?: 'index'));
        $this->action           = trim(strtolower(next($pathinfo) ?: 'index'));
        $pathInfo 				= ltrim(substr($path, strlen($this->appname . '/' . $this->controller . '/' . $this->action)), '/');
        $pathInfo 				= $this->controller . '/' . $this->action . ($pathInfo ? '/' . $pathInfo : '');
        request()->oldpath 		= $this->appname . '/' . $pathInfo;
        request()->import  		= $this->import		= $this->getInitialFileName(); //控制器层
        request()->import_file  = $this->getInitialFileName(true); //控制器层

    }
    /**
     * 多应用解析
     * @access public
     */
    public function handle()
    {
        $this->checkInstall($this->appname);
        // // 如果是安装操作，直接返回
        if ($this->appname === 'install') return;
        if ($this->appname === 'apidoc') return;


        $view =	config('view');

        // 如果定义了入口为admin，则修改默认的访问控制器层
        if ($this->import == 'admin') {

            if ($this->appname == '') {
                header("Location: " . request()->domain() . '/' . $this->import . '.php/admin', true, 302);
                exit();
            }

            if ($this->appname != $this->import && in_array($this->import, config('zbuilder.default_controller_layer'))) {
                // 修改默认访问控制器层
                $route_config = config('route');
                $route_config['controller_layer'] = 'admin';
                // 修改视图模板路径
                $view['view_path'] = app_path() . $this->appname . '/view/admin/';
                config($route_config, 'route');
            }
        } elseif ($this->import == 'api') {

            if ($this->appname == '') {
                header("Location: " . request()->domain() . '/' . $this->import . '.php/index', true, 302);
                exit();
            }
            // 修改默认访问控制器层
            $route_config = config('route');
            $route_config['controller_layer'] = 'api';
            config($route_config, 'route');
        } else {
            if ($this->appname == 'admin') {
                header("Location: " . request()->domain() . '/' . $this->appname . '.php/admin', true, 302);
                exit();
            }

            if ($this->appname != '' && !in_array($this->appname, config('zbuilder.default_controller_layer'))) {
                // 修改默认访问控制器层
                $route_config = config('route');
                $route_config['controller_layer'] = 'home';
                config($route_config, 'route');
            }
        }
        config($view, 'view');
        $this->LoadAppComposer();
        // $this->analysis();
    }
    protected function analysis()
    {

        $this->app->http->name($this->appname);
        $pathinfo		= explode('/', $this->path);
        trim(strtolower(current($pathinfo) ?: 'index'));
        $this->controller       = trim(strtolower(next($pathinfo) ?: 'index'));
        $this->action           = trim(strtolower(next($pathinfo) ?: 'index'));
        $pathInfo = ltrim(substr($this->path, strlen($this->appname . '/' . $this->controller . '/' . $this->action)), '/');
        $pathInfo =  $this->controller . '/' . $this->action . ($pathInfo ? '/' . $pathInfo : '');
        request()->oldpath =  $this->appname . '/' . $pathInfo;
        $this->app->request->setPathinfo($pathInfo);

        // dump([
        // 	'$this->controller' => $this->controller,
        // 	'module' =>	$this->module,
        // 	'import' => $this->import,
        // 	'appName' =>	app('http')->getName(),
        // 	'action' =>	$this->action,
        // 	'controller' =>	$this->controller,
        // 	'pathinfo' =>	request()->pathinfo(),
        // 	'getNamespace' =>	app()->getNamespace()
        // ]);
    }

    function getInitialFileName($is_prefix = false)
    {
        $trace = debug_backtrace();
        $initialFile = $trace[count($trace) - 1]['file'];

        if ($is_prefix) {
            $basename =  basename($initialFile) ?: 'index.php';
        } else {
            $basename =   basename($initialFile, '.php') ?: 'index';
        }

        return $basename;
    }

    /**
     * 载入应用独立资源包
     */
    private function LoadAppComposer()
    {

        $autoLoadFile = $this->app->getAppPath() . $this->composer . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        if (file_exists($autoLoadFile)) {
            require_once $autoLoadFile;
        }
    }
    private function checkInstall($appname = '')
    {
        if (!file_exists(root_path() . "data/install.lock") && $appname != 'install') {
            redirect(request()->domain() . '/install/index/index')->send() or die;
        }

        if (file_exists(root_path() . "data/install.lock") && $appname == 'install') {
            redirect(request()->domain() . '/index/index/index')->send() or die;
        }
    }
}
