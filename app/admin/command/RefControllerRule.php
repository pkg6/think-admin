<?php
declare (strict_types=1);

namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class RefControllerRule extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('admin:ref-controller-rule')
            ->setDescription('通过反射class类的方式将数据添加到路由规则上');
    }

    protected function execute(Input $input, Output $output)
    {

    }

}
