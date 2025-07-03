<?php

/**
 * 用法：
 * class index
 * {
 *     use \traits\controller\Jump;
 *     public function index(){
 *         $this->error();
 *         return redirect();
 *     }
 * }
 */

namespace app\common\traits;

use think\Container;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Request;
use think\Response;
use think\response\Redirect;

trait Jump
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param string|object|null $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function success(string $msg = '', string|object $url = null, $data = '', $wait = 3, array $header = [])
    {
        $url = is_object($url) ? (string)$url : $url;
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ('' !== $url) {

            $url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url :  (string) \think\facade\Route::buildUrl($url);
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        // return json($result);
        $type = $this->getResponseType();
        // 把跳转模板的渲染下沉，这样在 response_send 行为里通过getData()获得的数据是一致性的格式

        $response = Response::create($result, $type)->header($header)->options([
            // 'jump_template' => $this->app['config']->get('app.dispatch_success_tmpl')
        ]);

        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param string|object|null $url 跳转的URL地址
     * @param mixed $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @param array $header 发送的Header信息
     * @return void
     */
    protected function error(string $msg = '', string|object $url = null, $data = '', $wait = 3, array $header = [])
    {
        $url = is_object($url) ? (string)$url : $url;
        $type = $this->getResponseType();
        if (is_null($url)) {
            $url = $this->app['request']->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : (string) \think\facade\Route::buildUrl($url);
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $response = Response::create($result, $type)->header($header)->options(['jump_template' => Config::get('app.exception_tmpl')]);

        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param  mixed     $data 要返回的数据
     * @param  integer   $code 返回的code
     * @param  mixed     $msg 提示信息
     * @param  string    $type 返回数据格式
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        $config = Config::get('app');
        return Request::isAjax()
            ? $config['default_ajax_return']
            : $config['default_return_type'];
    }
}
