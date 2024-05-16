<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\response\Json;
use think\response\Xml;
use think\View;


/**
 * Class Group
 * @author zhiqiang
 * @package app\admin\controller
 * @
 */
class Group extends Backend
{
    /**
     * @var string
     */
    protected $model = \app\common\model\Group::class;
    /**
     * @var bool
     */
    protected $modelValidate = true;

    /**
     * layui treeTable 联动
     * @return Json|View
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $pid = $this->request->request('pid', 0);
        return parent::modelTreeList($pid);
    }


    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function add()
    {
        $this->componentsFromRadio('status');
        $this->modelDropDownSelectionTree();
        return parent::modelAdd();
    }

    /**
     * @param $ids
     * @return mixed|void
     * @throws \Exception
     */
    public function edit($ids = null)
    {
        $this->componentsFromRadio('status', $ids);
        $this->modelDropDownSelectionTree($ids);
        return parent::modelUpdateByID($ids);
    }

    /**
     * @return void
     * @throws \think\db\exception\PDOException
     */
    public function delete()
    {
        $ids = $this->request->request('ids', []);
        parent::modelDeleteByIds($ids, function ($group) {
            //判断是否存在上下关联
            $count = $this->model()->where($this->treePidField, $group->id)->count();
            if ($count > 0) {
                return false;
            }
            return true;
        });
    }
}
