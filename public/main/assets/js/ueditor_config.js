/**
 * @todo   layui UEditor 设置
 * @author Malcolm  (2018年05月25日)
 */

(function () {

    var URL = window.UEDITOR_HOME_URL || getUEBasePath();  //路径配置


    window.UEDITOR_CONFIG = {

        //为编辑器实例添加一个路径，这个不能被注释
        UEDITOR_HOME_URL: URL

        // 服务器统一请求接口路径
        , serverUrl: URL + "php/controller.php"

        //工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的重新定义
        , toolbars: [[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist',  'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph',  'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
            'link', 'unlink',  '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'attachment', 'map',  'insertcode',   'background', '|',
            'horizontal',  'spechars',   '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
             'preview', 'searchreplace'
        ]]

        ,zIndex:99999999

        ,autoSyncData:true //自动同步编辑器要提交的数据


        ,retainOnlyLabelPasted: true   //粘贴只保留标签，去除标签所有属性

        ,pasteplain:false  //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴

        ,'filterTxtRules' : function(){     //纯文本粘贴模式下的过滤规则
           function transP(node){
               node.tagName = 'p';
               node.setStyle();
           }
           return {
               //直接删除及其字节点内容
               '-' : 'script style object iframe embed input select',
               'p': {$:{}},
               'br':{$:{}},
               'div':{'$':{}},
               'li':{'$':{}},
               'caption':transP,
               'th':transP,
               'tr':transP,
               'h1':transP,'h2':transP,'h3':transP,'h4':transP,'h5':transP,'h6':transP,
               'td':function(node){
                   //没有内容的td直接删掉
                   var txt = !!node.innerText();
                   if(txt){
                       node.parentNode.insertAfter(UE.uNode.createText(' &nbsp; &nbsp;'),node);
                   }
                   node.parentNode.removeChild(node,node.innerText())
               }
           }
        }()


        //字号
        //,'fontsize':[10, 11, 12, 14, 16, 18, 20, 24, 36]



        ,elementPathEnabled : false  //是否启用元素路径，默认是显示

        ,wordCount:true          //是否开启字数统计
        ,maximumWords:10000       //允许的最大字符数
        //字数统计提示，{#count}代表当前字数，{#leave}代表还可以输入多少字符数,留空支持多语言自动切换，否则按此配置显示
        ,wordCountMsg:''   //当前已输入 {#count} 个字符，您还可以输入{#leave} 个字符
        //超出字数限制提示  留空支持多语言自动切换，否则按此配置显示
        ,wordOverFlowMsg:''    //<span style="color:red;">你输入的字符个数已经超出最大允许值，服务器可能会拒绝保存！</span>

        ,catchRemoteImageEnable: true //设置是否抓取远程图片


        //allowLinkProtocol 允许的链接地址，有这些前缀的链接地址不会自动添加http
        //, allowLinkProtocols: ['http:', 'https:', '#', '/', 'ftp:', 'mailto:', 'tel:', 'git:', 'svn:']



        ,disabledTableInTable:true  //禁止表格嵌套
        ,allowDivTransToP:true      //允许进入编辑器的div标签自动变成p标签
        ,rgb2Hex:true               //默认产出的数据中的color自动从rgb格式变成16进制格式

        // xss 过滤是否开启,inserthtml等操作
        ,xssFilterRules: true
        //input xss过滤
        ,inputXssFilter: true
        //output xss过滤
        ,outputXssFilter: true
        // xss过滤白名单 名单来源: https://raw.githubusercontent.com/leizongmin/js-xss/master/lib/default.js
        ,whitList: {
            a:      ['target', 'href', 'title', 'class', 'style'],
            abbr:   ['title', 'class', 'style'],
            address: ['class', 'style'],
            area:   ['shape', 'coords', 'href', 'alt'],
            article: [],
            aside:  [],
            audio:  ['autoplay', 'controls', 'loop', 'preload', 'src', 'class', 'style'],
            b:      ['class', 'style'],
            bdi:    ['dir'],
            bdo:    ['dir'],
            big:    [],
            blockquote: ['cite', 'class', 'style'],
            br:     [],
            caption: ['class', 'style'],
            center: [],
            cite:   [],
            code:   ['class', 'style'],
            col:    ['align', 'valign', 'span', 'width', 'class', 'style'],
            colgroup: ['align', 'valign', 'span', 'width', 'class', 'style'],
            dd:     ['class', 'style'],
            del:    ['datetime'],
            details: ['open'],
            div:    ['class', 'style'],
            dl:     ['class', 'style'],
            dt:     ['class', 'style'],
            em:     ['class', 'style'],
            font:   ['color', 'size', 'face'],
            footer: [],
            h1:     ['class', 'style'],
            h2:     ['class', 'style'],
            h3:     ['class', 'style'],
            h4:     ['class', 'style'],
            h5:     ['class', 'style'],
            h6:     ['class', 'style'],
            header: [],
            hr:     [],
            i:      ['class', 'style'],
            img:    ['src', 'alt', 'title', 'width', 'height', 'id', '_src', 'loadingclass', 'class', 'data-latex'],
            ins:    ['datetime'],
            li:     ['class', 'style'],
            mark:   [],
            nav:    [],
            ol:     ['class', 'style'],
            p:      ['class', 'style'],
            pre:    ['class', 'style'],
            s:      [],
            section:[],
            small:  [],
            span:   ['class', 'style'],
            sub:    ['class', 'style'],
            sup:    ['class', 'style'],
            strong: ['class', 'style'],
            table:  ['width', 'border', 'align', 'valign', 'class', 'style'],
            tbody:  ['align', 'valign', 'class', 'style'],
            td:     ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
            tfoot:  ['align', 'valign', 'class', 'style'],
            th:     ['width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'],
            thead:  ['align', 'valign', 'class', 'style'],
            tr:     ['rowspan', 'align', 'valign', 'class', 'style'],
            tt:     [],
            u:      [],
            ul:     ['class', 'style'],
            video:  ['autoplay', 'controls', 'loop', 'preload', 'src', 'height', 'width', 'class', 'style']
        }
    };

    function getUEBasePath(docUrl, confUrl) {

        return getBasePath(docUrl || self.document.URL || self.location.href, confUrl || getConfigFilePath());

    }

    function getConfigFilePath() {

        var configPath = document.getElementsByTagName('script');

        return configPath[ configPath.length - 1 ].src;

    }

    function getBasePath(docUrl, confUrl) {

        var basePath = confUrl;


        if (/^(\/|\\\\)/.test(confUrl)) {

            basePath = /^.+?\w(\/|\\\\)/.exec(docUrl)[0] + confUrl.replace(/^(\/|\\\\)/, '');

        } else if (!/^[a-z]+:/i.test(confUrl)) {

            docUrl = docUrl.split("#")[0].split("?")[0].replace(/[^\\\/]+$/, '');

            basePath = docUrl + "" + confUrl;

        }

        return optimizationPath(basePath);

    }

    function optimizationPath(path) {

        var protocol = /^[a-z]+:\/\//.exec(path)[ 0 ],
            tmp = null,
            res = [];

        path = path.replace(protocol, "").split("?")[0].split("#")[0];

        path = path.replace(/\\/g, '/').split(/\//);

        path[ path.length - 1 ] = "";

        while (path.length) {

            if (( tmp = path.shift() ) === "..") {
                res.pop();
            } else if (tmp !== ".") {
                res.push(tmp);
            }

        }

        return protocol + res.join("/");

    }

    window.UE = {
        getUEBasePath: getUEBasePath
    };

})();