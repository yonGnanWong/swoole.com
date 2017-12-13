<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>验证手机号码-<?=Swoole::$php->config['common']['site_name']?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__.'/../include/css.php'; ?>
</head>
<body class="">
<header style="background: #E4E4E4;color: #22201F" id="header">
    <?php include __DIR__ . '/../include/top_menu.php'; ?>
</header>
<aside id="left-panel">
    <?php include __DIR__ . '/../include/leftmenu.php'; ?>
    <span class="minifyme"> <i class="fa fa-arrow-circle-left hit"></i> </span>
</aside>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

    <!-- RIBBON -->
    <div id="ribbon">

    <span class="ribbon-button-alignment">
        <span id="refresh" class="btn btn-ribbon" data-title="refresh" rel="tooltip"
              data-placement="bottom"
              data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings."
              data-html="true"><i class="fa fa-refresh"></i></span> </span>

        <!-- breadcrumb -->
        <ol class="breadcrumb">
            <li>Home</li>
            <li>Dashboard</li>
        </ol>

    </div>

    <div id="content">
        <div class="row">
            <article class="col-sm-12 sortable-grid ui-sortable">
                <div class="jarviswidget jarviswidget-sortable" id="wid-id-0" data-widget-togglebutton="false"
                     data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-colorbutton="false"
                     data-widget-deletebutton="false" role="widget">
                    <header role="heading">
                        <ul class="nav nav-tabs pull-left in">
                            <li class="active">
                                <a><i class="fa fa-clock-o"></i>
                                    <span class="hidden-mobile hidden-tablet">验证手机号</span>
                                </a>
                            </li>
                        </ul>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>

                    <!-- widget div-->
                    <div class="no-padding" role="content">
                        <div class="widget-body">
                            <form class="smart-form" method="post" enctype="application/x-www-form-urlencoded">
                                <?php include dirname(__DIR__) . '/include/msg.php'; ?>
                                <fieldset>
                                    <section>
                                        <label class="label">[必填] 手机号码</label>
                                        <label class="input">
                                            <input type="text" class="input" name="mobile" value="<?=$this->value($_user, 'mobile')?>" />
                                        </label>
                                    </section>
                                    <section>
                                        <label class="label">[必填] 图形验证码</label>
                                        <label class="input col no-padding" id="label_input_vcode" style="width: 230px;">
                                            <input type="text" class="input" value="" name="vcode" id="input_vcode" />
                                            <span class="note note-error" style="display: none" id="note_input_vcode">错误的验证码</span>
                                        </label>
                                        <label class="input col col-3"> <img src="/page/verify/" id="img_vcode">
                                        </label>
                                        <label class="input col col-2 no-padding">
                                            <input type="button" id="btn_send_smscode" style="width: 100px;" disabled="disabled" class="btn btn-default" value="发送验证码" />
                                        </label>
                                    </section>
                                </fieldset>
                                <fieldset>
                                    <section>
                                        <label class="label">[必填] 短信验证码</label>
                                        <label class="input">
                                            <input type="text" class="input" name="smscode"/>
                                        </label>
                                    </section>
                                </fieldset>
                                <footer>
                                    <button type="submit" class="btn btn-primary">
                                        提交
                                    </button>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
    <?php include dirname(__DIR__) . '/include/javascript.php'; ?>
    <script>
        pageSetUp();

        function setBtnTimeout(_btn, _time)
        {
            var countdown = _time;
            _btn.setAttribute("disabled", true);
            _btn.value = "重新发送(" + countdown + ")";
            countdown--;
            var timer = setInterval(function () {
                if (countdown == 0) {
                    clearInterval(timer);
                    _btn.removeAttribute("disabled");
                    _btn.value = "发送验证码";
                    countdown = 60;
                } else {
                    _btn.setAttribute("disabled", true);
                    _btn.value = "重新发送(" + countdown + ")";
                    countdown--;
                }
            }, 1000);
        }

        $(document).ready(function () {

            $('#img_vcode').click(function (e) {
                $(e.currentTarget).attr('src', '/page/verify/?t=' + Math.random());
            });

            $('#input_vcode').keyup(function (e) {
                var v =  $('#input_vcode').val();
                if (v.length == 4) {
                    $.getJSON('/ajax/check_vcode/?vcode=' + v, function (ret) {
                        if (ret.data == false) {
                            $('#label_input_vcode').addClass('state-error').removeClass('state-success');
                            $('#note_input_vcode').show();
                        } else {
                            $('#label_input_vcode').addClass('state-success').removeClass('state-error');
                            $('#note_input_vcode').hide();
                            $('#btn_send_smscode').removeAttr('disabled');
                        }
                    });
                }
            });

            $('#btn_send_smscode').click(function (e) {
                var v = $('#input_vcode').val();
                var _btn = e.currentTarget;
                setBtnTimeout(_btn, 60);
                $.getJSON('/ajax/send_smscode/?vcode=' + v + '&mobile=' + $('input[name=mobile]').val(), function (ret) {
                    console.dir(ret);
                });
            });
        });
    </script>
</body>
</html>
