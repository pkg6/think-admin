<?php

namespace app\admin\validate;


use app\common\validate\Validate;

class Admin extends Validate
{
    protected $rule = [
        "username" => "require",
        "password" => "require",
        "email"    => 'email',
        "mobile"   => 'require',
        "status"   => 'require',
        "captcha"  => "require|captcha",
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'password.require' => '密码不能为空',
        'captcha.require'  => '验证码不能为空',
        'captcha.captcha'  => '验证码有误',
    ];

    protected $sceneAdd  = ["username", "password", 'email', 'mobile', 'status'];
    protected $sceneEdit = ['email', 'mobile', 'status'];
    protected $scene     = [
        "captcha" => ["username", "password", "captcha"],
        "login"   => ["username", "password"],
    ];
}
