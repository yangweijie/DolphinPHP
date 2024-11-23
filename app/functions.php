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