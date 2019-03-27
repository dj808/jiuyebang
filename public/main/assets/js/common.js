layui.config({
    dir: '/assets/plugins/layui/',
    base: '/assets/js/',
    version: new Date().getTime()
});

layui.define(['layer'], function(exports) {
    "use strict";

    var $ = layui.jquery,
        layer = layui.layer;

    var common = {
        /**
         * 抛出一个异常错误信息
         * @param {String} msg
         */
        throwError: function(msg) {
            throw new Error(msg);
            return;
        },
        /**
         * 弹出一个错误提示
         * @param {String} msg
         */
        msgError: function(msg) {
            layer.msg(msg, {
                icon: 5
            });
            return;
        }
    };

    exports('common', common);

});



layui.use(['form'], function() {
    var $ = layui.jquery,
        form = layui.form;

    form.verify({
        username: function(value, item) { //value：表单的值、item：表单的DOM对象
            // if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
            //     return '用户名不能有特殊字符';
            // }
            if (/(^\_)|(\__)|(\_+$)/.test(value)) {
                return '用户名首尾不能出现下划线\'_\'';
            }
            if (/^\d+\d+\d$/.test(value)) {
                return '用户名不能全为数字';
            }
        },
        //我们既支持上述函数式的方式，也支持下述数组的形式
        //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
        password: [
            /^[\S]{6,}$/, '密码必须6位以上，且不能出现空格'
        ]
    });


    //获取表单对象
    $.fn.serializeObject = function() {
        "use strict";

        var result = {};
        var extend = function(i, element) {
            var node = result[element.name];

            // If node with same name exists already, need to convert it to an array as it
            // is a multi-value field (i.e., checkboxes)

            if ('undefined' !== typeof node && node !== null) {
                if ($.isArray(node)) {
                    node.push(element.value);
                } else {
                    result[element.name] = [node, element.value];
                }
            } else {
                result[element.name] = element.value;
            }
        };

        $.each(this.serializeArray(), extend);
        return result;
    };


});


//单值修改AJAX
function edit_value(data) {
    layer.load();
    var $ = layui.jquery;

    $.post(_this_controller_url+'/?act=editValue', {data:data}, function(res) {
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
}
