<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?=$project['name']?>_开放文档系统</title>
</head>
<frameset rows="50,*" frameborder="no" border="0" framespacing="0">
    <frame src="/wiki_admin/top/?prid=<?=$project_id?>" name="top" scrolling="No" noresize="noresize" />
    <frameset cols="325,*" frameborder="no" border="0" framespacing="0">
        <frame src="/wiki_admin/tree/?prid=<?=$project_id?>" name="tree" noresize="noresize" scrolling="auto" />
        <frame src="/wiki_admin/main/?prid=<?=$project_id?><?php if ($p){?>&p=<?=$p?><?php } ?>" name="main" noresize="noresize" />
    </frameset>
</frameset>
<noframes>
    <body>

    </body>
</noframes>
</html>