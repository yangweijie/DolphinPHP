<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\user\admin;

use app\common\controller\Common;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\admin\model\Menu as MenuModel;

/**
 * 用户公开控制器，不经过权限认证
 * @package app\user\admin
 */
class Publics extends Common
{
    /**
     * 用户登录
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function signin()
    {
        if ($this->request->isPost()) {
            // 获取post数据
            $data = $this->request->post();
            $rememberme = isset($data['remember-me']) ? true : false;

            // 登录钩子
            $hook_result = hook('signin', $data);
            if (!empty($hook_result) && true !== $hook_result[0]) {
                return $this->error($hook_result[0]);
            }

            // 验证数据
            $result = $this->validate($data, 'app\user\validate\User.signin');
            if(true !== $result){
                // 验证失败 输出错误信息
                return $this->error($result);
            }

            // 验证码
            if (config('captcha_signin')) {
                $captcha = $this->request->post('captcha', '');
                $captcha == '' && $this->error('请输入验证码');
                if(!captcha_check($captcha, '')){
                    //验证失败
                    return $this->error('验证码错误或失效');
                };
            }

            // 登录
            $UserModel = new UserModel;
            $uid = $UserModel->login($data['username'], $data['password'], $rememberme);
            if ($uid) {
                // 记录行为
                action_log('user_signin', 'admin_user', $uid, $uid);
                return $this->jumpUrl();
            } else {
                return $this->error($UserModel->getError());
            }
        } else {
            $hook_result = hook('signin_sso');
            if (!empty($hook_result) && true !== $hook_result[0]) {
                if (isset($hook_result[0]['url'])) {
                    return $this->redirect($hook_result[0]['url']);
                }
                if (isset($hook_result[0]['error'])) {
                    return $this->error($hook_result[0]['error']);
                }
            }

            if (is_signin()) {
                return $this->jumpUrl();
            } else {
                return $this->fetch();
            }
        }
    }

    /**
     * 跳转到第一个有权限访问的url
     * @author 蔡伟明 <314013107@qq.com>
     * @return mixed|string
     */
    private function jumpUrl()
    {
        if (session('user_auth.role') == 1) {
            return $this->success('登录成功', url('admin/index/index'));
        }

        $default_module = RoleModel::where('id', session('user_auth.role'))->value('default_module');
        $menu = MenuModel::get($default_module);
        if (!$menu) {
            return $this->error('当前角色未指定默认跳转模块！');
        }

        if ($menu['url_type'] == 'link') {
            return $this->success('登录成功', $menu['url_value']);
        }

        $menu_url = explode('/', $menu['url_value']);
        role_auth();

        $menus = MenuModel::getSidebarMenu($default_module, $menu['module'], $menu_url[1]);
        $url   = '';
        foreach ($menus as $key => $menu) {
            if (!empty($menu['url_value'])) {
                $url = $menu['url_value'];
                break;
            }
            if (!empty($menu['child'])) {
                $url = $menu['child'][0]['url_value'];
                break;
            }
        }

        if ($url == '') {
            return $this->error('权限不足');
        } else {
            return $this->success('登录成功', $url);
        }
    }

    /**
     * 退出登录
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function signout()
    {
        $hook_result = hook('signout_sso');
        if (!empty($hook_result) && true !== $hook_result[0]) {
            if (isset($hook_result[0]['url'])) {
                $this->redirect($hook_result[0]['url']);
            }
            if (isset($hook_result[0]['error'])) {
                $this->error($hook_result[0]['error']);
            }
        }

        session(null);
        cookie('uid', null);
        cookie('signin_token', null);

        $this->redirect('/user/publics/signin');
    }
}
