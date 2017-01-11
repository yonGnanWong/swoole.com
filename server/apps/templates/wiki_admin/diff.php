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
    <link rel="StyleSheet" href="/static/jsdifflib/diffview.css" type="text/css" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap3/dist/js/html5shiv.js"></script>
    <script src="/static/bootstrap3/dist/js/respond.min.js"></script>
    <![endif]-->
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.js"></script>
    <script src="/static/js/dtree.js"></script>
    <script src="/static/jsdifflib/difflib.js"></script>
    <script src="/static/jsdifflib/diffview.js"></script>
    <title>比较_Swoole文档中心</title>
</head>
<body>
    <div class="main_right">
        <style type="text/css">
            td {
                vertical-align: middle !important;
            }
        </style>
        <div id="text_a" style="display: none"><?= $a ?></div>
        <div id="text_b" style="display: none"><?= $b ?></div>
        <div id="readme" class="blob instapaper_body">
            <article class="markdown-body entry-content" itemprop="mainContentOfPage">
                <?php if ($wiki_page) { ?>
                    <h1><?=$wiki_page['title']?>
                        <a href="/wiki_admin/main/?id=<?= $wiki_page['id'] ?>"> <span
                                class="badge right">当前版本: <?= $wiki_page['version'] ?></span></a>
                    </h1>
                <?php }?>
                <script type="application/javascript">
                    $(document).ready(function(){
                        var base = difflib.stringAsLines($("#text_a").html());
                        var newtxt = difflib.stringAsLines($("#text_b").html());
                        var sm = new difflib.SequenceMatcher(base, newtxt);
                        var opcodes = sm.get_opcodes();
                        var html = diffview.buildView({
                            baseTextLines: base,
                            newTextLines: newtxt,
                            opcodes: opcodes,
                            // set the display titles for each resource
                            baseTextName: "version-<?=$_GET['version']?>",
                            newTextName: "version-<?=$version_b?>",
                            contextSize: null,
                            viewType: 0
                        });
                        $("#diffoutput").html(html);
                    });
                </script>
            </article>
            <div id="diffoutput"></div>
        </div>
    </div>
</body>
</html>
