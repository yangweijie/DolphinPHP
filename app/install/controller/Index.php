<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\install\controller;

use Kingbes\Jump\Jump;
use support\View;
use think\facade\Db;
include_once __DIR__.'/../common.php';

define('INSTALL_APP_PATH', realpath('./') . '/');

/**
 * 安装控制器
 * @package app\install\controller
 */
class Index extends Jump
{

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 获取入口目录
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function initialize(): void
    {
        View::assign('static_dir', '/static/');
    }

    /**
     * 安装首页
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function index()
    {
        if (is_file(app_path() . 'database.php')) {
            // 已经安装过了 执行更新程序
            session(['reinstall'=>true]);
            View::assign('next', '重新安装');
        } else {
            session(['reinstall'=> false]);
            View::assign('next', '下一步');
        }

        session(['step'=> 1]);
        session(['error'=> false]);
        return view();
    }

    /**
     * 步骤二，检查环境
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function step2()
    {
        if (session('step') != 1 && session('step') != 3) redirect('/install/index/index');
        if(session('reinstall')){
            session(['step'=> 2]);
            return redirect('/install/index/step4');
        }else{
            session(['error'=> false]);

            // 环境检测
            $env = check_env();

            // 目录文件读写检测
            $dirfile = check_dirfile();
            View::assign('dirfile', $dirfile);

            // 函数检测
            $func = check_func();

            session(['step'=> 2]);

            View::assign('env', $env);
            View::assign('func', $func);

            return view();
        }
    }

    /**
     * 步骤三，设置数据库连接
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function step3()
    {
        // 检查上一步是否通过
        if (request()->isAjax()) {
            if (session('error')) {
                return $this->error('环境检测没有通过，请调整环境后重试！');
            } else {
                return $this->success('恭喜您环境检测通过', '/install/index/step3');
            }
        }
        if (session('step') != 2) redirect('/install/index/index');
        session(['error'=> false]);
        session(['step'=> 3]);
        return view();
    }

    /**
     * 步骤四，创建数据库
     * @param null $db 数据库配置信息
     * @param int $cover 是否覆盖已存在数据库
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function step4($db = null, $cover = 0)
    {
        // 检查上一步是否通过
        if (request()->isPost()) {
            // 检测数据库配置
            if(!is_array($db) || empty($db['type'])
                || empty($db['hostname'])
                || empty($db['database'])
                || empty($db['username'])
                || empty($db['prefix'])){
                return $this->error('请填写完整的数据库配置');
            }

            // 缓存数据库配置
            session(['db_config'=>$db]);

            // 防止不存在的数据库导致连接数据库失败
            $db_name = $db['database'];
            unset($db['database']);

            // 创建数据库连接
            $db_instance = Db::connect($db);

            // 检测数据库连接
            try{
                $db_instance->execute('select version()');
            }catch(\Exception $e){
                return $this->error('数据库连接失败，请检查数据库配置！');
            }

            // 用户选择不覆盖情况下检测是否已存在数据库
            if (!$cover) {
                // 检测是否已存在数据库
                $result = $db_instance->execute('SELECT * FROM information_schema.schemata WHERE schema_name="'.$db_name.'"');
                if ($result) {
                    $this->error('该数据库已存在，请更换名称！如需覆盖，请选中覆盖按钮！');
                }
            }

            // 创建数据库
            $sql = "CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8";
            $db_instance->execute($sql) || $this->error($db_instance->getError());

            // 跳转到数据库安装页面
            return $this->success('参数正确开始安装', '/install/index/step4');
        } else {
            if (session('step') != 3 && !session('reinstall')) {
                redirect('/install/index/index');
            }

            session(['step'=>4]);
            return view();
        }
    }

    /**
     * 完成安装
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed
     */
    public function complete()
    {
        if (session('step') != 4) {
            return $this->error('请按步骤安装系统', '/install/index/index');
        }

        if (session('error')) {
            return $this->error('安装出错，请重新安装！', '/install/index/index');
        } else {
            // 写入安装锁定文件(只能在最后一步写入锁定文件，因为锁定文件写入后安装模块将无法访问)
            file_put_contents('../data/install.lock', 'lock');
            session('step', null);
            session('error', null);
            session('reinstall', null);
            return view();
        }
    }
}