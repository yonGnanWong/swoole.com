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
    <link href="/static/css/theme.css" rel="stylesheet">
    <link href="/static/css/code.css" rel="stylesheet">
    <link rel="StyleSheet" href="/static/js/tree/dtree.css" type="text/css" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap3/dist/js/html5shiv.js"></script>
    <script src="/static/bootstrap3/dist/js/respond.min.js"></script>
    <![endif]-->
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/dtree.js"></script>
    <title><?=$wiki_page['title']?>_Swoole文档中心</title>
</head>
<body>
    <div class="main_right" style="width: 96%;">
        <style type="text/css">
            td {
                vertical-align: middle !important;
            }
        </style>
        <div id="readme" class="blob instapaper_body">
            <article class="markdown-body entry-content" itemprop="mainContentOfPage">
                <?php if ($wiki_page) { ?>
                <h1><?=$wiki_page['title']?>
                    <a href="/wiki_admin/main/?id=<?= $wiki_page['id'] ?>"> <span
                            class="badge right">当前版本: <?= $wiki_page['version'] ?></span></a>
                </h1>
            <?php }?>
            </article>
            <table class="table table-bordered table-striped" style="margin-top: 20px;">
                <thead>
                <tr>
                    <th style="width: 70px;">版本号</th>
                    <th>标题</th>
                    <th>修改人</th>
                    <th>修改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($list as $li): ?>
                <tr>
                    <td>
                        <span class="badge right">版本: <?=$li['version']?></span>
                    </td>
                    <td><a href="/wiki_admin/main/?id=<?= $_GET['id']?>&version=<?=$li['version']?>"><?= $li['title'] ?></a></td>
                    <td><a href="/page/user/uid-<?= $li['uid'] ?>"><?= $users[$li['uid']] ?></a></td>
                    <td><?=$li['addtime']?></td>
                    <td>
                        <a href="/wiki_admin/revert/?id=<?=$_GET['id']?>&version=<?=$li['version']?>" class="btn btn-sm btn-warning">回滚到此版本</a>
                        <a href="/wiki_admin/diff/?id=<?=$_GET['id']?>&version=<?=$li['version']?>&compare=current" class="btn btn-sm btn-info">与当前版本对比</a>
                        <?php if ($li['version'] > 0) {?>
                        <a href="/wiki_admin/diff/?id=<?=$_GET['id']?>&version=<?=$li['version']?>&compare=last" class="btn btn-sm btn-default">与上个版本对比</a>
                        <?php } ?>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <hr />
        <!-- Duoshuo Comment BEGIN -->
        <div class="ds-thread" data-thread-key="wiki-<?=$wiki_page['id']?>" data-title="<?=$wiki_page['title']?>"
             data-url="http://wiki.swoole.com/wiki/page/<?=$wiki_page['id']?>.html"></div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('a').each(function(e){
                    //外链
                    if(this.href.substring(7, location.host.length +7) != location.host) {
                        this.target = "_blank";
                    }
                });
            });
        </script>
        <!-- Duoshuo Comment END -->
    </div>
<div style="display: none">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F4967f2faa888a2e52742bebe7fcb5f7d' type='text/javascript'%3E%3C/script%3E"));
</script>
</div>
</body>
</html>
