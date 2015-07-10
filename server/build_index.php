<?php
require dirname(__DIR__).'/config.php';
require APPSPATH.'/classes/xunsearch/lib/XS.php';

$wikis = table('wiki_tree')->gets(array('project_id' => 1));
$table = table('wiki_content');

echo "count=".count($wikis)."\n";

$xs = new XS(WEBPATH.'/search.ini');

$index = $xs->index; 
$index->beginRebuild();

foreach($wikis as $v)
{
    $wiki = $table->get($v['id'])->get();
    $data = array(
        'pid' => $v['id'],
        'subject' => $v['text'],
        'message' => $wiki['content'],
        'chrono' => time(),
    );

    $doc = new XSDocument;
    $doc->setFields($data);

    $ret = $index->add($doc);
    echo "index #{$v['id']} ok\n";
}

$index->endRebuild();

