<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\common\event;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;
use think\facade\Config as FacadeConfig;
use think\facade\Env;
use think\facade\Request;
use think\facade\App;


/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\listener
 * @author CaiWeiMing <314013107@qq.com>
 */
class AdminAuth
{

    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @return void
     */
    public function handle()
    {

        $oldpath = 	explode('.', request()->oldpath);

        // 判断是否登录
        if (session('user_auth') != [] && !in_array($oldpath[0], ['user/publics/signin', 'admin/index/index'])) {
            // 未登录
            // die;
            redirect((string)url('user/publics/signin'))->send() or die;
        }
    }
}
