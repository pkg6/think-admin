layui.define(["form","think"], function () {
    "use strict";
    var form = layui.form
    var think = layui.think
    form.on('submit(LAY-user-login-submit)', function (obj) {
        think.request.ajaxform(obj,function (ret) {
            setTimeout(function () {
                location.href = ret.url
            }, ret.wait)
        })
        //阻止默认 form 跳转
        return false;
    })
});
