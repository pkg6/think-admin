<?php

namespace app\common\library;

use app\common\model\Admin;
use Ramsey\Uuid\Uuid;
use think\App;
use think\facade\Db;
use think\helper\Arr;
use tp5er\think\auth\contracts\StatefulGuard;
use tp5er\think\traits\think\Instance;
use function auth;
use function hash_check;
use function think_admin_info_change;
use function think_admin_session;

class Auth
{
    use Instance;

    /**
     * @var App
     */
    protected $app;

    /**
     * 登录状态
     *
     * @var bool
     */
    protected $logined = false;
    /**
     * @var string
     */
    protected $_error = "";
    /**
     * @var StatefulGuard
     */
    protected $auth;

    /**
     * @var int[]
     */
    protected $user_status_can = [1];
    /**
     * @var string
     */
    protected $requestUri = '';

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app  = $app;
        $this->auth = auth()->guard('admin');

    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组.
     *
     * @param array $noNeedLogin 需要验证权限的数组
     *
     * @return bool
     */
    public function match($noNeedLogin)
    {
        $noNeedLogin = is_array($noNeedLogin) ? $noNeedLogin : explode(',', $noNeedLogin);
        if (!$noNeedLogin) {
            return false;
        }
        $noNeedLogin = array_map('strtolower', $noNeedLogin);
        // 是否存在
        if (in_array(strtolower($this->app->request->action()), $noNeedLogin) || in_array('*', $noNeedLogin)) {
            return true;
        }
        // 没找到匹配
        return false;
    }

    /**
     * 检测是否登录.
     *
     * @return bool
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isLogin()
    {
        if ($this->logined) {
            return true;
        }
        if (!$this->auth->check()) {
            return false;
        }
        $admin = think_admin_session();
        if (!$admin) {
            return false;
        }
        $my = Admin::get($this->auth->id());
        if (!$my) {
            return false;
        }
        //判断是否存在用户信息变更，存在就进行退出
        if (think_admin_info_change()) {
            $this->logout();
            return false;
        }
        //判断是否同一时间同一账号只能在一个地方登录
        if ($this->app->config->get("admin.login_unique", false)) {
            if ($my->session_id != $admin["session_id"]) {
                $this->logout();

                return false;
            }
        }
        if ($this->app->config->get("admin.loginip_check", false)) {
            //判断管理员IP是否变动
            if ($my->loginip != $admin["loginip"]) {
                $this->logout();

                return false;
            }
        }
        return true;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function check(string $path)
    {
        return true;
    }

    /**
     * 退出登录.
     *
     * @return bool
     */
    public function logout()
    {
        //重置登录状态
        $this->logined = false;

        $this->auth->logout();

        think_admin_session(null);

        return true;
    }

    /**
     * 管理员登录.
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $remember 是否记住密码
     *
     * @return  bool
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($username, $password, $remember = false): bool
    {
        $admin = Admin::newQuery()->where(['username' => $username])->find();
        if (!$admin) {
            $this->setError('Username is incorrect');

            return false;
        }
        //判断账号状态是否正常
        if (!in_array($admin->status, $this->user_status_can)) {
            $this->setError('Admin is forbidden');

            return false;
        }
        //密码失败次数限制
        if ($admin->loginfailure >= 10 && time() - $admin->update_time < 86400) {
            $this->setError('Please try again after 1 day');

            return false;
        }
        //检查密码是否正确
        if (!hash_check($password, $admin->password)) {
            $admin->loginfailure++;
            $admin->save();
            $this->setError('Password is incorrect');

            return false;
        }
        //保持会话有效时长，24小时
        $this->auth
            ->setRememberDuration(3600 * 24)
            ->login($admin, $remember);

        $admin->loginfailure = 0;
        $admin->logintime    = time();
        if (method_exists($admin, "createToken")) {
            $token                = $admin->createToken("session");
            $admin->session_id = $token->plainTextToken;
        } else {
            $admin->session_id = Uuid::uuid4()->toString();
        }
        $admin->loginip = request()->ip();
        $admin->save();
        think_admin_session($admin->toArray());
        return true;
    }

    /**
     * 批量删除用户
     * @param $ids
     * @return bool
     */
    public function delete($ids)
    {
        $ids = Arr::wrap($ids);
        Db::startTrans();
        try {
            foreach ($ids as $id) {
                //判断ID 是否等于当前的登录的ID 如果是就进行退出
                if ($this->id == $id) {
                    $this->logout();
                }
                //删除用户
                $user = Admin::get($id);
                $user->delete();
                //删除token
                if (method_exists($user, 'tokens')) {
                    $user->tokens()->delete();
                }
            }
            Db::commit();
            return true;
        } catch (\Exception $exception) {
            Db::rollback();
            $this->setError($exception->getMessage());
            return false;
        }
    }

    /**
     * 设置错误信息.
     *
     * @param string $error 错误信息
     *
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * 自动登录.
     *
     * @return bool
     */
    public function autologin()
    {
        return false;
    }

    /**
     * @param $name
     * @return mixed|void
     */
    public function __get($name)
    {
        return think_admin_session($name);
    }


}
