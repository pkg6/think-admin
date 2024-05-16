<?php

namespace app\common\controller;

use app\common\controller\traits\ModelTrait;
use app\common\library\Auth;
use tp5er\think\Controller;
use function check_ip_allowed;
use function url;

class Backend extends Controller
{
    use ModelTrait;

    /**
     * 布局模板
     *
     * @var string
     */
    protected $layout = 'default';

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * 模型对象
     * @var \think\Model|string
     */
    protected $model = null;

    /**
     * 无需登录的方法,同时也就不需要鉴权了.
     *
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录.
     *
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = false;

    /**
     * 前台提交过来,需要排除的字段数据
     */
    protected $excludeFields;
    /**
     * 数据限制开启时自动填充限制字段值
     */
    protected $dataLimitFieldAutoFill = true;
    /**
     * 数据限制字段
     */
    protected $dataLimitField = 'admin_id';

    /**
     * tree 字段判断
     * @var string
     */
    protected $treePidField = "pid";


    /**
     * 模型指定指定进行hash_make
     * @var \string[][]
     */
    protected $modelHashMakeField = [
        \app\common\model\Admin::class => ['password'],
    ];

    /**
     * 是否开启数据限制
     * 支持auth/personal
     * 表示按权限判断/仅限个人
     * 默认为禁用,若启用请务必保证表中存在admin_id字段
     */
    protected $dataLimit = false;


    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _initialize()
    {
        $path = $this->currentModule
            . '/' . loader_parse_name($this->currentController)
            . '/' .loader_parse_name($this->currentAction);
        $this->auth = Auth::instance($this->app);
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        //检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->app->event->trigger('admin_nologin',$this);
                $url = $this->app->session->get('referer');
                $url = $url ? $url : $this->request->url();
                if (in_array($this->request->pathinfo(), ['/', 'index/index'])) {
                    $this->redirect('index/login');
                    exit;
                }
                $this->error('Please login first', url('index/login', ['url' => $url]));
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->app->event->trigger('admin_nopermission',$this);
                    $this->error('You have no permission', '');
                }
            }
        }
        // 检测IP是否允许
        check_ip_allowed();
        // 如果有使用模板布局
        if ($this->layout) {
            $this->engine->layout('layout/' . $this->layout);
        }
        $this->cookie->set("think_token", $this->request->buildToken());
        $this->assign('admin', think_admin_session());
    }
}
