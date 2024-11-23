<?php

namespace Kingbes\Jump;

class Jump
{
    /**
     * 获取当前的response 输出类型
     *
     * @return string
     */
    protected function getResponseType(): string
    {
        return request()->acceptJson() || request()->isAjax() ? 'json' : 'html';
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param  mixed $msg 提示信息
     * @param  string $url 跳转的URL地址
     * @param  mixed $data 返回的数据
     * @param  integer $wait 跳转等待时间
     * @param  array $header 发送的Header信息
     * @return void
     */
    public function success($msg = '', string $url = null, $data = '', int $wait = 2, array $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : route($url);
        }

        $result = [
            'code' => 1,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ('html' == strtolower($type)) {
            static $handler;
            if (null === $handler) {
                $handler = config('view.handler');
            }
            //模板路径 BASE_PATH . '/public/jump.html'
            return response($handler::render(config("plugin.kingbes.jump.app.jump"), $result), 200, $header);
        } else {
            return json($result);
        }
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param  mixed $msg 提示信息
     * @param  string $url 跳转的URL地址
     * @param  mixed $data 返回的数据
     * @param  integer $wait 跳转等待时间
     * @param  array $header 发送的Header信息
     * @return void
     */
    public function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
    {
        if (is_null($url)) {
            $url = request()->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ($url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : route($url);
        }

        $result = [
            'code' => 2,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();

        if ('html' == strtolower($type)) {
            static $handler;
            if (null === $handler) {
                $handler = config('view.handler');
            }
            //模板路径 BASE_PATH . '/public/jump.html'
            return response($handler::render(config("plugin.kingbes.jump.app.jump"), $result), 200, $header);
        } else {
            return json($result);
        }
    }

    /**
     * API数据到客户端 function
     *
     * @param array $data 要返回的数据
     * @param integer $code 返回的code
     * @param string $msg 提示信息
     * @param array $header 发送的Header信息
     * @return void
     */
    public function result(array $data, int $code = 1, string $msg = "", array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $header['Content-Type'] = 'application/json';

        return response(json_encode($result, 320), 200, $header);
    }
}
