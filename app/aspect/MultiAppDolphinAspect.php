<?php

namespace app\aspect;


use Xiaoyangguang\WebmanAop\AspectInterface;

class MultiAppDolphinAspect implements AspectInterface
{
//    private float $time;
//
//    // 记录方法执行时间
//    public function before(AopTarget $aopTarget): void
//    {
//        $this->time = microtime(true);
//    }
//
//    public function after(AopTarget $aopTarget, mixed $result): void
//    {
//        $time = microtime(true) - $this->time;
//        var_dump('after: ' . $aopTarget->getTargetClass() . '::' . $aopTarget->getMethod() . " -> return type:" . gettype($result) . " time: " . $time);
//    }

    /**
     * 前置通知
     * @param $params
     * @param $method
     * @return void
     */
    public static function beforeAdvice(&$params, $class, $method): void
    {
        var_dump('beforeAdvice', $params, $method);
        echo PHP_EOL;
    }

    /**
     * 后置通知
     * @param $res
     * @param $params
     * @param $method
     * @return mixed|void
     */
    public static function afterAdvice(&$res, $params, $class, $method): void
    {
        var_dump('afterAdvice', $res, $params, $method);
        echo PHP_EOL;
    }

    public static function exceptionHandler($throwable, $params, $class, $method): void
    {
        // TODO: Implement exceptionHandler() method.
    }
}