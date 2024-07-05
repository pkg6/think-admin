<?php

namespace app\admin;

use app\admin\command\RefControllerRule;

class Service extends \think\Service
{
    public function boot(): void
    {
        $this->commands([
            RefControllerRule::class
        ]);
    }
}
