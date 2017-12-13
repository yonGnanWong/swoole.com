<div id="logo-group">
    <span id="logo" style="margin-left: 10px; width: 240px;">
        <a href="/">
            <strong
                style="font-size: 18px;"><?= Swoole::$php->config['common']['site_name'] ?></strong></a></span>
</div>

<div class="pull-right" style="padding: 15px;">
            <span style="font-weight: bolder">
        <span style="text-transform: none;">
                        (<?= $_SESSION['user']['nickname'] ?>)
        </span>
        <span style="text-transform: none;padding: 15px 5px;">
                    <a style="text-decoration: none;font-weight: bolder" href="/page/logout/">退出</a>
        </span>
    </span>
</div>