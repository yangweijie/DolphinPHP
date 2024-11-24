<?php

namespace app\middleware;

use ReflectionClass;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CheckAuth implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        if (session('user_auth')) {
            // 已经登录，请求继续向洋葱芯穿越
            return $handler($request);
        }

        // 通过反射获取控制器哪些方法不需要登录
        $controller = new ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // 访问的方法需要登录
        if (!in_array($request->action, $noNeedLogin)) {
            // 拦截请求，返回一个重定向响应，请求停止向洋葱芯穿越
            return redirect('/user/admin/publics/signin');
        }

        // 不需要登录，请求继续向洋葱芯穿越
        return $handler($request);
    }
}