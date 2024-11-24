<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\common\controller;

use Kingbes\Jump\Jump;
use support\Cache;
use support\Response;
use support\View;
use taoser\exception\ValidateException;
use Webman\Http\Request;

/**
 * 项目公共控制器
 * @package app\common\controller
 */
class Common extends Jump
{
    public Request|\support\Request|null $request;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 初始化
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function initialize(): void
    {
//        var_dump(config('app'));
        // 后台公共模板
        View::assign('_admin_base_layout', config('app.admin_base_layout'));
        // 当前配色方案
        View::assign('system_color', config('app,system_color'));
        // 输出弹出层参数
        View::assign('_pop', request()->input('_pop', false));
        $this->request = request();
//        var_dump($this->request->header());
    }

    /**
     * 获取筛选条件
     * @author 蔡伟明 <314013107@qq.com>
     * @alter 小乌 <82950492@qq.com>
     * @return array
     */
    final protected function getMap(): array
    {
        $search_field     = input('param.search_field/s', '', 'trim');
        $keyword          = input('param.keyword/s', '', 'trim');
        $filter           = input('param._filter/s', '', 'trim');
        $filter_content   = input('param._filter_content/s', '', 'trim');
        $filter_time      = input('param._filter_time/s', '', 'trim');
        $filter_time_from = input('param._filter_time_from/s', '', 'trim');
        $filter_time_to   = input('param._filter_time_to/s', '', 'trim');
        $select_field     = input('param._select_field/s', '', 'trim');
        $select_value     = input('param._select_value/s', '', 'trim');
        $search_area      = input('param._s', '', 'trim');
        $search_area_op   = input('param._o', '', 'trim');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[] = [$search_field, 'like', "%$keyword%"];
        }

        // 下拉筛选
        if ($select_field != '') {
            $select_field = array_filter(explode('|', $select_field), 'strlen');
            $select_value = array_filter(explode('|', $select_value), 'strlen');
            foreach ($select_field as $key => $item) {
                if ($select_value[$key] != '_all') {
                    $map[] = [$item, '=', $select_value[$key]];
                }
            }
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[] = [$filter_time, 'between time', [$filter_time_from.' 00:00:00', $filter_time_to.' 23:59:59']];
        }

        // 表头筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                if (isset($filter_content[$key])) {
                    $map[] = [$item, 'in', $filter_content[$key]];
                }
            }
        }

        // 搜索区域
        if ($search_area != '') {
            $search_area = explode('|', $search_area);
            $search_area_op = explode('|', $search_area_op);
            foreach ($search_area as $key => $item) {
                list($field, $value) = explode('=', $item);
                $value = trim($value);
                $op    = explode('=', $search_area_op[$key]);
                if ($value != '') {
                    switch ($op[1]) {
                        case 'like':
                            $map[] = [$field, 'like', "%$value%"];
                            break;
                        case 'between time':
                        case 'not between time':
                            $value = explode(' - ', $value);
                            if ($value[0] == $value[1]) {
                                $value[0] = date('Y-m-d', strtotime($value[0])). ' 00:00:00';
                                $value[1] = date('Y-m-d', strtotime($value[1])). ' 23:59:59';
                            }
                            break;
                        default:
                            $map[] = [$field, $op[1], $value];
                    }
                }
            }
        }
        return $map;
    }

    /**
     * 获取字段排序
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     * @return string
     *@author 蔡伟明 <314013107@qq.com>
     */
    final protected function getOrder(string $extra_order = '', bool $before = false): string
    {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order. ' '. $by;
        }
        if ($before) {
            return $extra_order. ',' .$order. ' '. $by;
        } else {
            return $order. ' '. $by . ',' . $extra_order;
        }
    }

    /**
     * 渲染插件模板
     * @param string $template 模板文件名
     * @param string $suffix 模板后缀
     * @param array $vars 模板输出变量
     * @return mixed
     * @author 蔡伟明 <314013107@qq.com>
     */
    final protected function pluginView(string $template = '', string $suffix = '', array $vars = []): mixed
    {
        $plugin_name = input('param.plugin_name');

        if ($plugin_name != '') {
            $plugin = $plugin_name;
            $action = 'index';
        } else {
            $plugin = input('param._plugin');
            $action = input('param._action');
        }
        $suffix = $suffix == '' ? 'html' : $suffix;
        $template = $template == '' ? $action : $template;
        $template_path = config('plugin_path'). "{$plugin}/view/{$template}.{$suffix}";
        return view($template_path, $vars);
    }
    
    public function assign($key ,$value): void
    {
        View::assign($key, $value);
    }

    public function redirect($url, int $code = 302, array $header = []): Response
    {
        return redirect($url, $code, $header);
    }

    public function fetch($template = null, $vars = [], $config = []): Response
    {
        if($config){
            $think_view_config = Cache::get('view.options', []);
            $set = array_merge($think_view_config, $config);
            Cache::set('view.options', $set);
        }
        return view($template, $vars);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {

        if (class_exists($validate)) {
            $appname =   request()->app;
            $validate_class =    "app\\{$appname}\\validate\\{$validate}";
            $validate_class = class_exists($validate_class) ? $validate_class : $validate;
        } else {
            $validate_class = $validate;
        }
        try {
            validate($validate_class)->check($data);
            return true;
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            return $e->getMessage();
        }

//
//        $v->message($message);
//
//        // 是否批量验证
//        if ($batch || $this->batchValidate) {
//            $v->batch(true);
//        }
//
//        return $v->failException(true)->check($data);
    }
}
