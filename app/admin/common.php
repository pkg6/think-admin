<?php
// 这是系统自动生成的公共文件

use app\common\library\Layui;
use think\helper\Str;


if (!function_exists('think_admin_session')) {

    /**
     * 获取管理员的session用户信息.
     *
     * @param $val
     *
     * @return mixed|void
     */
    function think_admin_session($val = "", $name = "think_admin")
    {
        if (is_null($val)) {
            app()->session->delete($name);
            return;
        }
        if (is_array($val)) {
            app()->session->set($name, $val);
            return;
        }
        $admin = app()->session->get($name);
        if (is_string($val) && $val != "") {
            return $admin[$val];
        }
        return $admin;
    }
}

if (!function_exists('think_admin_info_change')) {

    /**
     *  针对用户信息发生改变需要重写进行登录.
     *
     * @param $val
     *
     * @return mixed
     */
    function think_admin_info_change($val = null)
    {
        $change = app()->session->has("think_admin_info_change");
        if (is_null($val)) {
            return $change;
        }
        app()->session->set("think_admin_info_change", 1);

        return true;
    }
}


if (!function_exists('asset_admin')) {

    /**
     * 加载admin静态资源文件.
     *
     * @param $asset
     *
     * @return string
     */
    function asset_admin($asset)
    {
        $path = app()->config->get("admin.assets_path", "/admin");
        if (Str::startsWith($asset, DIRECTORY_SEPARATOR)) {
            return $path . $asset;
        }

        return $path . DIRECTORY_SEPARATOR . $asset;
    }
}


if (!function_exists('loader_admin_module_js')) {

    /**
     * 加载admin静态资源文件.
     *
     * @return string
     */
    function loader_admin_module_js()
    {
        $currentController = app()->request->controller();
        $currentAction     = app()->request->action();
        $module_js_path    = 'module' .
            DIRECTORY_SEPARATOR . Str::snake($currentController) .
            DIRECTORY_SEPARATOR . Str::snake($currentAction) . '.js';
        $file              = asset_admin($module_js_path);
        $localPath         = app()->getRootPath() . 'public' . $file;

        if (!is_file($localPath)) {
            return '';
        }
        return Layui::loader_script_js($file);
    }
}


