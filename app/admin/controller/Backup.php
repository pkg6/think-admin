<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use tp5er\Backup\controller\Controller;

class Backup extends Backend
{
    use Controller;

    /**
     * @var string
     */
    protected $prefix = "/admin/backup";


    /**
     * @return mixed
     * @throws \Exception
     */
    protected function vfetch()
    {
        $this->assign("routes", array_merge($this->apiRoutes($this->prefix), [
            'view_backup' => $this->prefix . '/index',
            'view_import' => $this->prefix . '/import',
        ]));
        return $this->fetch();
    }

    /**
     * 备份视图渲染.
     *
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        return $this->vfetch();
    }

    /**
     * 还原视图渲染.
     *
     * @return string
     */
    public function import()
    {
        return $this->vfetch();
    }
}
