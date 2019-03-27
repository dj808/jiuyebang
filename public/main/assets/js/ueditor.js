/**
 * @todo   layui UEditor 封装
 * @author Malcolm  (2018年05月25日)
 */

layui.define(['jquery'], function (exports) {
    var $ = layui.$;
    var editor = {};
    var config = {
        elm: "",
        opt: null,
        baseUrl: "http://apps.bdimg.com/libs/ueditor/1.4.3.1/" ,
        configUrl: null,
        serverUrl: '/?app=ueditor',
        done: function () {
        }
    };

    var obj = {
        init: function (con) {
            config.elm = typeof con.elm === "string" ? con.elm : config.elm;
            config.opt = typeof con.opt === "string" ? con.opt : config.opt;
            config.baseUrl = typeof con.baseUrl === "string" ? con.baseUrl : config.baseUrl;
            config.configUrl = typeof con.configUrl === "string" ? con.configUrl : config.configUrl;
            config.serverUrl = typeof con.serverUrl === "string" ? con.serverUrl : config.serverUrl;
            config.done = typeof con.done === "function" ? con.done : config.done;

            window.UEDITOR_HOME_URL = config.baseUrl;
            if (typeof(UE) === "undefined") {
                console.log("初次加载UEditor文件");
                this.run();
            } else {
                if (typeof editor[config.elm] === "object") {
                    console.log("已存在编辑器，销毁后重新渲染");
                    editor[config.elm].destroy();
                    editor[config.elm] = null;
                }
                editor[config.elm] = UE.getEditor(config.elm,config.opt);
                config.done(editor[config.elm]);

            }
        },
        run: function () {
            var configUrl = "/assets/js/ueditor_config.js";
            if (config.configUrl !== null) {
                configUrl = config.configUrl;
            }
            $.getScript(configUrl, function () {
                console.log("UEditor配置文件加载成功.");
                window.UEDITOR_CONFIG.serverUrl = config.serverUrl;
                $.getScript(config.baseUrl + "/ueditor.all.js", function () {
                    console.log("UEditor核心文件加载成功.");
                    if (config.done !== null && config.done !== undefined) {
                        editor[config.elm] = UE.getEditor(config.elm,config.opt);
                        config.done(editor[config.elm]);
                    }
                });
            });
        }


    };

    exports('ueditor', obj);
});