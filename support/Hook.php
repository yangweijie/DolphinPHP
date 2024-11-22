<?php
namespace support;

use think\helper\Str;

class Hook
{
    function __construct(){

    }

    function exec($data, $event_name){
        $hook_name = str_replace('hook.', '', $event_name);
        $hooks = Cache::get('hooks', []);
        if(!array_key_exists($hook_name, $hooks)){
            return '';
        }
        $key = "hook_plugin_$hook_name";
        $plugins = Cache::get($key);
        if($plugins){
            $method = Str::camel($hook_name);
            $ret = '';
            foreach($plugins as $k=>$plugin){
                try {
                    $plugin_class = get_plugin_class($plugin);
                    $ret .= call_user_func_array([new $plugin_class(), $method],$data);
                }catch (\Exception $e){
                    Log::error("运行插件{$plugin}->{$method} 报错".PHP_EOL.$e->getMessage().PHP_EOL.$e->getTraceAsString());
                }
            }
            return $ret;
        }
    }

    public static function add($hook_name, $plugin){
        $key = "hook_plugin_$hook_name";
        $plugins = Cache::get($key, []);
        array_push($plugins, $plugin);
        array_unique($plugins);
        Cache::set($key, $plugins);
    }

    public static function get($hook_name){
        $key = "hook_plugin_$hook_name";
        return Cache::get($key, []);
    }
}