<?php /* Smarty version Smarty-3.1.8, created on 2018-05-27 22:08:49
         compiled from "I:/www/jyb/application/main/template\login\index.html" */ ?>
<?php /*%%SmartyHeaderCode:265925b0abbf1bad757-29643793%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3c8b90314c5f2eb63ad6c24839656e25f5452328' => 
    array (
      0 => 'I:/www/jyb/application/main/template\\login\\index.html',
      1 => 1527429634,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '265925b0abbf1bad757-29643793',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'app_name' => 0,
    'assets_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5b0abbf1bebf69_88040951',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5b0abbf1bebf69_88040951')) {function content_5b0abbf1bebf69_88040951($_smarty_tpl) {?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>登录 - <?php echo $_smarty_tpl->tpl_vars['app_name']->value;?>
后台管理系统</title>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['assets_url']->value;?>
/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['assets_url']->value;?>
/css/login.css" />
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['assets_url']->value;?>
/plugins/layui/layui.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['assets_url']->value;?>
/js/common.js"></script>
</head>

<body class="beg-login-bg">
    <div class="beg-login-logo"><?php echo $_smarty_tpl->tpl_vars['app_name']->value;?>
后台管理系统</div>
    <div class="beg-login-box">
        <header>
            <h1>登 录</h1>
        </header>
        <div class="beg-login-main">
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe612;</i>
                    </label>
                    <input type="text" name="username" id="username" lay-verify="required|username" autocomplete="off" placeholder="用户名" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe642;</i>
                    </label>
                    <input type="password" name="password" id="pwd" lay-verify="required" autocomplete="off" placeholder="密码" class="layui-input">
                </div>
                <div class="layui-form-item verify">
                    <label class="beg-login-icon">
                        <i class="layui-icon">&#xe6b2;</i>
                    </label>
                    <input type="text" name="verify" id="yz" lay-verify="required" autocomplete="off" placeholder="验证码" class="layui-input">
                    <img title="点击刷新" src="login/ajax_verify" onclick="this.src = this.src">
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn" lay-submit lay-filter="login" data-href="login/login" id="login">登录</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    layui.use(['layer', 'form'], function() {
        var $ = layui.jquery,
            // layer = undefined === parent.layer ? layui.layer : parent.layer,
            layer = layui.layer,
            form = layui.form;

        form.on('submit(login)', function(data) {
            $.post($(this).data('href'), data.field, function(res) {
                if (!res.status) {
                    layer.alert(res.msg);
                    var verify = $('.verify img');
                    verify.attr('src', verify.attr('src')); //刷新验证码
                    return false;
                }
                layer.msg(res.msg, {
                    time: 1000
                }, function() {
                    // layer.closeAll();
                    location.href = '/';
                });
            }, 'json');
            return false;

        });
    });
    </script>
</body>

</html>
<?php }} ?>