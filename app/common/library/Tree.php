<?php

namespace app\common\library;

class Tree
{
    /**
     * @var
     */
    protected static $instance;
    /**
     * 生成树型结构所需要的2维数组
     * @var array
     */
    protected $data = [];
    /**
     * 生成树型结构所需修饰符号，可以换成图片
     * @var array
     */
    protected $icon = array('│', '├', '└');
    /**
     * @var string
     */
    protected $nbsp = "&nbsp;";

    /**
     * @var string
     */
    protected $idname = 'id';
    /**
     * @var string
     */
    protected $pidname = 'pid';

    /**
     * @var string
     */
    protected $html_temp_option = "<option value='@id' @selected @disabled>@spacer@name</option>";

    /**
     * 初始化方法
     * @param array $data
     * @param string $idname
     * @param string $pidname 父字段名称
     * @param string $nbsp 空格占位符
     */
    public function __construct($data = [], $idname = "id", $pidname = 'pid', $nbsp = '&nbsp;')
    {
        $this->data    = $data;
        $this->idname  = $idname;
        $this->pidname = $pidname;
        $this->nbsp    = $nbsp;
    }

    /**
     * 初始化
     * @access public
     * @param array $data
     * @param string $idname
     * @param string $pidname
     * @param string $nbsp
     * @return Tree
     */
    public static function instance($data = [], $idname = "id", $pidname = 'pid', $nbsp = '&nbsp;')
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($data, $idname, $pidname, $nbsp);
        }
        return self::$instance;
    }


    /**
     * 得到子级数组
     * @param int
     * @return array
     */
    public function getChild($myid)
    {
        $newarr = [];
        foreach ($this->data as $value) {
            if (!isset($value[$this->idname])) {
                continue;
            }
            if ($value[$this->pidname] == $myid) {
                $newarr[$value[$this->idname]] = $value;
            }
        }
        return $newarr;
    }

    /**
     * 读取指定节点的所有孩子节点
     * @param int $myid 节点ID
     * @param boolean $withself 是否包含自身
     * @return array
     */
    public function getChildren($myid, $withself = false)
    {
        $newarr = [];
        foreach ($this->data as $value) {
            if (!isset($value[$this->idname])) {
                continue;
            }
            if ((string)$value[$this->pidname] == (string)$myid) {
                $newarr[] = $value;
                $newarr   = array_merge($newarr, $this->getChildren($value[$this->idname]));
            } elseif ($withself && (string)$value[$this->idname] == (string)$myid) {
                $newarr[] = $value;
            }
        }
        return $newarr;
    }

    /**
     * 读取指定节点的所有孩子节点ID
     * @param int $myid 节点ID
     * @param boolean $withself 是否包含自身
     * @return array
     */
    public function getChildrenIds($myid, $withself = false)
    {
        $children_ids = [];
        foreach ($this->getChildren($myid, $withself) as $v) {
            $children_ids[] = $v[$this->idname];
        }
        return $children_ids;
    }

    /**
     * 得到当前位置父辈数组
     * @param int
     * @return array
     */
    public function getParent($myid)
    {
        $pid  = 0;
        $data = [];
        foreach ($this->data as $value) {
            if (!isset($value[$this->idname])) {
                continue;
            }
            if ($value[$this->idname] == $myid) {
                $pid = $value[$this->pidname];
                break;
            }
        }
        if ($pid) {
            foreach ($this->data as $value) {
                if ($value[$this->idname] == $pid) {
                    $data[] = $value;
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * 得到当前位置所有父辈数组
     * @param int
     * @param bool $withself 是否包含自己
     * @return array
     */
    public function getParents($myid, $withself = false)
    {
        $pid  = 0;
        $data = [];
        foreach ($this->data as $value) {
            if (!isset($value['id'])) {
                continue;
            }
            if ($value['id'] == $myid) {
                if ($withself) {
                    $data[] = $value;
                }
                $pid = $value[$this->pidname];
                break;
            }
        }
        if ($pid) {
            $data = array_merge($this->getParents($pid, true), $data);
        }
        return $data;
    }

    /**
     * 读取指定节点所有父类节点ID
     * @param int $myid
     * @param boolean $withself
     * @return array
     */
    public function getParentsIds($myid, $withself = false)
    {
        $ids = [];
        foreach ($this->getParents($myid, $withself) as $v) {
            $ids[] = $v[$this->idname];
        }
        return $ids;
    }

    /**
     *
     * 获取树状数组
     * @param string $myid 要查询的ID
     * @param string $itemprefix 前缀
     * @return array
     */
    public function getTreeArray($myid, $itemprefix = '')
    {
        $childs = $this->getChild($myid);
        $n      = 0;
        $data   = [];
        $number = 1;
        if ($childs) {
            $total = count($childs);
            foreach ($childs as $id => $value) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                    $k = $itemprefix ? $this->nbsp : '';
                } else {
                    $j .= $this->icon[1];
                    $k = $itemprefix ? $this->icon[0] : '';
                }
                $spacer                = $itemprefix ? $itemprefix . $j : '';
                $value['spacer']       = $spacer;
                $data[$n]              = $value;
                $data[$n]['childlist'] = $this->getTreeArray($id, $itemprefix . $k . $this->nbsp);
                $n++;
                $number++;
            }
        }
        return $data;
    }


    /**
     * @param $myid
     * @param string $nodeClass
     * @param string $childClass
     * @return string
     */
    public function htmlTreeMenuLi($myid, $nodeClass = 'layui-nav-item', $childClass = 'layui-nav-child')
    {
        $treeData = $this->getTreeArray($myid);
        return Layui::menuLi($treeData, $nodeClass, $childClass);
    }


    /**
     * 树型结构Option
     * @param int $myid 表示获得这个ID下的所有子级
     * @param mixed $selectedids 被选中的ID，比如在做树型下拉框的时候需要用到
     * @param mixed $disabledids 被禁用的ID，比如在做树型下拉框的时候需要用到
     * @param string $itemprefix 每一项前缀
     * @param string $toptpl 顶级栏目的模板
     * @return string
     */
    public function htmlSelectOption($myid, $selectedids = '', $disabledids = '', $itemprefix = '', $toptpl = '')
    {
        $html   = '';
        $number = 1;
        $childs = $this->getChild($myid);
        if ($childs) {
            $total = count($childs);
            foreach ($childs as $value) {
                $id = $value[$this->idname];
                $j  = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                    $k = $itemprefix ? $this->nbsp : '';
                } else {
                    $j .= $this->icon[1];
                    $k = $itemprefix ? $this->icon[0] : '';
                }
                $spacer   = $itemprefix ? $itemprefix . $j : '';
                $selected = $selectedids && in_array($id, (is_array($selectedids) ? $selectedids : explode(',', $selectedids))) ? 'selected' : '';
                $disabled = $disabledids && in_array($id, (is_array($disabledids) ? $disabledids : explode(',', $disabledids))) ? 'disabled' : '';
                $value    = array_merge($value, [
                        'selected' => $selected,
                        'disabled' => $disabled,
                        'spacer'   => $spacer
                    ]
                );
                $value    = array_combine(array_map(function ($k) {
                    return '@' . $k;
                }, array_keys($value)), $value);
                $html     .= strtr((($value["@{$this->pidname}"] == 0 || $this->getChild($id)) && $toptpl ? $toptpl : $this->html_temp_option), $value);
                $html     .= $this->htmlSelectOption($id, $selectedids, $disabledids, $itemprefix . $k . $this->nbsp, $toptpl);
                $number++;
            }
        }
        return $html;
    }
}
