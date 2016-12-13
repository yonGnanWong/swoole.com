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
    <div class="main_right">
        <div id="readme" class="blob instapaper_body">
            <article class="markdown-body entry-content" itemprop="mainContentOfPage">
                <?php if ($wiki_page) { ?>
                <h1><?=$wiki_page['title']?></h1>
            <?php }?>
                <?php include __DIR__."/admin_menu.php"; ?>
                <?=$content?>
            </article>
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
