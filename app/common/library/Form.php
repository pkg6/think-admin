<?php

namespace app\common\library;

use think\helper\Arr;
use tp5er\think\traits\think\Instance;

class Form
{
    use Instance;

    /**
     * 跳过的填充value值的类型
     *
     * @var array
     */
    protected $skipValueTypes = array('file', 'password', 'checkbox', 'radio');
    /**
     * 已创建的标签名称
     *
     * @var array
     */
    protected $labels = [];
    /**
     * 转义HTML
     * @var boolean
     */
    protected $escapeHtml = true;

    /**
     * 生成Label标签
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function label($name, $value = null, $options = [])
    {
        $this->labels[] = $name;

        $options = $this->attributes($options);
        $value   = $this->escape($this->formatLabel($name, $value));

        return '<label for="' . $name . '"' . $options . '>' . $value . '</label>';
    }

    /**
     * 获取转义编码后的值
     * @param string $value
     * @return string
     */
    public function escape($value)
    {
        if (!$this->escapeHtml) {
            return $value;
        }
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Format the label value.
     *
     * @param string $name
     * @param string|null $value
     * @return string
     */
    protected function formatLabel($name, $value)
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    /**
     * 生成复选按钮
     *
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return string
     */
    public function checkbox($name, $value = 1, $checked = null, $options = [])
    {
        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input('checkbox', $name, $value, $options);
    }

    /**
     * 生成普通文本框
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function text($name, $value = null, $options = [])
    {
        return $this->input('text', $name, $value, $options);
    }

    /**
     * 生成密码文本框
     *
     * @param string $name
     * @param array $options
     * @return string
     */
    public function password($name, $options = [])
    {
        return $this->input('password', $name, '', $options);
    }

    /**
     * 生成隐藏文本框
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function hidden($name, $value = null, $options = [])
    {
        return $this->input('hidden', $name, $value, $options);
    }

    /**
     * 生成Email文本框
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function email($name, $value = null, $options = [])
    {
        return $this->input('email', $name, $value, $options);
    }

    /**
     * 生成URL文本框
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function url($name, $value = null, $options = [])
    {
        return $this->input('url', $name, $value, $options);
    }

    /**
     * 生成上传文件组件
     *
     * @param string $name
     * @param array $options
     * @return string
     */
    public function file($name, $options = [])
    {
        return $this->input('file', $name, null, $options);
    }

    /**
     * 生成单选按钮
     *
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return string
     */
    public function radio($name, $value = null, $checked = false, $options = [])
    {
        if (is_null($value)) {
            $value = $name;
        }

        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input('radio', $name, $value, $options);
    }

    /**
     * 生成文本框(按类型)
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function input($type, $name, $value = null, $options = [])
    {
        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $id = $this->getIdAttribute($name, $options);

        if (!in_array($type, $this->skipValueTypes)) {
            $value = $this->getValueAttribute($name, $value);
        }
        $merge   = compact('type', 'value', 'id');
        $options = array_merge($options, $merge);
        return '<input' . $this->attributes($options) . '>';
    }

    /**
     * 获取ID属性值
     *
     * @param string $name
     * @param array $attributes
     * @return string
     */
    public function getIdAttribute($name, $attributes)
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (in_array($name, $this->labels)) {
            return $name;
        }
    }

    /**
     * 数组转换成一个HTML属性字符串。
     *
     * @param array $attributes
     * @return string
     */
    public function attributes($attributes)
    {
        $html = [];
        // 假设我们的keys 和 value 是相同的,
        // 拿HTML“required”属性来说,假设是['required']数组,
        // 会已 required="required" 拼接起来,而不是用数字keys去拼接
        foreach ((array)$attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);
            if (!is_null($element)) {
                $html[] = $element;
            }
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * 拼接成一个属性。
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (!is_null($value)) {
            if (is_array($value) || stripos($value, '"') !== false) {
                $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                return $key . "='" . $value . "'";
            } else {
                return $key . '="' . $value . '"';
            }
        }
    }

    /**
     * 获取Value属性值
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public function getValueAttribute($name, $value = null)
    {
        if (is_null($name)) {
            return $value;
        }

        if (!is_null($value)) {
            return $value;
        }
    }

    /**
     * 生成多行文本框
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $value = null, $options = [])
    {
        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $options       = $this->setTextAreaSize($options);
        $options['id'] = $this->getIdAttribute($name, $options);
        $value         = (string)$this->getValueAttribute($name, $value);

        unset($options['size']);

        $options['class'] = isset($options['class']) ? $options['class'] . (stripos($options['class'], 'form-control') !== false ? '' : ' form-control') : 'form-control';
        $options          = $this->attributes($options);

        return '<textarea' . $options . '>' . $this->escape($value) . '</textarea>';
    }

    /**
     * 设置默认的文本框行列数
     *
     * @param array $options
     * @return array
     */
    protected function setTextAreaSize($options)
    {
        if (isset($options['size'])) {
            return $this->setQuickTextAreaSize($options);
        }

        $cols = Arr::get($options, 'cols', 50);
        $rows = Arr::get($options, 'rows', 5);
        return array_merge($options, compact('cols', 'rows'));
    }

    /**
     * 根据size设置行数和列数
     *
     * @param array $options
     * @return array
     */
    protected function setQuickTextAreaSize($options)
    {
        $segments = explode('x', $options['size']);
        return array_merge($options, array('cols' => $segments[0], 'rows' => $segments[1]));
    }

    /**
     * 生成下拉列表框
     *
     * @param string $name
     * @param array $list
     * @param mixed $selected
     * @param array $options
     * @return string
     */
    public function select($name, $list = [], $selected = null, $options = [])
    {
        $selected = $this->getValueAttribute($name, $selected);

        $options['id'] = $this->getIdAttribute($name, $options);

        if (!isset($options['name'])) {
            $options['name'] = $name;
        }

        $html = [];
        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption($display, $value, $selected);
        }
        $options['class'] = isset($options['class']) ? $options['class'] . (stripos($options['class'], 'form-control') !== false ? '' : ' form-control') : 'form-control';

        $options = $this->attributes($options);
        $list    = implode('', $html);

        return "<select{$options}>{$list}</select>";
    }

    /**
     * 根据传递的值生成option
     *
     * @param string $display
     * @param string $value
     * @param string $selected
     * @return string
     */
    public function getSelectOption($display, $value, $selected)
    {
        if (is_array($display)) {
            return $this->optionGroup($display, $value, $selected);
        }

        return $this->option($display, $value, $selected);
    }

    /**
     * 生成optionGroup
     *
     * @param array $list
     * @param string $label
     * @param string $selected
     * @return string
     */
    protected function optionGroup($list, $label, $selected)
    {
        $html = [];

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected);
        }

        return '<optgroup label="' . $this->escape($label) . '">' . implode('', $html) . '</optgroup>';
    }

    /**
     * 生成option选项
     *
     * @param string $display
     * @param string $value
     * @param string $selected
     * @return string
     */
    protected function option($display, $value, $selected)
    {
        $selected = $this->getSelectedValue($value, $selected);

        $options = array('value' => $this->escape($value), 'selected' => $selected);

        return '<option' . $this->attributes($options) . '>' . $this->escape($display) . '</option>';
    }

    /**
     * 检测value是否选中
     *
     * @param string $value
     * @param string $selected
     * @return string
     */
    protected function getSelectedValue($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string)$value == (string)$selected) ? 'selected' : null;
    }

    /**
     * 生成一个按钮
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function button($value = null, $options = [])
    {
        if (!array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return '<button' . $this->attributes($options) . '>' . $value . '</button>';
    }

}
