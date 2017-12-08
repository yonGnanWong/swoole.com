<?php
require dirname(__DIR__).'/server/config.php';

$table2 = table('wiki_tree');
$table1 = table('duoshuo_posts');

$list = $table1->all();
$list->filter('project_id = 0');

foreach($list as $li)
{
    $data = $li->get();
    $wiki_id = intval($data['thread_id']);
    if ($wiki_id )
    {
        $wiki = $table2->get($wiki_id);
        if ($wiki->exist())
        {
            $table1->set($li['id'], ['project_id' => $wiki['project_id']]);
            echo "wiki_id={$wiki_id}, project_id={$wiki['project_id']}\n";
        }
    }
}