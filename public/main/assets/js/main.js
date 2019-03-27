layui.use(['table', 'form', 'layer', 'laydate', 'upload', 'laytpl', 'flow', 'layedit'], function() {
    var $ = layui.jquery,
        table = layui.table,
        form = layui.form,
        layer = layui.layer,
        laydate = layui.laydate,
        upload = layui.upload,
        laytpl = layui.laytpl,
        flow = layui.flow,
        layedit = layui.layedit;

    // 日期控件
    laydate.render({
        elem: '.laydate'
    });

    // 搜索
    form.on('submit(search)', function(data) {
        layer.load();
        var options = {
            where: data.field,
            done: function(res, curr, count) {
                layer.closeAll('loading');
                if (false === res.status) {
                    layer.alert(res.msg);
                    return false;
                }
                set_sortable();
            },
        };
        // 表格重载
        table.reload('list', options);
        return false;
    });

    // 添加/修改
    var edit_layer = function(id, tpl, type) {
        tpl = tpl || 'edit';
        var href = _this_controller_url + '/' + tpl;
        var title = 0 == id ? '添加' : '修改';
        layer.load();
        $.get(href, {
            id: id,
            type: type,
        }, function(html) {
            layer.closeAll('loading');
            if (false === html.status) {
                layer.alert(html.msg);
                return false;
            }
            layer.open({
                type: 1,
                title: title,
                content: html,
                area: ['800px', '100%'],
                maxmin: true,
                scrollbar: false,
                btn: ['保存', '取消'],
                btnAlign: 'c',
                yes: function(index, layero) {
                    // 同步富文本编辑器数据
                    var layedit_index = $('.layedit').data('index');
                    // layedit.sync(layedit_index);
                    $('form .layedit').html(layedit.getContent(layedit_index));

                    //触发表单的提交事件
                    $('form.layui-form').find('button[lay-filter=edit]').click();
                },
                success: function(layero, index) {
                    //弹出窗口成功后渲染表单
                    form.render();
                    laydate.render({
                        elem: '.laydate'
                    });
                    form.on('submit(edit)', function(data) {
                        layer.load();
                        $.post(href, data.field, function(res) {
                            layer.closeAll('loading');
                            if (false === res.status) {
                                layer.alert(res.msg);
                                return false;
                            }
                            layer.msg(res.msg, {
                                time: 1000
                            }, function() {
                                layer.closeAll();
                                $('form.layui-form').find('button[lay-filter=search]').click();
                            });
                        }, 'json');
                        return false;
                    });
                },
            });
        });
    }

    // 删除
    var del_layer = function(id, popt, popt_name) {
        var opt = undefined == popt ? 'del' : popt;
        var opt_name = undefined == popt_name ? '删除' : popt_name;
        layer.confirm('真的' + opt_name + '吗？',{icon: 3, title:'确认'+opt_name}, function(index) {
            var href = _this_controller_url + '/' + opt;
            layer.load();
            $.post(href, {
                id: id
            }, function(res) {
                layer.closeAll('loading');
                if (false === res.status) {
                    layer.alert(res.msg);
                    return false;
                }
                layer.msg(res.msg, {
                    time: 1000
                }, function() {
                    $('form.layui-form').find('button[lay-filter=search]').click();
                });
            }, 'json');
        });
    }

    // 详情
    var detail_layer = function(id) {
        var href = _this_controller_url + '/detail';
        layer.load();
        $.get(href, {
            id: id
        }, function(html) {
            layer.closeAll('loading');
            if (false === html.status) {
                layer.alert(html.msg);
                return false;
            }
            layer.open({
                type: 1,
                title: '详情',
                content: html,
                area: ['880px', '100%'],
                maxmin: true,
                scrollbar: false,
            });
        });
    }

    // 图片缩略图放大
    var thumb_layer = function(photo) {
        // 放大缩略图
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            area: 'auto',
            maxWidth: '1000',
            skin: 'layui-layer-nobg', //没有背景色
            shadeClose: true,
            content: '<img style="max-width:1000px;" src="' + photo + '" />',
        });
    }

    // 菜单
    var menu_layer = function(id) {
        var href = _this_module_url + '/AdminRole/layer_menu';
        layer.load();
        $.get(href, {
            role_id: id
        }, function(html) {
            layer.closeAll('loading');
            if (false === html.status) {
                layer.alert(html.msg);
                return false;
            }
            var select_index = layer.open({
                type: 1,
                title: '菜单列表',
                content: html,
                area: ['351px', '100%'],
                maxmin: true,
                scrollbar: false,
                btn: ['保存', '取消'],
                btnAlign: 'c',
                yes: function(index, layero) {
                    var checkStatus = table.checkStatus('list-menu');
                    if (0 == checkStatus.data.length) {
                        layer.msg('请至少选择一个项目！');
                        return false;
                    }
                    var menu_ids_arr = new Array();
                    for (var i = 0; i < checkStatus.data.length; i++) {
                        menu_ids_arr.push(checkStatus.data[i].id);
                    }
                    layer.load();
                    $.post(href, {
                        role_id: id,
                        menu_ids: menu_ids_arr.join(','),
                    }, function(res) {
                        layer.closeAll('loading');
                        if (false === res.status) {
                            layer.alert(res.msg);
                            return false;
                        }
                        layer.msg(res.msg, {
                            time: 1000
                        }, function() {
                            layer.close(select_index);
                            $('form.layui-form').find('button[lay-filter=search]').click();
                        });
                    }, 'json');
                    return false;
                },
            });
        });
    }

    // 权限
    var access_layer = function(id) {
        var href = _this_controller_url + '/layer_access';
        layer.load();
        $.get(href, {
            role_id: id
        }, function(html) {
            layer.closeAll('loading');
            if (false === html.status) {
                layer.alert(html.msg);
                return false;
            }
            var select_index = layer.open({
                type: 1,
                title: '权限列表',
                content: html,
                area: ['620px', '100%'],
                maxmin: true,
                scrollbar: false,
                btn: ['保存', '取消'],
                btnAlign: 'c',
                yes: function(index, layero) {
                    var access_list = $('.access-list').find('form').serializeArray();
                    if (0 == access_list.length) {
                        layer.msg('请至少选择一个项目！');
                        return false;
                    }
                    access_list.push({
                        name: 'role_id',
                        value: id
                    });
                    layer.load();
                    $.post(href, access_list, function(res) {
                        layer.closeAll('loading');
                        if (false === res.status) {
                            layer.alert(res.msg);
                            return false;
                        }
                        layer.msg(res.msg, {
                            time: 1000
                        }, function() {
                            layer.close(select_index);
                            $('form.layui-form').find('button[lay-filter=search]').click();
                        });
                    }, 'json');
                    return false;
                },
            });
        });
    }

    // 添加/编辑
    $('body').on('click', 'button[data-opt=edit]', function() {
        var id = undefined == $(this).data('id') ? 0 : $(this).data('id');
        var type = undefined == $(this).data('type') ? 0 : $(this).data('type');
        edit_layer(id, '', type);
    });

    // 添加(利用于添加、编辑模板不一样情况)
    $('body').on('click', 'button[data-opt=add]', function() {
        var type = undefined == $(this).data('type') ? 0 : $(this).data('type');
        edit_layer(0, 'add', type);
    });

    // 导出Excel
    $('body').on('click', 'button[data-opt=export]', function() {
        var href = _this_controller_url + '/export_excel?';
        href += 'fr=' + ($('form.layui-form').find('[name=fr]').length > 0 ? $('form.layui-form').find('[name=fr]').val() : '');
        href += '&product_id=' + ($('form.layui-form').find('[name=product_id]').length > 0 ? $('form.layui-form').find('[name=product_id]').val() : '');
        href += '&date=' + $('form.layui-form').find('[name=date]').val();
        layer.load();
        $.get(href, null, function(res) {
            layer.closeAll('loading');
            if (false === res.status) {
                layer.alert(res.msg);
                return false;
            }
            location.href = href;
        }, 'json');

    });

    // 删除
    $('body').on('click', 'button[data-opt=del]', function() {
        del_layer($(this).data('id'));
    });

    // 详情
    $('body').on('click', '[data-opt=detail]', function() {
        detail_layer($(this).data('id'));
    });

    // 监听表格事件
    table.on('tool(list-table)', function(obj) {
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值
        var tr = obj.tr; //获得当前行 tr 的DOM对象

        if ('detail' === layEvent) {
            // 详情
            detail_layer(data.id);
        } else if ('edit' === layEvent) {
            // 编辑
            edit_layer(data.id, '', data.type);
        } else if ('del' === layEvent) {
            // 删除
            del_layer(data.id);
        } else if ('thumb' === layEvent) {
            // 图片缩略图放大
            var photo = undefined == data.logo ? (undefined == data.picture ? '' : data.picture) : data.logo;

            thumb_layer(photo);
        } else if ('block' === layEvent) {
            // 封号
            del_layer(data.id, 'block', '封号');
        } else if ('resetpwd' === layEvent) {
            // 封号
            del_layer(data.id, 'resetpwd', '重置密码');
        } else if ('up' === layEvent) {
            // 向上排序
            sort_layer(data.id, 1, data.type);
        } else if ('down' === layEvent) {
            // 向下排序
            sort_layer(data.id, -1, data.type);
        } else if ('menu' === layEvent) {
            // 菜单
            menu_layer(data.id);
        } else if ('access' === layEvent) {
            // 权限
            access_layer(data.id);
        }
    });

    // 拖拽排序
    var set_sortable = function() {
        if (0 == $('#list').length || true != $('#list').data('sort')) {
            return;
        }
        Sortable.create($('.layui-table tbody')[0], {
            handle: ".sort",
            draggable: "tr",
            animation: 150,
            onStart: function( /**Event*/ evt) { // 拖拽开始
                var itemEl = evt.item; // 当前拖拽的html元素
            },
            onEnd: function( /**Event*/ evt) { // 拖拽结束
                var itemEl = evt.item;
                var id_arr = new Array();
                $('.layui-table .sort').each(function() {
                    id_arr.push($(this).data('id'));
                });
                var id_str = id_arr.join(',');
                var href = _this_controller_url + '/drag_sort';
                layer.load();
                $.post(href, {
                    id_str: id_str,
                }, function(res) {
                    layer.closeAll('loading');
                    if (false === res.status) {
                        layer.alert(res.msg);
                        return false;
                    }
                    layer.msg(res.msg, {
                        time: 1000
                    }, function() {
                        $('form.layui-form').find('button[lay-filter=search]').click();
                    });
                }, 'json');
            }
        });
    };
    set_sortable();

    // 搜索栏日期控件
    laydate.render({
        elem: '.laydate-range-search',
        format: 'yyyy/MM/dd',
        range: true,
        trigger: 'click',
        done: function(value, date, endDate) {
            $('form.layui-form').find('.laydate-range-search').val(value);
            $('form.layui-form').find('.ala-btn-search').click();
        }
    });
    // 搜索栏checkbox
    form.on('checkbox(search-checkbox)', function(data) {
        $(this).parent().find('input[type=checkbox]').each(function() {
            var _this = $(this);
            _this.prop('checked', false);
        });
        $(this).prop('checked', true);
        form.render();
        $('form.layui-form').find('.ala-btn-search').click();
    });
    // 搜索栏select
    form.on('select(search-select)', function(data) {
        $('form.layui-form').find('.ala-btn-search').click();
    });
    // 城市联动
    form.on('select(select-prov)', function(_this) {
        $.get('/Index/ajax_subcity', {
            'id': _this.value
        }, function(res) {
            if (res.status) {
                var html = '';
                html += '<option value="">-- 选择市 --</option>';
                $.each(res.data, function(key, value) {
                    html += '<option value="' + key + '">' + value + '</option>';
                });
                $('select[name=city_id]').html(html);
                form.render();
            }
        }, 'json');
    });
    form.on('select(select-city)', function(_this) {
        $.get('/Index/ajax_subcity', {
            'id': _this.value
        }, function(res) {
            if (res.status) {
                var html = '';
                html += '<option value="">-- 选择区 --</option>';
                $.each(res.data, function(key, value) {
                    html += '<option value="' + key + '">' + value + '</option>';
                });
                $('select[name=dist_id]').html(html);
                form.render();
            }
        }, 'json');
    });
});
