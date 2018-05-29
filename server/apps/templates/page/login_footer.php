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
                username: {
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
                username: {
                    required: '用户名不能为空'
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

        <?php if (!empty($enable_digit_token)): ?>
        $('#sec_mobile_code').hide();
        <?php endif; ?>

        $("#login-form").submit(function(e){
            var post = {
                "mobile" : $('#mobile').val(),
                "sms_code" : $('#mobile_code').val(),
            };
            if (post.mobile == "" || post.sms_code == "") {
                showMessage("请输入手机号码和短信验证码");
                return false;
            }

            $.post("/page/sms_login/", post, function (res) {
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

    //发送验证码
    function sendMobileCode() {
        var mobile = $('#mobile').val();
        if (mobile != '')
        {
            $('#span_send_mobile_vcode').html("已发送短信验证码...");
            $.getJSON('/ajax/send_smscode_2/', {'mobile': mobile}, function (ret) {
                if (ret.code != 0) {
                    showMessage(ret.message);
                } else {
                    $('#sec_mobile_code').show();
                }
            });
        }
        else {
            showMessage("请输入用户名")
        }
    }
</script>
</body>
</html>
