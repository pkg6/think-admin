<?php

namespace app\common\model;

class Model extends \tp5er\think\Model
{

    /**
     * @var string
     */
    protected $autoWriteTimestamp = "int";

    public function status()
    {
        return [
            $this->radio('status', 1, "启用"),
            $this->radio('status', 0, "禁用"),
        ];
    }

    protected function radio($field, $value, $title)
    {
        try {
            $checked = $this->{$field} === $value;
        } catch (\Exception $exception) {
            $checked = false;
        }
        return [
            'field'   => $field,
            'value'   => $value,
            'title'   => $title,
            'checked' => $checked,
        ];
    }
}
