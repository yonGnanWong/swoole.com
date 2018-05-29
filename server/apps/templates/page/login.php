<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>Swoole 社区登录</title>
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
            <h1>Swoole 社区 - 登录</h1>
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
            		<form action="" id="login-form" class="smart-form client-form" novalidate="novalidate">
            			<header>
            				登录
            			</header>
            	
            			<fieldset>
            	
            				<section>
            					<label class="label">电子邮件</label>
            					<label class="input"> <i class="icon-append fa fa-user"></i>
            						<input type="email" id="email" name="email">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> 请输入邮件地址</b></label>
            				</section>
            	
            				<section>
            					<label class="label">密码</label>
            					<label class="input"> <i class="icon-append fa fa-lock"></i>
            						<input type="password" id="password" name="password">
            						<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> 请输入密码</b> </label>
            				</section>
            	
							<section>
								<label class="checkbox">
									<input type="checkbox" name="auto" checked="">
									<i></i>保持登录</label>
							</section>
            			</fieldset>
            			<footer>
                            <a href="/page/register/" style="  line-height: 4;">注册新账户</a>
            				<button type="submit" class="btn btn-primary">
								登录
            				</button>
            			</footer>
            		</form>
            	
            	</div>
            
            	<h5 class="text-center"> - 第三方登录 -</h5>
            	<ul class="list-inline text-center">
            		<li>
					<a href="<?php echo $qq_login_url;?>" class="btn btn-primary btn-circle" title="使用QQ账号登录"><i class="fa fa-qq"></i></a>
            		</li>
            		<li>
					<a href="<?php echo $weibo_login_url;?>" class="btn btn-info btn-circle" title="使用微博账号登录"><i class="fa fa-weibo"></i></a>
            		</li>
            		<li>
            			<a href="/page/sms_login" class="btn btn-warning btn-circle" title="使用手机号登录"><i class="fa fa-mobile fa-lg"></i></a>
            		</li>
            	</ul>
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
        $("#login-form").validate({
            // Rules for form validation
            rules: {
                email: {
                    required: true
                },
                password: {
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
                password: {
                    required: '密码不能为空'
                }
            },

            // Do not change code below
            errorPlacement: function (error, element) {
                error.insertAfter(element.parent());
            }
        });

        $("#login-form").submit(function(e){
            var post = {
                "username" : $('#email').val(),
                "password" : $('#password').val(),
            };
            if (post.username== "" || post.passport== "") {
                showMessage("请输入电子邮件和密码");
                return false;
            }

            $.post("/page/login/?ret=json", post, function (res) {
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
