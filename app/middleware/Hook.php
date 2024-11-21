<?php

namespace app\middleware;

use app\admin\model\Hook as HookModel;
use app\admin\model\HookPlugin as HookPluginModel;
use app\admin\model\Plugin as PluginModel;
use support\Cache;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class Hook implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        if(defined('BIND_MODULE') && BIND_MODULE === 'install') goto done;
        $hook_plugins = Cache::get('hook_plugins');
        $hooks        = Cache::get('hooks');
        $plugins      = Cache::get('plugins');
        if (!$hook_plugins) {
            // 所有钩子
            $hooks = HookModel::where('status', 1)->column('status', 'name');
            // 所有插件
            $plugins = PluginModel::where('status', 1)->column('status', 'name');
            // 钩子对应的插件
            $hook_plugins = HookPluginModel::where('status', 1)->order('hook,sort')->select();
            // 非开发模式，缓存数据
            if (config('develop_mode') == 0) {
                Cache::set('hook_plugins', $hook_plugins);
                Cache::set('hooks', $hooks);
                Cache::set('plugins', $plugins);
            }
        }

        if ($hook_plugins) {
            foreach ($hook_plugins as $value) {
                if (isset($hooks[$value['hook']]) && isset($plugins[$value['plugin']])) {
                    if ($value['hook'] == 'upload_attachment') {
                        if (strtolower(parse_name(config('upload_driver'), 1)) == strtolower($value['plugin'])) {
                            \support\Hook::add($value['hook'], get_plugin_class($value['plugin']));
                        }
                    } else {
                        \support\Hook::add($value['hook'], get_plugin_class($value['plugin']));
                    }
                }
            }
        }
        done:
        $response = $handler($request);
        return $response;
    }
}