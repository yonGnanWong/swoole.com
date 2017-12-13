<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel='stylesheet' href='/static/css/markdown.css' type='text/css'/>
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
    <script src="/static/js/tocbot.min.js"></script>
    <link href="/static/css/tocbot.css" rel="stylesheet">
    <title><?= $wiki_page['title'] ?>-<?= $project['name'] ?>-Swoole文档中心</title>
    <meta name="description" content="Swoole, <?= $wiki_page['title'] ?>">
    <meta name="keywords" content="swoole, <?= $wiki_page['title'] ?>">
</head>
<body>

<div class="navbar-inverse navbar-fixed-top">
    <div class="navbar-collapse collapse container">
        <ul class="nav navbar-nav">
            <?php
            $no_create_child = false;
            foreach ($projects as $v): ?>
                <li <?php if ($v['id'] == $project_id) { ?>class="active"<?php } ?>>
                    <a href="/wiki/index/prid-<?= $v['id'] ?>"><?= $v['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
        <form class="navbar-form navbar-right" action="/wiki/search/" role="search" id="searchForm">
            <div class="form-inline form-group">
                <input type="text" class="form-control " placeholder="输入要搜索的关键词" name="q">
            </div>
            <div class="form-inline form-group">
                <button type="submit" class="btn btn-success">搜索</button>
            </div>
        </form>
    </div>
</div>

<div class="wiki_main">
    <div class="row row-offcanvas wiki_tree" id="sidebar">
        <div class="sidebar-offcanvas" role="navigation">
            <div class="sidebar-nav">
                <ul class="nav">
                    <?php foreach ($tree['child'] as $v): ?>
                    <li class="active" <?php if ($v['id'] == $_GET['id']){ ?>id="wiki_node_active"<?php } ?>>
                        <h3><a href="/wiki/page/<?php if ($v['link']) { ?>p-<?= $v['link'] ?><?php } else { ?><?= $v['id'] ?><?php } ?>.html">
                                <?= $v['text'] ?>
                            </a></h3>
                    </li>
                    <li>
                    <?php if ($v['child']){ ?>
                    <ul class="nav li2">
                        <?php foreach ($v['child'] as $v2): ?>
                        <li <?php if ($v2['id'] == $_GET['id']){ ?>id="wiki_node_active"<?php } ?>>
                        <a href="/wiki/page/<?php if ($v2['link']) { ?>p-<?= $v2['link'] ?><?php } else { ?><?= $v2['id'] ?><?php } ?>.html">
                            <?= $v2['text'] ?></a></li>
                        <li>
                            <?php if ($v2['child'])
                            { ?>
                            <ul class="nav li3">
                                <?php foreach ($v2['child'] as $v3): ?>
                                    <li <?php if ($v3['id'] == $_GET['id']){
                                        $no_create_child = true; ?>id="wiki_node_active"<?php } ?>>
                                <a href="/wiki/page/<?php if ($v3['link']) { ?>p-<?= $v3['link'] ?><?php } else { ?><?= $v3['id'] ?><?php } ?>.html">
                                    <?= $v3['text'] ?></a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php } ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div><!--/.well -->
        </div>
    </div>
    <div class="wiki_content blob instapaper_body">
        <div class="panel-heading" style="margin: 0;background-color: #f5f5f5; border-color: #ddd;">
            <div class="row text-right" style="padding-right: 10px;">
                <?php if (!$no_create_child){?>
                <a class="btn btn-primary" href="/wiki/edit/?id=<?=$wiki_page['id']?>&create=child">
                    <span class="glyphicon glyphicon-file"></span>增加子页面</a>
                <?php } ?>
                <?php if ($wiki_page['id'] != $project['home_id']){?>
                <a class="btn btn-info" href="/wiki/edit/?id=<?=$wiki_page['id']?>&create=brother">
                    <span class="glyphicon glyphicon-file"></span>增加同级页面</a>
                <?php } ?>

                <?php if (!$wiki_page['close_edit']){?>
                <a href="/wiki/edit/?id=<?=$wiki_page['id']?>" class="btn btn-success"><span
                        class="glyphicon glyphicon-edit"></span>
                    编辑本页
                </a>
                <?php } ?>

                <div class="btn-group text-left">
                    <button type="button" class="btn btn-default">更多...</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Markdown源码</a></li>
                        <li><a href="#">历史修改记录</a></li>
                        <li><a href="#">贡献者名单</a></li>
                        <!--<li role="separator" class="divider"></li>-->
                        <!--<li><a href="#">Separated link</a></li>-->
                    </ul>
                </div>
            </div>
        </div>
        <article class="markdown-body entry-content" itemprop="mainContentOfPage">
            <?php if ($wiki_page){ ?>
            <h1 id="h_title"><?= $wiki_page['title'] ?></h1>
            <?php } ?>
            <?=$content?>
        </article>
        <nav class="toc js-toc"></nav>
        <hr/>
        <?php if ($wiki_page['close_comment'] == 0){ ?>
        <div class="ds-thread" id="ds-thread">
            <div id="ds-reset">
                <div class="ds-comments-info">
                    <div class="ds-sort"><a class="ds-order-desc" target="_blank">最新</a><a class="ds-order-asc ds-current" target="_blank">最早</a><a class="ds-order-hot" target="_blank">最热</a></div>
                    <ul class="ds-comments-tabs">
                        <li class="ds-tab"><a class="ds-comments-tab-duoshuo ds-current" href="javascript:void(0);" target="_blank"><span class="ds-highlight"><?=count($comments)?></span>条评论</a>
                        </li>
                    </ul>
                </div>
                <ul class="ds-comments">
                    <?php foreach ($comments as $v): ?>
                    <li class="ds-post" id="comment-<?= $v['id'] ?>">
                        <div class="ds-post-self">
                            <div class="ds-avatar">
                                <a rel="nofollow author" target="_blank" href="<?= $v['author_url'] ?>"
                                   title="<?= $v['author_name'] ?>"><img
                                        src="<?php if ($v['avatar']) echo $v['avatar']; else echo '/static/images/default.png';?>"
                                        alt="<?= $v['author_name'] ?>"></a></div>
                            <div class="ds-comment-body">
                                <div class="ds-comment-header"><a class="ds-user-name ds-highlight" data-qqt-account=""
                                                                  href="<?= $v['author_url'] ?>"
                                                                  rel="nofollow" target="_blank" data-user-id="8435783"><?= $v['author_name'] ?></a>
                                </div>
                                <p><?= $v['message'] ?></p>

                                <div class="ds-comment-footer ds-comment-actions">
                                    <span class="ds-time"><?= substr($v['created_at'], 0, 10) ?></span>
                                    <a class="ds-post-delete" href="javascript:delComment(<?= $v['id'] ?>);"><span
                                            class="ds-icon ds-icon-delete"></span>删除</a>

                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <div class="ds-replybox" id="post_comment_div" style="display: none;">
                    <div class="ds-avatar"><img src="" id="login_user_avatar"></div>
                    <form method="post" onsubmit="return postComment(this);">
                        <input type="hidden" name="wiki_id" value="<?=$wiki_page['id']?>">
                        <div class="ds-textarea-wrapper ds-rounded-top">
                            <textarea name="message" title="Ctrl+Enter快捷提交" placeholder="说点什么吧（支持Markdown语法）…"></textarea>
                            <pre class="ds-hidden-text"></pre>
                        </div>
                        <div class="ds-post-toolbar">
                            <div class="ds-post-options ds-gradient-bg">
                            </div>
                            <button class="ds-post-button" type="submit">发布</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
        <link href="/static/css/duoshuo.css" rel="stylesheet">
        <?php } ?>
        <script type="text/javascript">
            function divAlign() {
                var a = $("#sidebar")[0];
                var b = $("div.wiki_content")[0];
                if (a.clientHeight < b.clientHeight) {
                    a.style.height = (b.clientHeight + 2) + "px";
                } else {
                    b.style.height = (a.clientHeight + 2) + "px";
                }
            }
            function delComment(id) {
                if (confirm("确定要删除此条评论")) {
                    $.post('/api/delComment/', {'id': id}, function (data) {
                        if (data.code == 0) {
                            $('#comment-'+id).remove();
                            divAlign();
                        }
                    });
                }
            }
            function postComment(o) {
                if ($.trim(o.message.value) == '') {
                    return false;
                }
                $.post('/api/postComment/', {
                    'content': o.message.value,
                    'app': 'wiki',
                    'id': o.wiki_id.value
                }, function (data) {
                    if (data.code == 0) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
                return false;
            }

            var _bdhmProtocol = (("https:" == document.location.protocol) ? "https://" : "http://");
            $(document).ready(function () {
                $('.entry-content h2').each(function(o, e){
                    $(e).attr('id', 'entry_h2_'+o);
                });
                $('.entry-content h3').each(function(o, e){
                    $(e).attr('id', 'entry_h3_'+o);
                });
                tocbot.init({
                    // Where to render the table of contents.
                    tocSelector: '.js-toc',
                    // Where to grab the headings to build the table of contents.
                    contentSelector: '.entry-content',
                    // Which headings to grab inside of the contentSelector element.
                    headingSelector: 'h2, h3'
                });
                tocbot.refresh();
                $('.js-toc').css("left", $('.entry-content').position().left + 870);
                var timer = setInterval(function () {
                    divAlign();
                    window.clearInterval(timer);
                }, 300);
                $('a').each(function(e){
                    //外链
                    if (this.href.substring(_bdhmProtocol.length, location.host.length + _bdhmProtocol.length) != location.host) {
                        this.target = "_blank";
                    }
                });
                $.getJSON('/api/getLoginInfo?prid=<?=$project_id?>', function (data) {
                    $('#post_comment_div').show();
                    if (data.code == 0) {
                        if (data.data.avatar.substring(0, 5) != 'https') {
                            if (data.data.avatar.substring(0, 4) == 'http') {
                                data.data.avatar = 'https' + data.data.avatar.substring(4);
                            } else {
                                data.data.avatar = 'https://' + location.host + data.data.avatar;
                            }
                            if (!data.data.admin) {
                                $('a.ds-post-delete').remove();
                            }
                        }
                        $('#login_user_avatar').attr('src', data.data.avatar).attr('alt', data.data.nickname).attr('title', data.data.nickname);
                    } else {
                        $('#post_comment_div').html('<p><br/><a href="http://www.swoole.com/page/login/">[登录后发表评论]</a></p>');
                        $('a.ds-post-delete').remove();
                    }
                });
            });
        </script>
    </div>
</div>
<script src="/static/js/jquery.min.js"></script>
<script src="/static/bootstrap3/dist/js/bootstrap.min.js"></script>
<div class="container footer" style="height: 80px; clear: both">
    <hr />
    <p>&copy; Swoole.com 2008 - <?=date('Y')?> 备案号：京ICP备14049466号-7 | <a href="https://wiki.swoole.com/wiki/page/p-copyright.html">版权声明</a> 官方QQ群：399424487 开发组邮件列表：
        <a href="mailto:team@swoole.com">team@swoole.com</a>
        当前Swoole扩展版本：<a href="https://github.com/swoole/swoole-src" target="_blank">swoole-<?=SWOOLE_VERSION?></a>
    </p>
    <div style="display: none">
        <script>
            var _hmt = _hmt || [];
            (function () {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?4967f2faa888a2e52742bebe7fcb5f7d";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
    </div>
</div>

</body>
</html>
