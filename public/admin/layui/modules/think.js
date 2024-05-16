// 定义模块（通常单独作为一个 JS 文件）
layui.define(['layer', 'jquery', 'cookie', 'table', 'form', 'treeTable'], function (exports) {
    var layer = layui.layer;
    var $ = layui.$;
    var form = layui.form;
    var treeTable = layui.treeTable;
    var table = layui.table;
    //工具类
    var helper = {
        //在路径上追加参数
        // /admin/index {"id":1} 得到 /admin/index?id=1
        // /admin/index?id=1 {"name":"name"} 得到 /admin/index?id=1&name=name
        appendParamsToUrl: function (path, params) {
            // 检查路径中是否已经有参数
            let hasParams = path.includes('?');
            let newPath = path;
            // 如果路径中已经有参数，则使用 '&' 连接新参数，否则使用 '?'
            let separator = hasParams ? '&' : '?';
            // 遍历参数对象，将每个键值对连接到路径上
            for (const key in params) {
                if (params.hasOwnProperty(key)) {
                    newPath += `${separator}${key}=${params[key]}`;
                    // 更新分隔符为 '&'，因为已经有一个参数了
                    separator = '&';
                }
            }
            return newPath;
        },
        merger: function (...obj) {
            let res = {};
            let combine = (obj) => {
                for (let prop in obj) {
                    if (obj.hasOwnProperty(prop)) {
                        if (Object.prototype.toString.call(obj[prop]) === '[object Object]') {
                            res[prop] = helper.merger(res[prop], obj[prop]);
                        } else {
                            res[prop] = obj[prop];

                        }

                    }
                }
            }
            //扩张运算符将两个对象合并到一个数组里因此可以调用length方法
            for (let i = 0; i < obj.length; i++) {
                combine(obj[i]);
            }
            return res;
        },
        if: function (val, def = null) {
            return val ? val : def
        }
    }
    //弹出消息层
    var lay = {
        success: function (content) {
            layer.msg(content, {icon: 1, time: 800}, this.closeLayer)
        },
        error: function (content) {
            layer.msg(content, {icon: 2, time: 800}, this.closeLayer)
        },
        closeLayer: function () {
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        },
        eventURLByTpl: function (scriptID) {
            let eventURL = {}
            // 获取模板内容
            let template = document.getElementById(scriptID);
            if (template === null) {
                return eventURL
            }
            // 创建一个虚拟的父节点，并将模板内容插入其中
            let parent = document.createElement('div');
            parent.innerHTML = template.innerHTML;
            // 使用 JavaScript 在父节点内部查找所有包含按钮节点
            let buttons = parent.querySelectorAll('button');
            // 遍历这些按钮节点，这里只是简单地输出它们的文本内容
            buttons.forEach(function (element) {
                let toolbarevent = lay.getElementEvent(element)
                let toolbarURL = lay.getElementURL(element)
                if (toolbarevent !== "" && toolbarURL !== "") {
                    eventURL[toolbarevent] = toolbarURL;
                }
            });
            return eventURL
        },
        getElementEvent(element) {
            return this.getElementVal(element, 'lay-event')
        },
        getElementURL(element) {
            return this.getElementVal(element, 'lay-url')
        },
        getElementTitle(element) {
            return this.getElementVal(element, 'lay-title')
        },
        getElementVal(element, key) {
            return helper.if(element.getAttribute(key), "");
        }

    }

    //发送ajax请求
    var request = {
        post: function (path, data, callback) {
            this.ajax(path, "POST", data, callback)
        },
        get: function (path, callback) {
            this.ajax(path, "GET", {}, callback)
        },
        ajax: function (action, method, data, callback, options = {}) {
            data = helper.if(data, {})
            data["token"] = Cookies.get("think_token");
            options = helper.merger(options, {
                url: action,
                type: method,
                data: data,
                dataType: "json",
                success: function (ret, status, xh) {
                    if (ret.code === 0) {
                        //弹出成功消息
                        lay.success(ret.msg)
                        //存在函数就进行调用
                        typeof callback == "function" && callback(ret)
                    } else {
                        //接口失败
                        lay.error(ret.msg)
                    }
                },
                error: function (xhr, status, err) {
                    var result = {code: xhr.status, msg: xhr.statusText, data: null};
                    lay.error(result.msg, {icon: 2})
                }
            })
            console.debug("--------think.js ajax debug-------------")
            console.debug("obj:", options)
            console.debug("--------think.js ajax debug-------------")

            $.ajax(options)
        },
        ajaxform: function (obj, callback) {
            this.ajax(obj.form.action, obj.form.method, obj.field, callback)
        }
    }

    var tables = {
        bind: function (elem, url) {
            this.elem = elem;
            this.url = url;
            return this
        },
        treeTable: function (obj) {
            let options = helper.merger(obj, {
                elem: '#' + this.elem,
                url: this.url,
                method: 'POST',
                tree: {async: {enable: true, url: this.url, autoParam: ["pid=id"]}},
                parseData: function (res) {
                    return {
                        "code": res.code, // 解析接口状态
                        "msg": res.message, // 解析提示文本
                        "count": res.data.total, // 解析数据长度
                        "data": res.data.data // 解析数据列表
                    };
                }
            })

            console.debug("--------think.js tables debug-------------")
            console.debug("url: ：" + this.url)
            console.debug("options: ", options)
            console.debug("--------think.js tables debug-------------")

            treeTable.render(options)
            return this
        },
        table: function (obj) {
            let options = helper.merger(obj, {
                elem: '#' + this.elem,
                url: this.url,
                method: 'POST',
                page: true,
                parseData: function (res) {
                    return {
                        "code": res.code, // 解析接口状态
                        "msg": res.message, // 解析提示文本
                        "count": res.data.total, // 解析数据长度
                        "data": res.data.data // 解析数据列表
                    };
                },
            })


            this.eventsURL = tables.events.eventsURL(options)

            console.debug("--------think.js tables debug-------------")
            console.debug("url: ：" + this.url)
            console.debug("options: ", options)
            console.debug("eventsURL: ", this.eventsURL)
            console.debug("--------think.js tables debug-------------")


            table.render(options);
            return this
        },
        on: function () {
            this.onTool()
            this.onToolbar()
            this.onSearch()
            this.onSwitchStatus()
        },
        reload: function (options) {
            table.reload(tables.elem, options);
        },
        onSearch: function () {
            this.onForms('submit(search)', function (form) {
                tables.reload({where: form.field})
                return false;
            })
            return this
        },
        onSwitchStatus: function () {
            this.onForms('switch(status)', function (obj) {
                let status = 0;
                if (obj.elem.checked) {
                    status = 1
                }
                if (status === 1) {
                    lay.success("激活成功")
                } else {
                    lay.success("禁用成功")
                }
                //阻止默认 form 跳转
                return false;
            })
            return this
        },
        onTool: function () {
            //行级事件
            treeTable.on("tool", function (obj) {
                tables.events.bind(this, obj)
            })
            return this
        },
        onToolbar: function () {
            // 工具条点击事件
            treeTable.on('toolbar', function (obj) {
                tables.events.bind(this, obj)
            });
            return this
        },
        onForms(onName, callback) {
            form.on(onName, callback)
            return this
        },
        events: {
            bind: function (element, obj) {
                this.element = element;
                this.obj = obj
                this.event = lay.getElementEvent(this.element)
                this.path = lay.getElementURL(this.element)
                this.title = lay.getElementTitle(this.element)
                console.debug("--------think.js events debug-------------")
                console.debug("event: " + this.event)
                console.debug("url: " + this.path)
                console.debug("elem: ", element)
                console.debug("obj:", this.obj)
                console.debug("--------think.js events debug-------------")
                typeof tables.events[this.event] === "function" && tables.events[this.event].apply(this);
            },
            eventsURL: function (options) {
                let col_toolbar_event_url = {}
                let toolbar_event_url = {}
                // 获取单行操作的event和请求地址
                options.cols.forEach(function (t) {
                    t.forEach(function (e) {
                        if (!!e.toolbar) {
                            col_toolbar_event_url = lay.eventURLByTpl(e.toolbar.substr(1))
                        }
                    })
                })
                // 获取批量操作event和请求地址
                if (!!options.toolbar) {
                    toolbar_event_url = lay.eventURLByTpl(options.toolbar.substr(1))
                }
                return helper.merger(col_toolbar_event_url, toolbar_event_url)
            },
            fn: function (event, fn) {
                tables.events[event] = fn;
                return this
            },
            adds: function () {
                layer.open({
                    content: tables.events.path,
                    title: helper.if(this.title, 'add data'),
                    area: ['40%', '85%'],
                    type: 2,
                    maxmin: true,
                    shadeClose: true
                });
            },
            edit: function () {
                layer.open({
                    content: helper.appendParamsToUrl(tables.events.path, {"ids": this.obj.data.id}),
                    title: helper.if(this.title, 'edit data'),
                    area: ['40%', '85%'],
                    type: 2,
                    maxmin: true,
                    shadeClose: true,
                });
            },
            delete: function () {
                layer.confirm('确定要删除吗？', {icon: 3}, function () {
                    request.post(tables.events.path, {"ids": tables.events.obj.data.id}, function (ret) {
                        tables.events.obj.del()
                    })
                });
            },
        }
    }

    tables.onForms('submit(add-data)', function (obj) {
        request.ajaxform(obj, function () {
            tables.reload()
        })
        //阻止默认 form 跳转
        return false;
    })
    tables.onForms('submit(edit-data)', function (obj) {
        request.ajaxform(obj)
        //阻止默认 form 跳转
        return false;
    })

    exports('think', {
        helper: helper,
        request: request,
        tables: tables,
        lay: lay,
    });
});
