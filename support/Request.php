<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace support;

/**
 * Class Request
 * @package support
 */
class Request extends \Webman\Http\Request
{

    /**
     * 当前控制器名
     * @var string
     */
    public $controller;

    /**
     * 获取当前的控制器名
     * @access public
     * @param  bool $convert 转换为小写
     * @return string
     */
    public function controller(bool $convert = false): string
    {
        $path = explode('\\', $this->controller);
        $name = $path? $path[array_key_last($path)]: '';
        return $convert ? strtolower($name) : $name;
    }

    public function action(){
        return $this->action;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELETE请求
     * @access public
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function scheme(): string
    {
        return str_contains($this->url(), 'https')? 'https': 'http';
    }

    /**
     * 获取当前包含协议的域名
     * @access public
     * @param  bool $port 是否需要去除端口号
     * @return string
     */
    public function domain(bool $port = false): string
    {
        return $this->scheme() . '://' . $this->host($port);
    }

    public function ip(bool $long = false): string
    {
        return $long? ip2long($this->getRemoteIp()):$this->getRemoteIp();
    }

    public function time(bool $float = false)
    {
        $header = $this->header();
        if(isset($header['REQUEST_TIME'])){
            return $float? $header['REQUEST_TIME_FLOAT'] : $header['REQUEST_TIME'];
        }else{
            $header['REQUEST_TIME'] = time();
            $header['REQUEST_TIME_FLOAT'] = microtime(true);
            $this->setHeaders($header);
            return $header['REQUEST_TIME'];
        }
    }

    public function module(): ?string
    {
        return $this->app;
    }


}