<?php
require dirname(__DIR__).'/server/config.php';
require APPSPATH.'/classes/xunsearch/lib/XS.php';

Swoole::$php->db->debug = true;

$table = table('aws_question');
$table->primary = 'question_id';
$table->select = 'question_id, question_content, question_detail, update_time';

$pages = $table->all();
echo "count=".count($pages)."\n";

$xs = new XS(APPSPATH.'/configs/search/question.ini');

$index = $xs->index;
$index->beginRebuild();

foreach($pages as $v)
{
    $page = $table->get($v['question_id'])->get();
    $data = array(
        'pid' => $v['question_id'],
        'subject' => $v['question_content'],
        'message' => $v['question_detail'],
        'chrono' => $v['update_time'],
    );
    $doc = new XSDocument;
    $doc->setFields($data);
    $ret = $index->add($doc);
    echo "index #{$v['question_id']} ok\n";
}

$index->endRebuild();

