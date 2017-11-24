<div id="opmenu">
    <a class="button btn-sm" href="/wiki_admin/create/?id=<?=$id?>&prid=<?=$project_id?>&parent=1">
        <span class="glyphicon glyphicon-file"></span>增加子页面</a>
    <?php if ($id!=0) { ?>
    <a class="button btn-sm" href="/wiki_admin/create/?id=<?=$id?>&prid=<?=$project_id?>">
        <span class="glyphicon glyphicon-file"></span>增加同级页面</a>
    <a class="button btn-sm btn-success" href="/wiki_admin/modify/?id=<?=$id?>">
        <span class="glyphicon glyphicon-edit"></span>
        编辑页面
    </a>
    <?php if ($id!=$project['home_id'] and $wiki_page['id']!=$project['home_id']) { ?>
    <a class="button btn-sm btn-warning" href="/wiki_admin/cut/?id=<?=$id?>">
        <span class="glyphicon glyphicon-log-out"></span> 剪切页面</a>
    <?php } ?>
    <?php if (isset($_COOKIE['wiki_cut_id']) and $_COOKIE['wiki_cut_id'] !=$id) { ?>
    <a class="button btn-sm btn-warning" href="/wiki_admin/paste/?child&id=<?=$id?>">
        <span class="glyphicon glyphicon-download-alt"></span>
        粘贴为子页面</a>
    <a class="button btn-sm btn-warning" href="/wiki_admin/paste/?id=<?=$id?>">
        <span class="glyphicon glyphicon-export"></span>
        粘贴为同级页面</a>
    <?php } ?>
        <?php if ($id!=$project['home_id'] and $wiki_page['id']!=$project['home_id']) { ?>
        <a class="button btn-sm btn-danger" onclick="return confirm('确定要删除此页面？');" href="/wiki_admin/delete/?id=<?=$id?>">
            <span class="glyphicon glyphicon-remove"></span>删除页面
        </a>
        <?php } ?>
    <?php } ?>
    <a class="button btn-sm btn-info" href="/wiki_admin/order/?id=<?=$id?>">
        <span class="glyphicon glyphicon-list"></span>
        子页排序
    </a>
    <a class="button btn-sm btn-primary" href="/wiki_admin/comments/?wiki_id=<?=$id?>">
        <span class="glyphicon glyphicon-comment"></span>
        管理评论
    </a>
</div>
