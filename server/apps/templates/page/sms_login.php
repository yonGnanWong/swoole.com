<?php include __DIR__ . '/login_header.php'; ?>
<div class="well no-padding">
  <form action="" method="post" id="login-form" class="smart-form client-form" novalidate="novalidate">
    <header>
      手机号码登录
    </header>
      <fieldset>
          <section>
              <label class="label">手机号</label>
              <label class="input"> <i class="icon-append fa fa-user"></i>
                  <input type="text" name="mobile" id="mobile">
                  <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i>
                      请输入手机号码</b></label>
          </section>
          <section id="sec_mobile_code">
              <label class="label">手机验证码（需要登录后绑定手机号）</label>
              <label class="input"> <i class="icon-append fa fa-lock"></i>
                  <input type="input" name="mobile_code" id="mobile_code" placeholder="请输入收到的4位数字短信验证码"/>
                  <div class="note">
                      <span id="span_send_mobile_vcode"><a href="javascript: sendMobileCode();">发送手机验证码</a></span>
                  </div>
                  <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i>
                      请输入4位短信验证码</b>
              </label>
          </section>
      </fieldset>
    <footer>
      <button type="submit" class="btn btn-primary">
        登录
      </button>
    </footer>
  </form>
</div>
<?php include __DIR__ . '/login_footer.php'; ?>
