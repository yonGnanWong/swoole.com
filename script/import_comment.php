<?php
require dirname(__DIR__).'/server/config.php';

$table1 = table('wiki_project');
$table2 = table('wiki_tree');
$table3 = table('wiki_comment');

$i = 0;

foreach($json['threads'] as $li)
{
    $u = parse_url($li['url']);
    if ($u['host'] != 'wiki.swoole.com')
    {
        continue;
    }
    //wiki/page/9.html
    if (preg_match('#^/wiki/page/(\d+).html$#i', $u['path'], $match))
    {
        $wiki_id = $match[1];
        $threads[$li['thread_id']] = $wiki_id;
    }
    elseif (preg_match('#^/wiki/index/prid-(\d+)$#i', $u['path'], $match))
    {
        $project_id = $match[1];
        goto get_home_id;
    }
    elseif (preg_match('#^/wiki/page/p-([a-z0-9_]+)\.html$#i', $u['path'], $match))
    {
        $name = $match[1];
        $n = $table2->get($name, 'link')->get();
        $threads[$li['thread_id']] = $n['id'];
    }
    elseif ($u['path']=='/wiki/main/')
    {
        parse_str($u['query'], $get);
        $project_id = $get['prid'];
        get_home_id:
        $p = $table1->get($project_id)->get();
        $threads[$li['thread_id']] = $p['home_id'];
    }
    elseif ($u['path']=='/')
    {
        $project_id = 1;
        goto get_home_id;
    }
    else
    {
        continue;
    }
}

foreach($json['posts'] as $li)
{
    if (!isset($threads[$li['thread_id']])) continue;
    $record ['wiki_id'] = $threads[$li['thread_id']];
    debug($li, $threads[$li['thread_id']]);
}