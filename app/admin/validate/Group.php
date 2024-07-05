<?php

namespace app\admin\validate;


use app\common\validate\Validate;

class Group extends Validate
{

    /**
     * @var string[]
     */
    protected $sceneAdd = ["name", "status", 'pid'];

    /**
     * @var string[]
     */
    protected $rule = [
        "name"   => "require",
        "status" => "require",
        "pid"    => 'egt:0',
    ];
}
