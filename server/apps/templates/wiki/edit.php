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
    <link href="/static/editor.md/css/editormd.css" rel="stylesheet">
    <link rel="StyleSheet" href="/static/js/tree/dtree.css" type="text/css" />
    <link rel="StyleSheet" href="/static/css/doc.css" type="text/css" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap3/dist/js/html5shiv.js"></script>
    <script src="/static/bootstrap3/dist/js/respond.min.js"></script>
    <![endif]-->
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/js/dtree.js"></script>
    <script src="/static/editor.md/editormd.min.js"></script>
    <title>编辑页面_Swoole文档中心</title>
</head>
<body>
<div class="main" style="width: 96%; margin-top: 20px;">
        <?php if (!empty($info)){ ?>
        <div id="alert-info" class="alert alert-success"><?= $info ?> <a href="/wiki/page/<?=$page['id']?>.html">点击返回</a></div>
        <?php }else{ ?>
            <div class="bs-callout bs-callout-info" style="margin-top: 0" id="div_notice">
                <h4>感谢您向我们贡献文档！
                    <button type="button" class="close" onclick="$('#div_notice').hide(200);"><span
                            aria-hidden="true">×</span><span
                            class="sr-only">Close</span></button>
                </h4>
                <p style="line-height: 2; margin-top: 15px;">请遵守
                    <a href="/wiki/page/p-document_contribution.html" target="_blank">《Swoole社区文档编辑条例》</a>中约定的各项细则，编辑成功后系统会自动将您的名字加入贡献者名单。
                    <br/>请勿恶意编辑内容，否则根据社区编辑规则您的账户会被加入黑名单。</p>
            </div>
        <?php } ?>
        <form method="post">
            <div class="form-group">
                <input type="input" name="title" style="width: 100%;" value="<?= $this->value($page, 'title') ?>"
                       class="form-control" placeholder="请输入标题">
            </div>
            <div class="form-group" id="md_editor">
                <textarea id="content" name="content"
                          style="width: 100%; height: 640px;"><?= $this->value($page, 'content') ?></textarea>
            </div>
            <hr>
            <button type="submit" class="button btn-primary">提交编辑</button>
            <button type="button" class="button" onclick="location.href='/wiki/page/<?= $_GET['id'] ?>.html';">取消并返回
            </button>
        </form>
</div>
<script>
    var WikiEditor;
    $(function () {
        WikiEditor = editormd("md_editor", {
            width: "100%",
            height: 640,
            syncScrolling: "single",
            path: "/static/editor.md/lib/",
            imageUpload : true,
            imageFormats : ["jpg", "jpeg", "gif", "png", "bmp"],
            imageUploadURL : "/wiki_admin/upload/?id=<?=$this->value($page, 'id')?>"
        });
    });
</script>
</body>
</html>
