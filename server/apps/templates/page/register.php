<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>Swoole 社区会员注册</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-production.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/smartadmin-skins.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/static/smartadmin/css/demo.css">
    <link rel="icon" href="/static/smartadmin/img/favicon/favicon.ico" type="image/x-icon">
    <style type="text/css">
    </style>
	<link href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body style="height:auto">
<?php
$display = empty($_GET['display']) ? '' : $_GET['display'];
if ($display != 'popup'): ?>
    <header id="header">
        <div style="margin-left: 20px;">
            <h1>Swoole 社区 - 注册</h1>
        </div>
    </header>
<?php endif; ?>

<div role="main" style="width: 360px; margin: 0 auto;">
    <div id="content" style="top: 60px;">
        <!-- row -->
        <div class="row">
        <div class="alert alert-warning fade in" id="msg" style="display: none;">
              <i class="fa-fw fa fa-times"></i>
            <span id="msg_content"></span>
        </div>
            </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            	<div class="well no-padding">
            		<form action="" id="register-form" class="smart-form client-form" novalidate="novalidate">
            			<header>
							注册
            			</header>
            	
            			<fieldset>
            	
            				<section>
            					<label class="label">电子邮件</label>
            					<label class="input"> <i class="icon-append fa fa-user"></i>
            						<input type="email" id="email" name="email">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 请输入邮件地址</b></label>
            				</section>
            	
            				<section>
            					<label class="label">昵称</label>
            					<label class="input"> <i class="icon-append fa fa-user"></i>
            						<input type="text" id="nickname" name="nickname">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 请输入昵称(英文字母和数字的组合)</b></label>
            				</section>
            				<section>
                                <label class="label">性别</label>
                                <div class="inline-group">
                                    <label class="radio">
                                        <input type="radio" name="sex" value="2">
                                        <i></i>男</label>
                                    <label class="radio">
                                        <input type="radio" name="sex" value="1">
                                        <i></i>女</label>
                                </div>
                            </section>
            				<section>
            					<label class="label">密码</label>
            					<label class="input"> <i class="icon-append fa fa-lock"></i>
            						<input type="password" id="password" name="password">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 请输入密码</b> </label>
            				</section>
            				<section>
            					<label class="label">确认密码</label>
            					<label class="input"> <i class="icon-append fa fa-lock"></i>
            						<input type="password" id="rpassword" name="rpassword">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 请重新输入密码</b> </label>
            				</section>
            	
            			</fieldset>
            			<footer>
                            <a href="/page/login/" style="  line-height: 4;">已有账户登录</a>
            				<button type="submit" class="btn btn-primary">
								注册
            				</button>
            			</footer>
            		</form>
            	</div>
            
            </div>
            <!-- END MAIN CONTENT -->
        </div>
    </div>
</div>
<!-- END MAIN PANEL -->
<script src="/static/smartadmin/js/libs/jquery-2.0.2.min.js"></script>
<script src="/static/smartadmin/js/app.js"></script>
<script src="/static/smartadmin/js/bootstrap/bootstrap.min.js"></script>
<script src="/static/smartadmin/js/notification/SmartNotification.min.js"></script>
<script src="/static/smartadmin/js/plugin/jquery-validate/jquery.validate.min.js"></script>
<script src="/static/smartadmin/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

<script>
    $(function () {
        $("#register-form").validate({
            // Rules for form validation
            rules: {
                email: {
                    required: true
                },
                nickname: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                },
                rpassword: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                }
            },

            // Messages for form validation
            messages: {
                email: {
                    required: '电子邮件不能为空'
                },
                nickname: {
                    required: '用户名不能为空'
                },
                password: {
                    required: '密码不能为空'
                },
                rpassword: {
                    required: '密码不能为空'
                }
            },

            // Do not change code below
            errorPlacement: function (error, element) {
                error.insertAfter(element.parent());
            }
        });

        $("#register-form").submit(function(e){
            var post = {
                "email" : $('#email').val(),
                "nickname" : $('#nickname').val(),
                "sex" : $('input[name="sex"]').val(),
                "password" : $('#password').val(),
                "rpassword" : $('#rpassword').val(),
            };
            if (post.email== "" || post.nickname== "" || post.password == "" || post.rpassword == "") {
                showMessage("请输入注册所需信息");
                return false;
            }

            $.post("/page/register/", post, function (res) {
                if (res.code == 0) {
                    location.href = res.data.url;
                } else {
                    showMessage(res.message);
                }
            });
            return false;
        });
    });

    function showMessage(msg)
    {
        $('#msg_content').html(msg);
        $('#msg').show();
        setTimeout(function(){
            $('#msg').hide(100);
        }, 3000);
    }
</script>
</body>
</html>
