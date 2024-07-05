layui.use(["think"], function () {
    var think = layui.think;
    think.tables.bind('layui-table', '/admin/user/index').table({
        toolbar: '#toolbar',
        smartReloadModel: true,
        cols: [
            [
                {type: 'checkbox'},
                {field: 'id', title: 'ID'},
                {field: 'username', title: '用户名', minWidth: 160},
                {field: '', title: '所属组别', minWidth: 160},
                {field: 'mobile', title: '手机号', minWidth: 160},
                {field: 'email', title: '电子邮箱', minWidth: 160},
                {field: 'status', title: '状态', templet: "#statusTpl", minWidth: 160},
                {field: 'logintime', title: '最后登录', minWidth: 160},
                {field: 'createTime', title: '创建时间', align: 'center', width: 180},
                {fixed: 'right', title: '操作', width: 200, toolbar: '#column-toolbar'}
            ]
        ],
    }).on()
});
