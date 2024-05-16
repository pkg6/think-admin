<?php

namespace app\common\model;

use tp5er\think\auth\access\Authorizable;
use tp5er\think\auth\Authenticatable;
use tp5er\think\auth\contracts\Authenticatable as AuthenticatableContract;
use tp5er\think\auth\contracts\Authorizable as AuthorizableContract;
use tp5er\think\auth\sanctum\HasApiTokens;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable,HasApiTokens,Authorizable;

    /**
     * @var string
     */
    protected $table = "tp_admin";

    /**
     * @var string
     */
    protected $autoWriteTimestamp = "int";

    /**
     * 数据输出隐藏的属性
     * @var string[]
     */
    protected $hidden =[
        'password',
    ];

    /**
     * 通过获取器 对登录时间进行格式化
     * @param $value
     * @return false|string
     */
    public function getLogintimeAttr($value)
    {
        return date("Y-m-d H:i:s",$value);
    }
}
