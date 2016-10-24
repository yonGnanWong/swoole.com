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
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/static/bootstrap3/dist/js/html5shiv.js"></script>
    <script src="/static/bootstrap3/dist/js/respond.min.js"></script>
    <![endif]-->
    <script src="/static/js/rainbow-custom.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/js/dtree.js"></script>
    <script src="/static/editor.md/editormd.min.js"></script>
    <title>新建页面_Swoole文档中心</title>
</head>
<body>
<div class="main_right" style="width: 96%;overflow-y: scroll;">
        <?php if (!empty($info)): ?>
        <div id="alert-info" class="alert alert-success"><?= $info ?></div>
        <script>
            //parent.window.frames['tree'].location.reload();
            setTimeout(function(){$('#alert-info').hide(500);}, 2000);
        </script>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <input type="input" name="title" style="width: 100%;" value="<?= $this->value($page, 'title') ?>"
                       class="form-control" placeholder="请输入标题">
            </div>
            <div class="form-group">
                <input type="input" name="link" style="width: 100%;" value="<?= $this->value($node, 'link') ?>"
                       class="form-control" placeholder="请输入页面文件名">
            </div>
            <div class="form-group" id="md_editor">
                <textarea id="content" name="content"
                          style="width: 100%; height: 640px;"><?= $this->value($page, 'content') ?></textarea>
            </div>
            <div class="form-group">
                <?php if ($use_editor){ ?>
                <a href="javascript: location.href += '&editor=0';"> 使用文本编辑器</a>
                <?php }else{ ?>
                <a href="javascript: location.href += '&editor=1';"> 使用MarkDown编辑器</a>
                <?php } ?>
            </div>
            <div class="form-group">
                <span>允许评论：</span>
                <?= $form['comment'] ?>
            </div>
            <div class="form-group">
                <span>是否公开：</span>
                <?= $form['publish'] ?>
            </div>
            <div class="form-group">
                <span>时间排序：</span>
                <?= $form['order_by_time'] ?>
            </div>
            <hr>
                <button type="submit" class="button btn-primary">提交</button>
                <button type="button" class="button" onclick="history.back()">取消</button>
        </form>
</div>
<?php if ($use_editor){ ?>
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
<?php } ?>
</body>
</html>
