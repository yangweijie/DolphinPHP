<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\common\controller;



/**
 * 消息发送构造类
 * 该类用于构造消息发送类，并发送消息
 * @package app\common\controller
 * @author 蔡伟明 <314013107@qq.com>
 */
abstract class Msgout
{
    /**
     * @var string 错误信息
     */
    protected $error = '';

    /**
     * 获取错误信息
     * @author 蔡伟明 <314013107@qq.com>
     * @return string
     */
    final public function getError()
    {
        return $this->error;
    }


    private $msg_template;

    /**
     * 设置消息模版的内容
     */
    public function setTplStr($msg_template)
    {
        $this->msg_template = $msg_template;
        return $this;
    }

    /**
     *
     * 渲染消息模版
     */
    public function render($data, $fields = [])
    {
        $fields =   array_intersect(array_keys($data), array_keys($fields));
        $pattern = '/\{\{(\w+)\}\}/'; // 匹配形如 {{variable}} 的模板变量

        return preg_replace_callback($pattern, function ($matches) use ($data, $fields) {
            $variable = $matches[1];
            if (in_array($variable, $fields)) {
                return isset($data[$variable]) ? $data[$variable] : '';
            }
        }, $this->msg_template);
    }



    // // 示例用法
    // $templateString = "Hello, {{name}}! You are {{age}} years old.";
    // $template = new Template($templateString);

    // $data = [
    // 	'name' => 'John',
    // 	'age' => 25
    // ];

    // echo $template->render($data);

}
