<?php

namespace app\common\controller\traits;

use app\common\library\Form;
use app\common\library\Tree;
use Exception;
use InvalidArgumentException;
use think\db\exception\PDOException;
use think\exception\ValidateException;
use think\facade\Db;
use think\helper\Arr;
use think\Model;
use think\response\Json;
use think\Validate;
use think\View;
use function hash_make;
use function url;

trait ModelTrait
{
    /**
     * 排除前台提交过来的字段
     * @param $params
     * @return array
     */
    protected function preExcludeFields($params)
    {
        if (is_array($this->excludeFields)) {
            foreach ($this->excludeFields as $field) {
                if (array_key_exists($field, $params)) {
                    unset($params[$field]);
                }
            }
        } else if (array_key_exists($this->excludeFields, $params)) {
            unset($params[$this->excludeFields]);
        }
        return $params;
    }

    /**
     * 组装请求参数
     * @return array
     */
    protected function buildSaveRequest()
    {
        //获取请求参数
        $params = $this->request->request();
        if (empty($params)) {
            throw new InvalidArgumentException("Missing %s request parameter", $this->request->method());
        }
        //排除请求参数
        $params = $this->preExcludeFields($params);
        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }
        return $params;
    }

    /**
     * 获取模型
     * @return Model
     */
    protected function model()
    {
        if (class_exists($this->model)) {
            return new $this->model;
        }
        return $this->model;
    }


    /**
     *  模型验证
     * @param string $modelClass
     * @param array $params
     * @param string $scene
     * @return void
     */
    protected function modelValidate($modelClass, $params, $scene = "")
    {
        $validate_class = str_replace("\\model\\", "\\validate\\", $modelClass);
        /*** @var Validate $validate */
        $validate = new $validate_class;
        if ($validate->hasScene($scene)) {
            $validate->scene($scene);
        }
        $validate->failException();
        $validate->check($params);
    }


    /**
     * 单个模型列表数据
     * @return mixed|void
     * @throws \think\db\exception\DbException
     */
    protected function modelList()
    {
        if (false === $this->request->isAjax()) {
            return $this->fetch();
        }

//        var_dump(request()->param());

        $list = $this->model()
            ->paginate(20);
        $this->success("success", "", $list);
    }


    /**
     * 上级ID 下拉选择
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function modelDropDownSelectionTree($ids = "")
    {
        $data        = $this->model()->select()->toArray();
        $selectedids = "";
        if ($ids != "") {
            $item        = $this->model()->find($ids);
            $selectedids = $item->pid;
        }
        $data = Tree::instance($data)->htmlSelectOption(0, $selectedids);
        $this->assign('drop_down_selection', $data);
        return $data;
    }

    /**
     *
     * @param $modelMethod
     * @param $ids
     * @param $title
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function componentsFromRadio($modelMethod, $ids = '', $title = "状态")
    {
        if ($ids != "") {
            $model = $this->model()->find($ids);
        } else {
            $model = $this->model();
        }
        $radioss = "";
        if (method_exists($model, $modelMethod)) {
            $radio_list = [];
            $radios     = $model->$modelMethod();
            foreach ($radios as $radio) {
                $radio_list[] = Form::instance()->radio(
                    $radio['field'],
                    $radio['value'],
                    $radio['checked'],
                    [
                        'title' => $radio['title']
                    ]
                );
            }
            $radioss = implode('', $radio_list);
        }
        $form_radio = ['title' => $title, 'radios' => $radioss];
        $this->assign('form_radio_' . $modelMethod, $form_radio);
        return $form_radio;
    }

    /**
     * layui treeTable 联动
     * @param int $pid
     * @return Json|View
     * @throws \think\db\exception\DbException
     */
    protected function modelTreeList($pid = 0)
    {
        if ($this->request->isGet()) {
            return $this->fetch();
        }
        $list = $this->model()
            ->where($this->treePidField, $pid)
            ->paginate(20);

        $list->getCollection()->map(function ($item) {
            $count = $this->model()->where([$this->treePidField => $item->id])->count();
            if ($count > 0) {
                $item->isParent = true;
            }
        });
        $this->success("success", "", $list);
    }

    /**
     * 模型添加参数
     * @return mixed|void
     * @throws Exception
     */
    protected function modelAdd()
    {
        $form_action = url(sprintf("admin/%s/%s", $this->currentController, $this->currentAction))->build();
        $this->assign('form_action', $form_action);
        if ($this->request->isGet()) {
            return $this->fetch();
        }
        $result = false;
        try {
            $params = $this->buildSaveRequest();
            $result = $this->modelSave($this->model(), $params, "add");
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if ($result === false) {
            $this->error('No rows were inserted');
        }
        $this->success("add success");
    }

    /**
     * 单独模型进行删除
     * @param array|string $ids
     * @param null $callback
     * @return void
     */
    protected function modelDeleteByIds($ids, $callback = null)
    {
        Db::startTrans();
        try {
            foreach (Arr::wrap($ids) as $id) {
                $model = $this->model()->find($id);
                if (!is_null($callback) && is_callable($callback)) {
                    if (!$callback($model)) {
                        throw new \RuntimeException("Callback does not allow deletion");
                    }
                }
                $model->delete();
            }
            Db::commit();
            $message = true;
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $message = $e->getMessage();
        }
        if ($message === true) {
            $this->success("delete success");
        } else {
            $this->error($message);
        }
    }

    /**
     * 模型修改数据
     * @param $ids
     * @return mixed|void
     * @throws Exception
     */
    protected function modelUpdateByID($ids = null)
    {
        $form_action = url(
            sprintf("admin/%s/%s", $this->currentController, $this->currentAction),
            ['ids' => $ids]
        )->build();
        $this->assign('form_action', $form_action);

        $row = $this->model()->get($ids);
        if (!$row) {
            $this->error('No Results were found');
        }
        if ($this->request->isGet()) {
            $this->assign('row', $row);
            return $this->fetch();
        }
        $result = false;
        try {
            $params = $this->buildSaveRequest();
            $result = $this->modelSave($row, $params, "edit");
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if ($result === false) {
            $this->error('No rows were updated');
        }
        $this->success("update success");
    }

    /**
     * 模型save数据
     * @param Model $model
     * @param array $params
     * @param string $scene
     * @return bool
     * @throws PDOException
     */
    protected function modelSave(Model $model, $params = [], $scene = "")
    {
        Db::startTrans();
        try {
            $modelClass = get_class($model);
            //验证数据
            if ($this->modelValidate) {
                $this->modelValidate($modelClass, $params, $scene);
            }
            $hashMakeFields = $this->modelHashMakeField[$modelClass] ?? [];
            //插入数据
            foreach ($params as $field => $val) {
                if (in_array($field, $hashMakeFields)) {
                    if ($val != "") {
                        $model->{$field} = hash_make($val);
                    }
                } else {
                    $model->{$field} = $val;
                }
            }
            Db::commit();
            return $model->save();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            throw $e;
        }
    }


}
