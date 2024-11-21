<?php

namespace app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class Config implements MiddlewareInterface
{

    public function process(Request $request, callable $handler): Response
    {
        // TODO: Implement process() method.
    }
}