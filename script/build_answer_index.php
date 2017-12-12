<?php
require dirname(__DIR__).'/server/config.php';

Swoole::$php->db->debug = true;

$question_table = table('aws_question');
$question_table->primary = 'question_id';
$question_table->select = 'question_content';

$table = table('aws_answer');
$table->primary = 'answer_id';
$table->select = 'answer_id, question_id, answer_content, add_time';

$pages = $table->all();
echo "count=".count($pages)."\n";

$index = new App\Indexer('answer');
$index->beginRebuild();

foreach($pages as $v)
{
    $q = $question_table->get($v['question_id'])->get();
    if (empty($q))
    {
        echo "no question [{$v['question_id']}], skip.\n";
    }
    $data = array(
        'pid' => $v['answer_id'],
        'question_id' => $v['question_id'],
        'subject' => $q['question_content'],
        'message' => $v['answer_content'],
        'add_time' => $v['add_time'],
    );
    $ret = $index->add($data);
    echo "index #{$v['answer_id']} ok\n";
}

$index->endRebuild();
