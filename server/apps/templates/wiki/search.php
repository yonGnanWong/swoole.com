<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel='stylesheet' href='/static/css/markdown.css' type='text/css' />
    <!-- Bootstrap core CSS -->
    <link href="/static/bootstrap3/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="/static/bootstrap3/dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/static/css/wiki/noframe.css" rel="stylesheet">
    <link href="/static/css/code.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap3/dist/js/html5shiv.js"></script>
    <script src="/static/bootstrap3/dist/js/respond.min.js"></script>
    <![endif]-->
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/dtree.js"></script>
    <title><?=$_GET['q']?>-Swoole搜索</title>
    <style>
        #search_result h2 {
            font-size: 16px;
        }
        #search_result h2 a{
            font-size: 16px;
            text-decoration: underline;
        }
        #search_result div {
            font-size: 13px;
            line-height: 1.54;
        }
        #search_result {
            width: 640px;
            margin-top: 15px;
            padding-top: 15px;
        }
        #ResultStats {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="navbar-inverse navbar-fixed-top">
    <div class="navbar-collapse collapse container">
        <?php
        if (empty($_GET['type'])) {
            $type = 'wiki';
        }
        else {
            $type = $_GET['type'];
        }
        ?>
        <ul class="nav navbar-nav">
            <li <?php if ($type == 'wiki') { ?>class="active"<?php } ?>><a
                    href="/wiki/search/?q=<?= urlencode($_GET['q']) ?>"> 文档 </a></li>
            <li <?php if ($type == 'question') { ?>class="active"<?php } ?>><a
                    href="/wiki/search/?q=<?= urlencode($_GET['q']) ?>&type=question"> 问题 </a></li>
            <li <?php if ($type == 'answer') { ?>class="active"<?php } ?>><a
                    href="/wiki/search/?q=<?= urlencode($_GET['q']) ?>&type=answer"> 答案 </a></li>
        </ul>
        <form class="navbar-form navbar-left" action="/wiki/search/" role="search" id="searchForm">
            <div class="form-inline form-group">
                <input type="text" class="form-control" value="<?=$_GET['q']?>" name="q" style="width: 740px;">
                <input type="hidden" name="type" value="<?=$type?>" />
            </div>
            <div class="form-inline form-group">
                <button type="submit" class="btn btn-success">Swoole 搜索</button>
            </div>
        </form>
    </div>
</div>
<div class="wiki_main" style="width: 1130px;">
<div id="search_result">
    <div id="ResultStats">找到约 <?= $count ?> 条结果 （用时约 <?= $cost_time ?> 秒）</div>
    <?php foreach ($list as $li): ?>
        <h2><a href="<?= str_replace(['{id}', '{question_id}'], [$li['id'], empty($li['question_id'])?'':$li['question_id']], $link_tpl) ?>" target="_blank"><?= $li['title'] ?></a></h2>
        <div>
            <?= $li['desc'] ?>
        </div>
    <?php endforeach; ?>
</div>
<hr/>
<div class="pagination">
    <?= $pager ?>
</div>
    </div>
<div class="container footer" style="height: 80px; clear: both">
    <hr />
    <p>&copy; Swoole.com 2008-<?=date('Y')?> 备案号：京ICP备14049466号-7 官方QQ群：399424487 开发组邮件列表：
        <a href="mailto:team@swoole.com">team@swoole.com</a>
        当前Swoole扩展版本：<a href="https://github.com/swoole/swoole-src" target="_blank">swoole-<?=SWOOLE_VERSION?></a>
    </p>
    <div style="display: none">
        <script type="text/javascript">
            var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
            document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F4967f2faa888a2e52742bebe7fcb5f7d' type='text/javascript'%3E%3C/script%3E"));
        </script>
    </div>
</div>
</body>
</html>
