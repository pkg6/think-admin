<?php
// 应用公共文件


use Symfony\Component\HttpFoundation\IpUtils;
use think\facade\Lang;
use tp5er\think\facade\Jump;


if (!function_exists('check_ip_allowed')) {
    /**
     * 检测IP是否允许.
     *
     * @param string $ip IP地址
     */
    function check_ip_allowed($ip = null)
    {
        $ip             = is_null($ip) ? request()->ip() : $ip;
        $forbiddenipArr = app()->config->get("admin.forbiddenip");
        $forbiddenipArr = !$forbiddenipArr ? [] : $forbiddenipArr;
        $forbiddenipArr = is_array($forbiddenipArr) ? $forbiddenipArr : array_filter(explode("\n", str_replace("\r\n", "\n", $forbiddenipArr)));
        if ($forbiddenipArr && IpUtils::checkIp($ip, $forbiddenipArr)) {
            throw new HttpResponseException(
                Jump::error('Your IP cannot be accessed')
            );
        }
    }
}


if (!function_exists('__')) {
    /**
     * 获取语言变量值
     *
     * @param string $name 语言变量名
     * @param string | array $vars 动态变量值
     * @param string $lang 语言
     *
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }

        return Lang::get($name, $vars, $lang);
    }
}



if (!function_exists('rule_url')) {

    /**
     * 加载admin静态资源文件.
     *
     * @param $rule
     * @return \think\route\Url
     */
    function rule_url($rule)
    {
        if ($rule instanceof \think\Model){
            $rule = $rule->toArray();
        }
        parse_str($rule['condition']??'', $array);
        return url($rule['url'], $array);
    }
}
