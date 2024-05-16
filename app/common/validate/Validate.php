<?php

namespace app\common\validate;

class Validate extends \think\Validate
{

    /**
     * @var array
     */
    protected $sceneAdd  = [];
    /**
     * @var array
     */
    protected $sceneEdit = [];


    public function __construct()
    {
        parent::__construct();
        $this->think_scene();
    }

    /**
     * @return void
     */
    public function think_scene()
    {
        $this->scene['add']  = $this->sceneAdd;
        $this->scene['edit'] = $this->sceneEdit;
    }
}
