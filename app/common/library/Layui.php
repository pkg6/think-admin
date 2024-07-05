<?php

namespace app\common\library;

use think\helper\Arr;
use function rule_url;

class Layui
{
    /**
     * @param $paths
     * @return string
     */
    public static function loader_script_js($paths)
    {
        $paths = Arr::wrap($paths);
        $js    = "";
        foreach ($paths as $path) {
            $js .= '<script src="' . $path . '"></script>' . PHP_EOL;
        }
        return $js;
    }

    /**
     * @param $paths
     * @return string
     */
    public static function loader_link_css($paths)
    {
        $paths = Arr::wrap($paths);
        $css = "";
        foreach ($paths as $path) {
            $css .= '<link href="' . $path . '" rel="stylesheet">' . PHP_EOL;
        }
        return $css;
    }

    /**
     * @param array $menu
     * @param string $nodeClass
     * @param string $childClass
     * @return string
     */
    public static function menuLi($menu, $nodeClass = 'layui-nav-item', $childClass = 'layui-nav-child')
    {
        $htmlHrefFn  = function ($item) {
            $html = '<a ';
            if (!empty($item['url']) && empty($item['childlist'])) {
                $url  = rule_url($item);
                $html .= 'lay-id="' . $url . '" lay-url="' . $url . '"';
            }
            $html .= '>';
            if (!empty($item['icon'])) {
                $html .= '<i class="' . $item['icon'] . '"></i>';
            }
            $html .= '<cite>' . $item['name'] . '</cite>';
            $html .= '</a>';
            return $html;
        };
        $htmlChildfn = function ($chidlist) use (&$htmlChildfn, &$htmlHrefFn, &$childClass) {
            if (empty($chidlist)) {
                return '';
            }
            $html = '';
            foreach ($chidlist as $item) {
                $html .= '<dl class="' . $childClass . '">';
                $html .= '<dd>';
                $html .= $htmlHrefFn($item);

                if (!empty($item['childlist'])) {
                    $html .= $htmlChildfn($item['childlist']);
                }
                $html .= "</dd>";
                $html .= '</dl>';
            }
            return $html;
        };
        $html        = '';
        foreach ($menu as $item) {
            $html .= '<li class="' . $nodeClass . '">';
            $html .= $htmlHrefFn($item);
            $html .= $htmlChildfn($item['childlist']);
            $html .= '</li>';
        }
        return $html;
    }
}
