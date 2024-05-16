<?php

namespace app\admin\validate;

use app\common\validate\Validate;

class Rule extends Validate
{
    /**
     * @var string[]
     */
    protected $sceneAdd = ["name", "icon", 'url','remark','type','status','pid'];

    /**
     * @var string[]
     */
    protected $rule = [
        "name"   => "require",
        "icon"   => "require",
        "url"    => "require",
        "remark" => "require",
        "type"   => "require",
        "status" => "require",
        "pid"    => 'egt:0',
    ];
}
