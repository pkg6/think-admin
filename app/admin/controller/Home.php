<?php

namespace app\admin\controller;

use app\common\controller\Backend;

class Home extends Backend
{
    public function index()
    {
        return $this->fetch();
    }
}
