<?php

namespace app\aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Webman\App;


class MultiAppDolphinAspect extends AbstractAspect
{
    public array $classes = [
        App::class . '::getAppByController',
        App::class . '::findRoute',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $result = $proceedingJoinPoint->process();
        var_dump($result);
        return $result;
    }
}