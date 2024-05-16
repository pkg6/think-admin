layui.use([ 'think'], function () {
    var think = layui.think;


    //菜单授权
    think.tables.events.fn("grant-menu",function (){
        think.lay.success("授权菜单功能待开发")
    })

    think.tables.bind('layui-tree-table', '/admin/group/index').treeTable( {
        cols: [[
            {type: 'checkbox', fixed: 'left'},
            {field: 'id', title: 'ID', width: 80, sort: true, fixed: 'left'},
            {field: 'pid', title: '父级', width: 80, sort: true, fixed: 'left'},
            {field: 'name', title: '名称', fixed: 'left'},
            {title: '授权', align: 'center', width: 200, toolbar: '#auth-toolbar'},
            {fixed: 'right', title: '操作', width: 200, toolbar: '#column-toolbar'}
        ]],
        toolbar: '#toolbar',
        maxHeight: '501px',
        page: true
    }).on()
});
