layui.use(['think'], function () {
    var think = layui.think;
    think.tables.bind('layui-tree-table', '/admin/rule/index').treeTable({
        cols: [[
            {type: 'checkbox', fixed: 'left'},
            {field: 'id', title: 'ID', width: 80, sort: true, fixed: 'left'},
            {field: 'pid', title: '父级', width: 80, sort: true, fixed: 'left'},
            {field: 'name', title: '节点名称', fixed: 'left'},
            {field: 'icon', title: '图标', fixed: 'left'},
            {field: 'url', title: '规则URL', fixed: 'left'},
            {field: 'type', title: '类型', fixed: 'left'},
            {field: 'status', title: '状态', fixed: 'left'},
            {fixed: 'right', title: '操作', width: 200, toolbar: '#column-toolbar'}
        ]],
        toolbar: '#toolbar',
        maxHeight: '501px',
        page: true
    }).on()
});
