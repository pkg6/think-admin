<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Admin;

class User extends Backend
{

    /**
     * @var string
     */
    protected $model = Admin::class;
    /**
     * @var bool
     */
    protected $modelValidate = true;


    /**
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        return parent::modelList();
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function add()
    {
        $this->componentsFromRadio('status');
        return parent::modelAdd();
    }

    /**
     * @param $ids
     * @return mixed|void
     * @throws \Exception
     */
    public function edit($ids = null)
    {
        $this->componentsFromRadio('status');
        return parent::modelUpdateByID($ids);
    }

    /**
     * @return void
     */
    public function delete()
    {
        $ids = $this->request->request('ids');
        $ret = $this->auth->delete($ids);
        if ($ret === true) {
            $this->success("删除成功");
        } else {
            $this->error($this->auth->getError());
        }
    }
}
