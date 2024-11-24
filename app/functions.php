<?php
/**
 * Here is your custom functions.
 */

use support\Cache;
use think\Template;

if(!function_exists('model')){
    function model(string $path) {
        list($module, $class) = explode('/',$path);
        $model = "app\\{$module}\\model\\".ucfirst($class);
        return new $model();
    }
}

if(!function_exists('think_view_display')){
    function think_view_display($content, $vars)
    {
        $defaultOptions = [
            'cache_path' => runtime_path() . '/views/',
        ];
        $options = array_merge($defaultOptions, config("view.options", []));
        $views = new Template($options);
        ob_start();
        if(isset($request->_view_vars)) {
            $vars = array_merge((array)$request->_view_vars, $vars);
        }
        $views->display($content, $vars);
        return ob_get_clean();
    }
}
if(!function_exists('url')){
    function url($url, $vars = []){
        $request = request();
        $query_str = http_build_query($vars);
        if(str_starts_with($url, '/')){
            $build = $url;
            goto done;
        }
        $path = $request->path();
        $path = ltrim($path, '/');
        $urlExplode = explode('/', $url);
        if(count($urlExplode) == 3){
            $build = $url;
        }else if(count($urlExplode) == 2){
            $build =  $request->app.'/'.$url;
        }else if(count($urlExplode) == 1){
            $pathExplode = explode('/', $path);
            $pathExplode[2] = $url;
            $build = implode('/', $pathExplode);
        }
        done:
        return $query_str? $build.'?'.$query_str: $build;
    }
}


if (!function_exists('cache')) {
    /**
     * 缓存管理
     * @param string|null $name 缓存名称
     * @param mixed $value 缓存值
     * @param mixed|null $options 缓存参数
     * @return mixed
     */
    function cache(string $name = null, mixed $value = '', mixed $options = null): mixed
    {
        if (is_null($name)) {
            return Cache::store();
        }

        if ('' === $value) {
            // 获取缓存
            return str_starts_with($name, '?') ? Cache::has(substr($name, 1)) : Cache::get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return Cache::delete($name);
        }

        // 缓存数据
        if (is_array($options)) {
            $expire = $options['expire'] ?? null; //修复查询缓存无法设置过期时间
        } else {
            $expire = $options;
        }
        return Cache::set($name, $value, $expire);
    }

    if (!function_exists('cookie')) {
        /**
         * Cookie管理
         * @param string $name   cookie名称
         * @param mixed  $value  cookie值
         * @param mixed  $option 参数
         * @return mixed
         */
        function cookie(string $name): mixed
        {
            $request = request();
            // 获取
            return str_starts_with($name, '?') ? $request->cookie($name) !== null : $request->cookie($name);
        }
    }
}