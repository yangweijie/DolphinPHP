<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\common\event;

use app\admin\model\Hook as HookModel;
use app\admin\model\HookPlugin as HookPluginModel;
use app\admin\model\Plugin as PluginModel;
use think\App;
use think\helper\Str;
use think\facade\Cache;
use think\facade\Event;

/**
 * 注册钩子
 * @package app\common\listener
 * @author 蔡伟明 <314013107@qq.com>
 */
class Hook
{
    /** @var App */
    protected $app;
    /**
     * 路径入口判断
     * @var string
     */
    protected $appname;
    public function __construct(App $app)
    {
        $this->app     = $app;
    }
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function handle($event)
    {
        if ($this->appname === 'apidoc') return;
        $this->appname  = request()->appname;

        //如果是安装操作，直接返回
        if ($this->appname === 'install') return;


        $hook_plugins = Cache::get('hook_plugins');
        $hooks        = Cache::get('hooks');
        $plugins      = Cache::get('plugins');

        if (!$hook_plugins) {
            // 所有钩子
            $hooks = HookModel::where('status', 1)->column('status', 'name');

            // 所有插件
            $plugins = PluginModel::where('status', 1)->column('status', 'name');

            // 钩子对应的插件
            $hook_plugins = HookPluginModel::where('status', 1)->order('hook,sort')->select()->toArray();

            // 非开发模式，缓存数据
            if (config('app.develop_mode') == 0) {
                Cache::set('hook_plugins', $hook_plugins);
                Cache::set('hooks', $hooks);
                Cache::set('plugins', $plugins);
            }
        }

        if ($hook_plugins) {
            foreach ($hook_plugins as $value) {

                $event = Str::camel($value['hook']);
                if (isset($hooks[$value['hook']]) && isset($plugins[$value['plugin']])) {
                    $class =	'\\' . get_plugin_class($value['plugin']);
                    if (class_exists($class)) {
                        Event::listen($value['hook'], [app($class), $event]);
                    }
                }
            }
        }
    }
}
