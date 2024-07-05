<?php

use app\common\model\Admin;
return [
    //静态文件存放路径
    'assets_path'   => '/admin/',

    //登录验证
    'login_captcha' => false,

    //动态配置auth
    'auth'          => [
        'name'  => 'tp_admin',
        'model' => Admin::class,
    ],
    //判断是否同一时间同一账号只能在一个地方登录
    'login_unique'  => false,
    //判断管理员IP是否变动
    'loginip_check' => false,
];
