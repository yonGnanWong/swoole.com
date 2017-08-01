<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!-- Bootstrap core CSS -->
    <link href="/static/bootstrap3/dist/css/bootstrap.css" rel="stylesheet">
    <title>Swoole文档中心</title>
    <base target="_top" />
</head>
<body>
<div class="navbar-wrapper">
    <div class="container">
        <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <?php foreach($projects as $v): ?>
                        <li <?php if ($v['id'] == $project_id) { ?> class="active"<?php } ?>>
                        <a href="/wiki_admin/index/prid-<?=$v['id']?>"><?=$v['name']?></a></li>
                      <?php endforeach; ?>
                    </ul>
                    <div class="btn-group" style="margin-left: 600px;line-height: 50px;">

                        <a href="/wiki_admin/update_list/" class="small" target="main">
                            <span class="glyphicon glyphicon-list"></span> 更新列表</a>
                        <span style="color: #fff; margin-left: 10px;margin-right: 10px;">|</span>

                        <a href="/wiki_admin/comments/" class="small" target="main">
                            <span class="glyphicon glyphicon-list"></span> 评论管理</a>
                        <span style="color: #fff; margin-left: 10px;margin-right: 10px;">|</span>

                        <a href="/wiki_admin/setting/prid-<?=$project_id?>" class="small" target="main">
                            <span class="glyphicon glyphicon-th"></span> 项目设置</a>
                        <span style="color: #fff; margin-left: 10px;margin-right: 10px;">|</span>
                        <a href="/wiki_admin/create_project/" class="small" target="main">
                        <span class="glyphicon glyphicon-edit"></span> 创建项目</a>
                    </div>
                </div>
            </div>
    </div>
</div>
</body>
</html>
