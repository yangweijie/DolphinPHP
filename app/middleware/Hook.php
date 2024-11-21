<?php

namespace app\middleware;

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
        done:
        $response = $handler($request);
        return $response;
    }
}