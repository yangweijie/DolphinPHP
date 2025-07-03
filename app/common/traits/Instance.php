<?php

namespace app\common\traits;

trait Instance
{
    static protected $Instance = null;

    static public function Instance(...$params)
    {
        if (static::$Instance === null)
        {
            static::$Instance = new static($params);
        }
        return static::$Instance;
    }
}