<?php
/**
 * Here is your custom functions.
 */

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