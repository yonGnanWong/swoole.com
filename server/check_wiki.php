<?php
require dirname(__DIR__).'/config.php';

$wikis = table('wiki_tree')->gets(array('project_id' => 1));
$table = table('wiki_content');

foreach($wikis as $v)
{
    $wiki = $table->get($v['id']);
    if ($wiki->exist())
    {
        continue;
    }
    $table->put(array('id' => $v['id']));
    echo "index #{$v['id']} ok\n";
}
