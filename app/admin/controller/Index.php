<?php
declare (strict_types=1);

namespace app\admin\controller;


use app\admin\validate\Admin;
use app\common\controller\Backend;
use app\common\library\Tree;

class Index extends Backend
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout      = '';

    public function index()
    {
        $list = \app\common\model\Rule::all()->toArray();
        $menu = Tree::instance($list)->htmlTreeMenuLi(0);
        $this->assign('menu', $menu);
        return $this->fetch();
    }

    /**
     * 管理员的登录.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function login()
    {
        $login_captcha = $this->app->config->get("admin.login_captcha", false);
        $url           = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success("You've logged in, do not login again", $url);
        }
        if ($this->request->isPost()) {

            $err_data = ['token' => $this->request->buildToken()];

            $data     = $this->request->request();
            $validate = new Admin();

            $scene = "login";
            if ($login_captcha) {
                $scene = "captcha";
            }
            if (!$validate->scene($scene)->check($data)) {
                $this->error($validate->getError(), $url, $err_data);
            }

            $remember = false;
            if (isset($data['remember'])) {
                $remember = true;
            }
            $result = $this->auth->login($data['username'], $data['password'], $remember);

            if ($result === true) {
                $this->success('login success', $url);
            } else {
                $msg = $this->auth->getError();
                $this->error($msg, $url, $err_data);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->app->session->delete("referer");
            $this->redirect($url);
        }

        $this->assign("login_captcha", $login_captcha);
        $this->assign("title", 'Login');

        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success('Logout successful', 'index/login');
    }
}
